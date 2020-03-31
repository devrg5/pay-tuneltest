<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Transaction;
use \App\Client;
use \App\Cardholder;
use SoapClient;
use Validator;
Use Exception;

class TransactionTigoController extends Controller
{
    public function create()
    {
        return view('tigo.form', []);
    }

    public function store(Request $request)
    {
        //validation
        $validator = Validator::make($request->all(), [
            'inputName' => 'required|string|max:30',
            'inputLastName' => 'required|string|max:50',
            'inputAmount' => 'required|integer|min:0',
            'inputEmail' => 'required|email',
            'inputDescription' => 'required|string|max:255',
            'inputAddress' => 'required|string|max:255',
            'inputZip' => 'required|max:50',
            'inputCity' => 'required|string|max:100',
            'inputState' => 'required|string|max:100',
            'inputCountry' => 'required|string|max:100',
            'orderId' => 'required|string',
            'inputCurrency' => 'required|in:068,840',
            'inputLang' => 'required|in:sp,en,SP,EN',
            'verificationNumber' => 'required|string',
        ]);
        //get client from DB clients table
        $client = $this->getClient($request->inputClientId);
        if ($validator->fails()) {
            return redirect('client/redirect')->with('clientUrl', $client->url_base)->with('message', 'Datos inconsistentes. Sera redirigido al comercio.');
        }
        //get variables from the post request
        $shippingFirstName = $request->inputName;
        $shippingLastName = $request->inputLastName;
        $purchaseAmount = $request->inputAmount;
        $cardHolderEmail = $request->inputEmail;
        $descriptionProducts = $request->inputDescription;
        $clientId = $request->inputClientId;
        $cardHolderAddress = $request->inputAddress;
        $cardHolderZip = $request->inputZip;
        $cardHolderCity = $request->inputCity;
        $cardHolderState = $request->inputState;
        $cardHolderCountry = $request->inputCountry;
        $orderId = $request->orderId;
        $purchaseCurrencyCode = $request->inputCurrency;
        $language = $request->inputLang;
        $phone = $request->inputPhone;
        $verification = $request->verificationNumber;

        //set english if requested
        if ($language == 'en' || $language == 'EN') {
            App::setLocale('en');
        }

        //create the verification key
        $verificationLaravel = openssl_digest($purchaseAmount . $clientId . $orderId . $purchaseCurrencyCode . $client->token, 'sha512');

        //check verificationNumber from the commerce and the app
        if($verification == $verificationLaravel){

            //get variable from DB transactions table
            $purchaseOperationNumber = str_pad($this->getTransactionId(), 9, "0", STR_PAD_LEFT);

            //get or store the cardholder in the DB
            $cardHolder = $this->getCardHolder($cardHolderEmail, $shippingFirstName, $shippingLastName);

            //store transaction
            $transaction = $this->storeTransaction($client->user_id, $cardHolder->email, $purchaseOperationNumber, $verificationLaravel, $purchaseAmount, $purchaseCurrencyCode, $language, $descriptionProducts, $cardHolderAddress, $cardHolderZip, $cardHolderCity, $cardHolderState, $cardHolderCountry, $phone);

            $verificationForClient = openssl_digest($clientId . $orderId . $transaction->id . $client->token, 'sha512');
            $verificationCheck = openssl_digest($clientId . $transaction->id . $client->token, 'sha512');

            return view('tigo.checkout', ['shippingFirstName' => $shippingFirstName,
                'shippingLastName' => $shippingLastName,
                'purchaseAmount' => $purchaseAmount,
                'purchaseOperationNumber' => $purchaseOperationNumber,
                'purchaseCurrencyCode' => $purchaseCurrencyCode,
                'language' => $language,
                'shippingEmail' => $cardHolderEmail,
                'shippingAddress' => $cardHolderAddress,
                'shippingZIP' => $cardHolderZip,
                'shippingCity' => $cardHolderCity,
                'shippingState' => $cardHolderState,
                'shippingCountry' => $cardHolderCountry,
                'descriptionProducts' => $descriptionProducts,
                'reserved1' => $clientId,
                'reserved2' => $orderId,
                'reserved3' => $transaction->id,
                'purchaseVerification' => $verificationForClient,
                'verificationCheck' => $verificationCheck,
                'clientImage' => $client->user->image]);

        }else{

            return abort(403, 'Los datos fueron modificados! Vuelva a realizar la compra.');

        }
    }


    function payTransaction(Request $request)
    {
        //get variables
        $phone = $request->phone;
        $clientId = $request->reserved1;
        $orderId = $request->reserved2;
        $transactionId = $request->reserved3;
        $verification = $request->verification;

        //get client from DB clients table
        $client = $this->getClient($clientId);

        //create the verification key
        $verificationLaravel = openssl_digest($clientId . $orderId . $transactionId . $client->token, 'sha512');

        if($verification != $verificationLaravel){

            return response()->json([
                'mensaje' => 'Los datos fueron modificados, vuelva a intentarlo.'
            ]);

        }

        //get transaction
        $transactionToModify = Transaction::where('id', $transactionId)->first();

        //set the transaction
        $arrayWithAnswer = $this->payWithTigo($phone, ($transactionToModify->purchase_amount/100), $transactionToModify->purchase_operation_number, $client->user->name);

        $codRespuesta = $arrayWithAnswer[0];
        $mensajeRespuesta = $arrayWithAnswer[1];
        $orderIdRespuesta = $arrayWithAnswer[2];

        //save answer from tigo
        $transactionToModify->phone = $phone;
        $transactionToModify->authorization_result = $codRespuesta;
        $transactionToModify->error_message = $mensajeRespuesta;

        $transactionToModify->save();

        //return JSON
        return response()->json([
            'mensaje' => $mensajeRespuesta,
            'order' => $orderIdRespuesta
        ]);
    }


    function checkTransaction(Request $request)
    {
        //get variables
        $operatioNumber = $request->purchaseOperationNumber;
        $clientId = $request->reserved1;
        $transactionId = $request->reserved3;
        $verification = $request->verificationCheck;

        //get client from DB clients table
        $client = $this->getClient($clientId);

        $verificationLaravel = openssl_digest($clientId . $transactionId . $client->token, 'sha512');

        if($verificationLaravel != $verification){
            return response()->json([
                'mensaje' => 'Los datos fueron modificados, vuelva a intentarlo.'
            ]);
        }

        //get variables from .env file
        $serverKey = env('serverKey');
        $serverUrl = env('serverUrl');
        $encryptionKey = env('encryptionKey');

        //encrypt numberID
        $encryptedString = $this->encryptString($operatioNumber, $encryptionKey);

        $arrContextOptions=Array("ssl"=>array( "verify_peer"=>false, "verify_peer_name"=>false,'crypto_method' => STREAM_CRYPTO_METHOD_TLS_CLIENT));
        $options = Array(
                        'soap_version'=>'SOAP_1_2',
                        'exceptions'=>true,
                        'trace'=>1,
                        'cache_wsdl'=>WSDL_CACHE_NONE,
                        'stream_context' => stream_context_create($arrContextOptions),
                        'user_agent' => 'PHPSoapClient',
                        'Content-Type' => 'application/soap+xml'
            );
        $params = Array(
            'key' => $serverKey,
            'parametros' => $encryptedString
        );

        $soapClient = new SoapClient($serverUrl, $options);
        $result = $soapClient->consultarEstado($params);

        //decrypt
        $unEncryptedString = $this->unencryptString($result->return, $encryptionKey);

        //parse decrypted string
        $arrayWithAnswer = $this->parserForTransactionStatus($unEncryptedString);

        $codRespuesta = $arrayWithAnswer[0];
        $mensajeRespuesta = 'Transaccion exitosa';
        $thirdParam = end($arrayWithAnswer);;

        //get transaction
        $transactionToModify = Transaction::where('id', $transactionId)->first();
        //save answer from tigo
        $transactionToModify->authorization_code = $thirdParam;
        $transactionToModify->authorization_result = '00';
        $transactionToModify->error_message = $mensajeRespuesta;
        $transactionToModify->error_code = '00';

        if($codRespuesta != '0'){
            $transactionToModify->error_message = 'Transaccion fallida';
            $transactionToModify->authorization_code = '';
            $transactionToModify->authorization_result = '01';
            $transactionToModify->error_code = $thirdParam;
        }

        $transactionToModify->save();

        $verification = openssl_digest($transactionToModify->authorization_result . ($transactionToModify->purchase_amount/100) . $transactionToModify->updated_at . $client->token, 'sha512');

        //must return JSON
        return response()->json([
            'authResult' => $transactionToModify->authorization_result,
            'dateServer' => $transactionToModify->updated_at,
            'verification' => $verification
        ]);
    }


    protected function getClient($clientId)
    {
        return Client::where('client_identifier', $clientId)->firstOrFail();
    }


    protected function getTransactionId()
    {
        if (Transaction::latest()->first()) {
            $lastId = Transaction::latest('id')->first()->purchase_operation_number+1;
        } else {
            $lastId = 1;
        }
        return $lastId;
    }


    protected function getCardHolder($cardHolderEmail, $shippingFirstName, $shippingLastName)
    {
        return Cardholder::firstOrCreate(
            ['email' => $cardHolderEmail],
            ['user_commerce' => 'nova'.(Cardholder::all()->count()+1), 'name' => $shippingFirstName, 'last_name' => $shippingLastName]
        );
    }


    protected function storeTransaction($client_id, $cardHolder_id, $purchaseOperationNumber, $verificationLaravel, $purchaseAmount, $purchaseCurrencyCode, $language, $descriptionProducts, $cardHolderAddress, $cardHolderZip, $cardHolderCity, $cardHolderState, $cardHolderCountry, $phone)
    {
        try
        {
            return Transaction::create(['client_id' => $client_id, 'cardholder_id'=> $cardHolder_id, 'purchase_operation_number'=> $purchaseOperationNumber, 'purchase_verification' => $verificationLaravel, 'purchase_amount' => $purchaseAmount, 'currency_code' => $purchaseCurrencyCode, 'language' => $language,'description_products' => $descriptionProducts, 'cardholder_address' => $cardHolderAddress, 'cardholder_zip' => $cardHolderZip, 'cardholder_city' => $cardHolderCity, 'cardholder_state' => $cardHolderState, 'cardholder_country' => $cardHolderCountry, 'phone' => $phone]);
        }
        catch(Exception $e)
        {
            $purchaseOperationNumber = str_pad($this->getTransactionId(), 9, "0", STR_PAD_LEFT);
            $this->storeTransaction($client_id, $cardHolder_id, $purchaseOperationNumber, $verificationLaravel, $purchaseAmount, $purchaseCurrencyCode, $language, $descriptionProducts, $cardHolderAddress, $cardHolderZip, $cardHolderCity, $cardHolderState, $cardHolderCountry, $phone);
        }
    }


    protected function soapTigo($encryptedString, $serverKey, $serverUrl)
    {
        $arrContextOptions=Array("ssl"=>array( "verify_peer"=>false, "verify_peer_name"=>false,'crypto_method' => STREAM_CRYPTO_METHOD_TLS_CLIENT));
        $options = Array(
                        'soap_version'=>'SOAP_1_2',
                        'exceptions'=>true,
                        'trace'=>1,
                        'cache_wsdl'=>WSDL_CACHE_NONE,
                        'stream_context' => stream_context_create($arrContextOptions),
                        'user_agent' => 'PHPSoapClient',
                        'Content-Type' => 'application/soap+xml'
            );
        $params = Array(
            'key' => $serverKey,
            'parametros' => $encryptedString
        );

        $soapClient = new SoapClient($serverUrl, $options);
        $result = $soapClient->solicitarPagoAsincrono($params);
        return $result;
    }


    protected function encryptString($cadenaplana, $llave) {
        $message_padded = $cadenaplana;
        if (strlen($message_padded) % 8) {
            $message_padded = str_pad($message_padded, strlen($message_padded) + 8 - strlen($message_padded) % 8, "\0");
        }

        $encrypted_openssl = openssl_encrypt($message_padded, "des-ede3", $llave, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING);
        $encriptado = base64_encode($encrypted_openssl);
        return $encriptado;
    }


    function unencryptString($cadenacifrada,$llave){
        $cadena_dec = base64_decode($cadenacifrada);
        $message_padded = $cadena_dec;

        if (strlen($message_padded) % 8) {
            $message_padded = str_pad($message_padded, strlen($message_padded) + 8 - strlen($message_padded) % 8, "\0");
        }

        $cadenadecifrada = openssl_decrypt($message_padded, "des-ede3", $llave, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING);
        return $cadenadecifrada;
    }


    function parser($cadenares){
        $array_data = explode("&", $cadenares);
        foreach($array_data as $array_dat){
            $data_list[] = strstr($array_dat, '=');
        }
        foreach($data_list as $key=>$value){
            $arrayres[$key] = str_replace("=", "", $value);
        }

        return $arrayres;
    }

    function parserForTransactionStatus($cadenares){
        $array_data = explode(";", $cadenares);
        $removed = explode("=", array_pop($array_data));
        $array_data[$removed[0]] = $removed[1];
        return $array_data;
    }


    protected function payWithTigo($phone, $amount, $operationNumber, $clientName)
    {
        //get variables from .env file
        $serverKey = env('serverKey');
        $serverUrl = env('serverUrl');
        $encryptionKey = env('encryptionKey');

        //create string to send to TIGO
        $unencryptedString = "pv_nroDocumento=;pv_linea=".$phone.";pv_monto=".$amount.";pv_orderId=".$operationNumber.";pv_confirmacion=".$clientName.";pv_notificacion=exitoso;pv_mensaje=Venta;";

        //encrypt
        $encryptedString = $this->encryptString($unencryptedString, $encryptionKey);

        //request SOAP
        $soapResult = $this->soapTigo($encryptedString, $serverKey, $serverUrl);

        //decrypt
        $unEncryptedString = $this->unencryptString($soapResult->return, $encryptionKey);

        //parse decrypted string
        return $this->parser($unEncryptedString);
    }

}
