<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use \App\Transaction;
use \App\Client;
use \App\Cardholder;
use App;
use SoapClient;
use Validator;
Use Exception;

class TransactionController extends Controller
{
    public function index()
    {

        return view('transactions.form', '');
    }

    public function create()
    {
        return view('transactions.form');
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
            'inputEnv' => 'required|in:0,1',
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
        $controlEnv = $request->inputEnv;
        $verification = $request->verificationNumber;
        //get variables from .env file
        $programmingLanguage = env('programmingLanguage');
        if ( App::environment() === 'production' ) {
            $acquirerId = env('acquirerId');
            $idCommerce = env('idCommerce');
            $claveSecreta = env('claveSecretaPasarela');
        }else {
            $acquirerId = env('acquirerIdIntegration');
            $idCommerce = env('idCommerceIntegration');
            $claveSecreta = env('claveSecretaPasarelaIntegration');
        }
        //set english if requested
        if ($language == 'en' || $language == 'EN') {
            App::setLocale('en');
        }
        //create the verification key
        /*El campo purchaseVerification contiene el valor cifrado de los campos acquirerId, idCommerce,
purchaseOperationNumber, purchaseAmount, purchaseCurrencyCode y la clave SHA-2 para la pasarela
descargada del sistema V-Payment. (PASSWORD_COM_XXXX_PASARELA_XXXXXXXXXXXX.txt).*/
        $verificationLaravel = openssl_digest($cardHolderEmail .
            $purchaseAmount .
            $purchaseCurrencyCode .
            $clientId . $orderId .
            $controlEnv .
            $client->token, 'sha512');

        //check verificationNumber from the commerce and the app
        if($verification == $verificationLaravel){

            //get variable from DB transactions table
            $purchaseOperationNumber = str_pad($this->getTransactionId(), 9, "0", STR_PAD_LEFT);

            //get or store the cardholder in the DB
            $cardHolder = $this->getCardHolder($cardHolderEmail, $shippingFirstName, $shippingLastName);
            $userCommerce = $cardHolder->user_commerce;
            $userCodePayme = $cardHolder->user_code_payme;

            if($userCodePayme == ""){
                //get info from Wallet
                $userCodePayme = $this->getUserCodePayme($cardHolder->email, $cardHolder->user_commerce, $cardHolder->name, $cardHolder->last_name);
                //store user-code-payme to cardHolder
                $this->storeUserCodePayme($cardHolder->email, $userCodePayme);
            }

            //generate this variable to control ESTO SE SIGNDATA EN CYBERSOURCE
            $purchaseVerification = openssl_digest($acquirerId . $idCommerce . $purchaseOperationNumber . $purchaseAmount . $purchaseCurrencyCode . $claveSecreta, 'sha512');

            //store transaction
            $transaction = $this->storeTransaction($client->user_id, $cardHolder->email, $purchaseOperationNumber, $purchaseVerification, $purchaseAmount, $purchaseCurrencyCode, $language, $descriptionProducts, $cardHolderAddress, $cardHolderZip, $cardHolderCity, $cardHolderState, $cardHolderCountry);

            return view('transactions.checkout',
                ['shippingFirstName' => $shippingFirstName,
                    'shippingLastName' => $shippingLastName,
                    'purchaseAmount' => $purchaseAmount,
                    'acquirerId' => $acquirerId,
                    'idCommerce' => $idCommerce,
                    'purchaseOperationNumber' => $purchaseOperationNumber,
                    'purchaseCurrencyCode' => $purchaseCurrencyCode,
                    'language' => $language,
                    'shippingEmail' => $cardHolderEmail,
                    'shippingAddress' => $cardHolderAddress,
                    'shippingZIP' => $cardHolderZip,
                    'shippingCity' => $cardHolderCity,
                    'shippingState' => $cardHolderState,
                    'shippingCountry' => $cardHolderCountry,
                    'userCommerce' => $userCommerce,
                    'userCodePayme' => $userCodePayme,
                    'descriptionProducts' => $descriptionProducts,
                    'programmingLanguage' => $programmingLanguage,
                    'reserved1' => $clientId,
                    'reserved2' => $orderId,
                    'reserved3' => $transaction->id,
                    'reserved4' => $controlEnv,
                    'purchaseVerification' => $purchaseVerification,
                'clientImage' => $client->user->image]);

        }else{

            return abort(403, 'Los datos fueron modificados! Vuelva a realizar la compra.');

        }
    }

    public function response(Request $request)
    {

        //get variables from .env file
        if ( App::environment() === 'production' ) {
            $acquirerId = env('acquirerId');
            $idCommerce = env('idCommerce');
            $claveSecreta = env('claveSecretaPasarela');
        }else {
            $acquirerId = env('acquirerIdIntegration');
            $idCommerce = env('idCommerceIntegration');
            $claveSecreta = env('claveSecretaPasarelaIntegration');
        }

        //variables to send to the client
        $amount = $request->purchaseAmount;
        $currency = $request->purchaseCurrencyCode;
        $dateTime = $request->txDateTime;
        $reserved2 = $request->reserved2;

        //get variables
        $operationNumber = $request->purchaseOperationNumber;
        $authorizationCode = $request->authorizationCode;
        $authorizationResult = $request->authorizationResult;
        $errorCode = $request->errorCode;
        $errorMessage = $request->errorMessage;
        $client = $this->getClient($request->reserved1);
        $transactionId = $request->reserved3;
        $controlEnv = $request->reserved4;

        if ($controlEnv === '1' && App::environment() === 'production'){
            return view('transactions.integration.redirectForm', ['purchaseAmount' => $amount, 'purchaseCurrencyCode' => $currency, 'txDateTime' => $dateTime, 'reserved2' => $reserved2, 'purchaseOperationNumber' => $operationNumber, 'authorizationCode' => $authorizationCode, 'authorizationResult' => $authorizationResult, 'errorCode' => $errorCode, 'errorMessage' => $errorMessage, 'reserved1' => $request->reserved1, 'reserved3' => $transactionId, 'reserved4' => $controlEnv, 'purchaseVerification' => $request->purchaseVerification]);
        }

        //get and generate the purchaseVerification variable
        $purchaseVericationVPOS2 = $request->purchaseVerification;
        $purchaseVericationComercio = openssl_digest($acquirerId .
            $idCommerce .
            $operationNumber .
            $amount .
            $currency .
            $authorizationResult .
            $claveSecreta, 'sha512');

        if($purchaseVericationVPOS2 == $purchaseVericationComercio){

            //get the transaction to modify from DB
            $transactionToModify = Transaction::where('id', $transactionId)->first();

            //duplicate operation number
            if($errorCode != "2202"){
                //assign new values
                $transactionToModify->authorization_code = $authorizationCode;
                $transactionToModify->authorization_result = $authorizationResult;
                $transactionToModify->error_code = $errorCode;
                $transactionToModify->error_message = $errorMessage;

                if ($authorizationResult == "00") {
                    //set Operación Autorizada
                    $transactionToModify->state = 1;
                    $transactionToModify->message_to_client = 'Operación Autorizada';
                }else if ($authorizationResult == "01") {
                    // set Operación Denegada
                    $transactionToModify->state = 0;
                    $transactionToModify->message_to_client = 'Operación Denegada';
                    $transactionToModify->authorization_code = '';
                }else if ($authorizationResult == "05") {
                    // set Operación Rechazada
                    $transactionToModify->authorization_code = '';
                    $transactionToModify->state = 0;
                    $transactionToModify->message_to_client = 'Operación Rechazada';
                }

                //save new values for transaction
                $transactionToModify->save();
            }

            $verification = openssl_digest($authorizationResult . $amount . $currency . $dateTime . $reserved2 . $client->token, 'sha512');

            return view('transactions.responseForm', ['clientResponseUrl' => $client->url_response, 'authResult' => $authorizationResult, 'amount' => $amount, 'currency' => $currency, 'dateServer' => $dateTime, 'reserved2' => $reserved2, 'verificationNumber' => $verification]);

        }else if($purchaseVericationVPOS2 == ""){
            // get transaction to modify from DB
            $transactionToModify = Transaction::where('id', $transactionId)->first();
            $authorizationResult = "05";

            // set Operación Rechazada
            $transactionToModify->authorization_result = $authorizationResult;
            $transactionToModify->error_code = $errorCode;
            $transactionToModify->error_message = $errorMessage;
            $transactionToModify->authorization_code = '';
            $transactionToModify->state = 0;
            $transactionToModify->message_to_client = 'Operación Rechazada';

            //save new values for transaction
            $transactionToModify->save();

            $verification = openssl_digest($authorizationResult . $amount . $currency . $dateTime . $reserved2 . $client->token, 'sha512');

            return view('transactions.responseForm', ['clientResponseUrl' => $client->url_response, 'authResult' => $authorizationResult, 'amount' => $amount, 'currency' => $currency, 'dateServer' => $dateTime, 'reserved2' => $reserved2, 'verificationNumber' => $verification]);

        }else{
            //redirect to client commerce
            return redirect('client/redirect')->with('clientUrl', $client->url_base)->with('message', 'Datos inconsistentes. Sera redirigido al comercio.');
        }
    }

    public function cancelTransaction(Request $request)
    {
        $transactionId = $request->reserved3;

        $transactionToModify = Transaction::where('id', $transactionId)->first();
        $transactionToModify->state = 0;
        $transactionToModify->authorization_result = "05";
        $transactionToModify->error_code = "2300";
        $transactionToModify->error_message = "User Cancelled in PASS 1";
        $transactionToModify->message_to_client = 'Operación Rechazada';
        $transactionToModify->save();

        $client = $this->getClient($request->reserved1);

        return redirect('client/redirect')->with('clientUrl', $client->url_base);
    }


    protected function getUserCodePayme($email, $cod, $names, $lastNames)
    {
        //get variables from the .env file
        if ( App::environment() === 'production' ) {
            $idEntCommerce = env('idEntCommerce');
            $claveSecreta = env('claveSecretaWallet');
        }else {
            $idEntCommerce = env('idEntCommerceIntegration');
            $claveSecreta = env('claveSecretaWalletIntegration');
        }

        //generate verification variable
        $registerVerification = openssl_digest($idEntCommerce . $cod . $email . $claveSecreta, 'sha512');

        //Referencia al Servicio Web de Wallet
        $wsdl = 'https://www.pay-me.pe/WALLETWS/services/WalletCommerce?wsdl';
        $clientSoap = new SoapClient($wsdl);

        //parametros para consultar a wallet
        $params = array(
            'idEntCommerce' => $idEntCommerce,
            'codCardHolderCommerce' => $cod,
            'names' => $names,
            'lastNames' => $lastNames,
            'mail' => $email,
            'reserved1' => '',
            'reserved2' => '',
            'reserved3' => '',
            'registerVerification' => $registerVerification
        );

        //Consumo del metodo RegisterCardHolder
        $result = $clientSoap->RegisterCardHolder($params);

        return $result->codAsoCardHolderWallet;
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


    protected function getClient($clientId)
    {
        return Client::where('client_identifier', $clientId)->firstOrFail();
    }


    protected function getCardHolder($cardHolderEmail, $shippingFirstName, $shippingLastName)
    {
        return Cardholder::firstOrCreate(
            ['email' => $cardHolderEmail],
            ['user_commerce' => 'nova'.(Cardholder::all()->count()+1), 'name' => $shippingFirstName, 'last_name' => $shippingLastName]
        );
    }


    protected function storeTransaction($client_id, $cardHolder_id, $purchaseOperationNumber, $purchaseVerification, $purchaseAmount, $purchaseCurrencyCode, $language, $descriptionProducts, $cardHolderAddress, $cardHolderZip, $cardHolderCity, $cardHolderState, $cardHolderCountry)
    {
        try
        {
            return Transaction::create(['client_id' => $client_id, 'cardholder_id'=> $cardHolder_id, 'purchase_operation_number'=> $purchaseOperationNumber, 'purchase_verification' => $purchaseVerification, 'purchase_amount' => $purchaseAmount, 'currency_code' => $purchaseCurrencyCode, 'language' => $language,'description_products' => $descriptionProducts, 'cardholder_address' => $cardHolderAddress, 'cardholder_zip' => $cardHolderZip, 'cardholder_city' => $cardHolderCity, 'cardholder_state' => $cardHolderState, 'cardholder_country' => $cardHolderCountry]);
        }
        catch(Exception $e)
        {
            $purchaseOperationNumber = str_pad($this->getTransactionId(), 9, "0", STR_PAD_LEFT);
            $this->storeTransaction($client_id, $cardHolder_id, $purchaseOperationNumber, $purchaseVerification, $purchaseAmount, $purchaseCurrencyCode, $language, $descriptionProducts, $cardHolderAddress, $cardHolderZip, $cardHolderCity, $cardHolderState, $cardHolderCountry);
        }
    }


    public function storeUserCodePayme($email, $codePayme)
    {
        $cardHolder = Cardholder::find($email);
        $cardHolder->user_code_payme = $codePayme;
        $cardHolder->save();
    }
}
