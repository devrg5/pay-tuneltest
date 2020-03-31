<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pay Bolivia</title>
</head>
<body>
<form id="pay-form" name="pay-form" action="https://integration.pay.com.bo/transaction/response" method="POST">
        <input type="text" name="purchaseAmount" value="{{ $purchaseAmount }}" hidden>
        <input type="text" name="purchaseCurrencyCode" value="{{ $purchaseCurrencyCode }}" hidden>
        <input type="text" name="txDateTime" value="{{ $txDateTime }}" hidden>
        <input type="text" name="reserved2" value="{{ $reserved2 }}" hidden>
        <input type="text" name="purchaseOperationNumber" value="{{ $purchaseOperationNumber }}" hidden>
        <input type="text" name="authorizationCode" value="{{ $authorizationCode }}" hidden>
        <input type="text" name="authorizationResult" value="{{ $authorizationResult }}" hidden>
        <input type="text" name="errorCode" value="{{ $errorCode }}" hidden>
        <input type="text" name="errorMessage" value="{{ $errorMessage }}" hidden>
        <input type="text" name="reserved1" value="{{ $reserved1 }}" hidden>
        <input type="text" name="reserved3" value="{{ $reserved3 }}" hidden>
        <input type="text" name="reserved4" value="{{ $reserved4 }}" hidden>
        <input type="text" name="purchaseVerification" value="{{ $purchaseVerification }}" hidden>
    </form>
    <script type="text/javascript">
        window.onload = function() {
            document.forms['pay-form'].submit();
        };
    </script>
</body>
</html>