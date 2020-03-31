<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pay Bolivia</title>
</head>
<body>
    <input type="text" name="authResult" value="{{ $authResult }}">
    <input type="text" name="amount" value="{{ $amount }}">
    <input type="text" name="currency" value="{{ $currency }}">
    <input type="text" name="dateServer" value="{{ $dateServer }}">
    <input type="text" name="orderId" value="{{ $orderId }}">
</body>
</html>