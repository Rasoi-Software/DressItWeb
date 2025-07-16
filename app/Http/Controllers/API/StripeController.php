<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Payment;
use Stripe\Webhook;
use Stripe\Stripe;

class StripeController extends Controller
{
    protected $stripe;

    public function __construct(StripeService $stripe)
    {
        $this->stripe = $stripe;
    }

    public function createCustomer(Request $request)
    {
        try {
            $user = User::findOrFail(auth()->id());

            // If Stripe customer ID already exists, return it without creating a new one
            if ($user->stripe_customer_id) {
                return returnSuccess('Stripe Customer ID', [
                    'id' => $user->stripe_customer_id,
                ]);
            }

            // Create Stripe Customer
            $customer = $this->stripe->createCustomer($user->name, $user->email);

            // Store Stripe customer ID in the database
            $user->stripe_customer_id = $customer->id;
            $user->save();

            return returnSuccess('Customer created successfully', $customer);
        } catch (\Exception $e) {
            return returnError($e);
        }
    }
    public function getPaymentMethod(Request $request)
    {
        try {
            $user = User::findOrFail(auth()->id());

            if ($user->stripe_pm_id) {
                return returnSuccess('Payment method already exists', [
                    'id' => $user->stripe_pm_id,
                ]);
            }




            return returnSuccess('Payment method created successfully', $user);
        } catch (\Exception $e) {
            return returnError($e);
        }
    }

    public function savePaymentMethod(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|string',
            'payment_method_id' => 'required|string',
        ]);

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            // Attach to customer
            $paymentMethod = \Stripe\PaymentMethod::retrieve($request->payment_method_id);
            $paymentMethod->attach([
                'customer' => $request->customer_id,
            ]);

            // Optionally save to DB
            $user = User::where('stripe_customer_id', $request->customer_id)->first();
            $user->stripe_pm_id = $paymentMethod->id;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Payment method saved successfully!',
                'data' => $paymentMethod,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }


    public function addPaymentMethod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|string',
            'payment_method_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }

        try {
            $pm = $this->stripe->attachPaymentMethod(
                $request->customer_id,
                $request->payment_method_id
            );
            return returnSuccess('Payment method attached successfully', $pm);
        } catch (\Exception $e) {
            return returnError($e);
        }
    }

    public function chargeCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }

        try {
            $user = User::findOrFail(auth()->id());

            // Create payment intent using your Stripe service
            $intent = $this->stripe->createPaymentIntent(
                $request->amount,
                'USD',
                $user->stripe_customer_id,
                $user->stripe_pm_id
            );

            // Store in DB
            Payment::create([
                'user_id'           => $user->id,
                'payment_intent_id' => $intent->id,
                'payment_method_id' => $intent->payment_method,
                'amount'            => ($intent->amount / 100),
                'currency'          => $intent->currency,
                'status'            => $intent->status,
                'description'       => $intent->description ?? 'Payment via Stripe',
                'response'          => json_encode($intent),
            ]);

            return returnSuccess('Payment successful', $intent);
        } catch (\Exception $e) {
            return returnError($e);
        }
    }


    public function createConnectedAccount(Request $request)
    {
        try {
            $account = $this->stripe->createConnectedAccount();
            return returnSuccess('Connected account created successfully', $account);
        } catch (\Exception $e) {
            return returnError($e);
        }
    }

    public function transferToConnected(Request $request)
    {
        $validated = $request->validate([
            'amount'                => 'required|integer|min:1',
            'currency'              => 'required|string|size:3',
            'destination_account_id' => 'required|string',
        ]);

        try {
            $transfer = $this->stripe->transferToConnectedAccount(
                $validated['amount'],
                $validated['currency'],
                $validated['destination_account_id']
            );
            return returnSuccess('Transfer completed', $transfer);
        } catch (\Exception $e) {
            return returnError($e);
        }
    }


    public function handleWebhook(Request $request)
    {
        // Your Stripe secret webhook signing key
        $endpoint_secret = config('services.stripe.webhook_secret');

        $payload = $request->getContent();
        $sig_header = $request->server('HTTP_STRIPE_SIGNATURE');

        try {
            // Verify webhook signature
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response('Invalid signature', 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $intent = $event->data->object;

                // Optionally, update your DB based on PaymentIntent ID
                Payment::where('payment_intent_id', $intent->id)
                    ->update(['status' => 'succeeded']);

                Log::info('PaymentIntent succeeded: ' . $intent->id);
                break;

            case 'payment_method.attached':
                $paymentMethod = $event->data->object;
                Log::info('Payment method attached: ' . $paymentMethod->id);
                break;

            // Add more cases as needed

            default:
                Log::info("Unhandled event type: {$event->type}");
        }

        return response('Webhook handled', 200);
    }
}
