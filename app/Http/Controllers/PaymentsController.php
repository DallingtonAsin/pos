<?php

namespace App\Http\Controllers;

use Bmatovu\MtnMomo\Products\Collection;
use Bmatovu\MtnMomo\Exceptions\CollectionRequestException;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\ExpressCheckout;

class PaymentsController extends Controller
{

    public function __construct()
    {
    }

    public function index()
    {
        return view('pages.main.mtnmomo');
    }


    public function paypalIndex()
    {
        return view('pages.main.paypal');
    }


    public function MoMoPayment(Request $request)
    {

        $this->validate($request, [
            'phoneNo' => 'required',
            'amount' => 'required',
        ]);

        $tel_no = trim($request->input('phoneNo'));
        $amt = trim($request->input('amount'));
        $amount = floatval($amt);
        try {
            $collection = new Collection();
            $transactionId = $this->generateRandomString();
            $momoTransId = $collection->transact($transactionId, $tel_no, $amount);
            return back()->with('success', "Transaction successful with ID:" . $momoTransId . "");
        } catch (CollectionRequestException $e) {
            do {
                printf(
                    "\n\r%s:%d %s (%d) [%s]\n\r",
                    $e->getFile(),
                    $e->getLine(),
                    $e->getMessage(),
                    $e->getCode(),
                    get_class($e)
                );
            } while ($e = $e->getPrevious());
            return back()->with('failed', "Smelling bad issues");
        }
    }

    public function generateRandomString($length = 10)
    {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    public function PayPalPayment(Request $request)
    {

        $this->validate($request, [
            /*'card_no' => 'required',
    		'exp_month' => 'required',
    		'exp_year' => 'required',
    		'cvv' => 'required',
    		'amount' => 'required',*/]);

        // $card_no = trim($request->input('card_no'));
        // $exp_month = trim($request->input('exp_month'));
        // $exp_year = trim($request->input('exp_year'));
        // $cvv = trim($request->input('cvv'));
        // $amount = trim($request->input('amount'));

        //     $payment = new Payment;
        //     $payment->transaction_id = $this->generateRandomString();
        //     $payment->currency_code = "USD" ;
        //     $payment->paid_amount = floatval($amount) ;
        //     $payment->payment_details = $message = "payment of ".number_format($amount)." effected successfully";
        //     $payment->payment_status = "OK";

        //     $result = $payment->save();
        //     if($result)
        //     {
        //        return back()->with("success", $message);
        //     }
        //     else
        //     {
        //     	return back()->with("error", "Sorry, failed to transact");
        //     }

        $data = [];

        $data['items'] = [
            [
                'name' => 'codechief.org',
                'price' => 2,
                'desc'  => 'Description goes herem',
                'qty' => 1
            ]
        ];

        $data['invoice_id'] = 1;
        $data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";
        $data['return_url'] = route('payment.success');
        $data['cancel_url'] = route('payment.cancel');
        $data['total'] = 2;

        $provider = new ExpressCheckout;

        $response = $provider->setExpressCheckout($data);

        $response = $provider->setExpressCheckout($data, true);

        return redirect($response['paypal_link']);
    }


    public function cancel()
    {
        dd('Sorry you payment is canceled');
    }

    public function success(Request $request)
    {

        $provider = new ExpressCheckout;
        $response = $provider->getExpressCheckoutDetails($request->token);

        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
            $message = "Your payment was successful. You can create success page here.";
            return view('pages.main.paypal')
                ->with("success", $message);
        } else {
            $messageErr = "Payment failed!";
            return view('pages.main.paypal')->with("error", $messageErr);
        }
    }



    protected function ActionMessage($action)
    {
        $message = "You have successfully " . $action . "";
        return $message;
    }
}
