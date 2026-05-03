<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment Result</title>
    <style>
        body {
            text-align: center;
            margin-top: 60px;
            font-family: Arial, sans-serif;
        }

        .success {
            color: green;
        }

        .fail {
            color: red;
        }

        .info-box {
            margin-top: 20px;
            display: inline-block;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        .info-box p {
            margin: 10px 0;
            font-size: 18px;
        }
    </style>
</head>

<body>

    @if($success ?? false)
    <h2 class="success">✅ Payment Completed Successfully</h2>

    <div class="info-box">
        @if(isset($order_id))
        <p><strong>Order Number:</strong> #{{ $order_id }}</p>
        @endif

        @if(isset($transaction_id))
        <p><strong>Transaction ID:</strong> {{ $transaction_id }}</p>
        @endif

        <p>Thank you for your purchase! Your order is being processed.</p>
    </div>
    @else
    <h2 class="fail">❌ Payment Failed</h2>
    <div class="info-box">
        <p>There was a problem processing your payment.</p>
        <p>Please try again later or contact support if the issue persists.</p>
    </div>
    @endif

</body>

</html>