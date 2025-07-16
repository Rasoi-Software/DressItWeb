<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\PaymentIntent;
use Stripe\Account;
use Stripe\Transfer;
use Stripe\Token;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCustomer($name, $email)
    {
        try {
            $customer = Customer::create([
                'name'  => $name,
                'email' => $email,
            ]);
            return $customer;
        } catch (\Exception $e) {
            return returnError($e);
        }
    }

    public function createTestPaymentMethod()
    {
        try {
            $token = Token::create([
                'card' => [
                    'number'    => '4242424242424242',
                    'exp_month' => 12,
                    'exp_year'  => 2030,
                    'cvc'       => '123',
                ],
            ]);

            return returnSuccess('Test payment method token created', $token);
        } catch (\Exception $e) {
            return returnError($e);
        }
    }

    public function attachPaymentMethod($customerId, $paymentMethodId)
    {
        try {
            $pm = PaymentMethod::retrieve($paymentMethodId);
            $pm->attach(['customer' => $customerId]);

            return returnSuccess('Payment method attached', $pm);
        } catch (\Exception $e) {
            return returnError($e);
        }
    }

    public function createPaymentIntent($amount, $currency, $customerId, $paymentMethodId)
    {
        try {
            $intent = PaymentIntent::create([
                'amount' => ($amount * 100), // convert to paisa if INR
                'currency' => $currency,
                'customer' => $customerId,
                'payment_method' => $paymentMethodId,
                'off_session' => true,
                'confirm' => true,
                'description' => 'Charge for product/service', // Indian export law requires this
            ]);

            return $intent;
        } catch (\Exception $e) {
            return returnError($e);
        }
    }

    public function createConnectedAccount()
    {
        try {
            $account = Account::create([
                'type' => 'custom',
                'country' => 'US',
                'email' => 'recipient@example.com',
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
            ]);

            return returnSuccess('Connected account created', $account);
        } catch (\Exception $e) {
            return returnError($e);
        }
    }

    public function transferToConnectedAccount($amount, $currency, $destinationAccountId)
    {
        try {
            $transfer = Transfer::create([
                'amount' => $amount,
                'currency' => $currency,
                'destination' => $destinationAccountId,
                'description' => 'Payout for services rendered',
            ]);

            return returnSuccess('Transfer to connected account successful', $transfer);
        } catch (\Exception $e) {
            return returnError($e);
        }
    }
}
