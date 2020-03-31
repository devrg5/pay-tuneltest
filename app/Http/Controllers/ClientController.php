<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Foundation\Validation\ValidatesRequests;
// use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// use \App\Transaction;
// use \App\Client;
// use \App\Cardholder;
// use SoapClient;

class ClientController extends Controller
{
    public function response(Request $request)
    {
        $authResult = $request->authResult;
        $amount = $request->amount;
        $currency = $request->currency;
        $date = $request->dateServer;
        $reserved2 = $request->orderId;

        return view('clients.response', ['authResult'=>$authResult, 'amount'=>$amount, 'currency'=>$currency, 'dateServer'=>$date, 'orderId'=>$reserved2]);
    }

    public function redirect()
    {
        return view('clients.redirect');
    }
}
