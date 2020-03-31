<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pay Bolivia</title>
</head>
<body>
    <form id="pay-form" name="pay-form" action="{{ $clientResponseUrl }}" method="POST">
        <input type="text" name="authResult" value="{{ $authResult }}" hidden>
        <input type="text" name="amount" value="{{ $amount }}" hidden>
        <input type="text" name="currency" value="{{ $currency }}" hidden>
        <input type="text" name="dateServer" value="{{ $dateServer }}" hidden>
        <input type="text" name="orderId" value="{{ $reserved2 }}" hidden>
        <input type="text" name="verificationNumber" value="{{ $verificationNumber }}" hidden>
    </form>
    <script type="text/javascript">
        window.onload = function() {
            document.forms['pay-form'].submit();
        };
    </script>
</body>
</html>
