<!DOCTYPE html>
<html>

<head>
    <title>Stripe Card Form</title>
    <script src="https://js.stripe.com/v3/"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <h2>Enter Card Details</h2>

    <form id="payment-form">
        <input id="stripe_customer_id" value="" name="stripe_customer_id" />
        <div id="card-element" style="margin-bottom: 20px;"></div>
        <button id="submit">Save Card</button>
    </form>

    <div id="result"></div>

   <script>
    const stripe = Stripe("{{ env('STRIPE_PUBLIC') }}");
    const elements = stripe.elements();
    const card = elements.create('card');
    card.mount('#card-element');

    const form = document.getElementById('payment-form');
    const resultDiv = document.getElementById('result');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const stripe_customer_id = document.getElementById('stripe_customer_id').value;

        if (!stripe_customer_id) {
            alert('Please enter Stripe customer ID');
            return; // or return false;
        }

        const { paymentMethod, error } = await stripe.createPaymentMethod({
            type: 'card',
            card: card,
        });

        if (error) {
            resultDiv.innerText = error.message;
        } else {
            // Send to backend
            fetch("{{ route('save.payment.method') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    payment_method_id: paymentMethod.id,
                    customer_id: stripe_customer_id
                })
            })
            .then(res => res.json())
            .then(data => {
                resultDiv.innerText = data.message || "Success";
            });
        }
    });
</script>

</body>

</html>