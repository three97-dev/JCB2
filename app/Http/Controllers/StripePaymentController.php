<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Stripe;
use Auth;
use App\Models\User;
class StripePaymentController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */

    private $stripeApiKey;
    function __construct() {
        $this->stripeApiKey = env('Stripe_Api_SECRETKey');
    }

    public function stripe()
    {
        return view('stripe');
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe\Charge::create ([
                "amount" => 100 * 100,
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Test payment from junkcarboys.com."
        ]);

        Session::flash('success', 'Payment successful!');

        return back();
    }

    public function createIntent(Request $request)
    {
        $customer_id = $request->input('customer');
        try {
            \Stripe\Stripe::setApiKey(env('Stripe_Api_SECRETKey'));
        } catch (\Exception $Exception) {
            return response()->json([
                'error' => 'Payments Configuration Error'
            ])->setStatusCode(500);
        }
        try {
            $customer = \Stripe\Customer::retrieve($request->input('customer'));
        } catch (\Exception $Exception) {
            $error = $Exception->getMessage();
            if(str_contains($error, "No such customer")) {
                try {
                    $customer = \Stripe\Customer::create([
                        'email' => Auth::user()->email
                    ]);
                } catch (\Exception $Exception) {
                    return response()->json([
                        'error' => $Exception->getMessage()
                    ])->setStatusCode(500);
                }
            }
            else
            return response()->json([
                'error' => $Exception->getMessage()
            ])->setStatusCode(500);
        }

        User::where('email', Auth::user()->email)->update(['stripe_id' => $customer->id]);
        $customer_id = $customer->id;

        try {
            $Intent = Stripe\PaymentIntent::create([
                'amount' => $request->input('amount'),
                'currency' =>  $request->input('currency'),
                'customer' => $customer_id,
                'metadata' => [
                    'UserId' =>  $request->input('user_id'),
                    'UserEmail' => $request->input('user_email')
                ]
            ]);
        } catch (\Exception $Exception) {
            return response()->json([
                'error' => $Exception->getMessage()
            ])->setStatusCode(500);
        }
        return response()->json([
            'Intent' => [
                'Secret' => $Intent->client_secret
            ]
        ])->setStatusCode(201);
    }

    public function createNoPaymentIntent(Request $request)
    {
        $customer_id = $request->input('customer');
        try {
            \Stripe\Stripe::setApiKey(env('Stripe_Api_SECRETKey'));
        } catch (\Exception $Exception) {
            return response()->json([
                'error' => 'Payments Configuration Error'
            ])->setStatusCode(500);
        }
        try {
            $customer = \Stripe\Customer::retrieve($request->input('customer'));
            // $customer = \Stripe\Customer::retrieve($request->input('customer'));
        } catch (\Exception $Exception) {
            $error = $Exception->getMessage();
            if(str_contains($error, "No such customer")) {
                try {
                    $customer = \Stripe\Customer::create([
                        'email' => Auth::user()->email
                    ]);
                } catch (\Exception $Exception) {
                    return response()->json([
                        'error' => $Exception->getMessage()
                    ])->setStatusCode(500);
                }
            }
            else
            return response()->json([
                'error' => $Exception->getMessage()
            ])->setStatusCode(500);
        }

        User::where('email', Auth::user()->email)->update(['stripe_id' => $customer->id]);
        $customer_id = $customer->id;

        try {
            $Intent = Stripe\SetupIntent::create([
                'customer' => $customer_id
            ]);
        } catch (\Exception $Exception) {
            return response()->json([
                'error' => $Exception->getMessage()
            ])->setStatusCode(500);
        }
        return response()->json([
            'Intent' => [
                'Secret' => $Intent->client_secret
            ]
        ])->setStatusCode(201);
    }

    public function createRawIntent(Request $request)
    {

        try {
            \Stripe\Stripe::setApiKey(env('Stripe_Api_SECRETKey'));
        } catch (\Exception $Exception) {
            return response()->json([
                'error' => 'Payments Configuration Error'
            ])->setStatusCode(500);
        }
        try {
            $Intent = Stripe\PaymentIntent::create([
                'amount' => $request->input('amount'),
                'currency' =>  $request->input('currency'),
                'metadata' => [
                    'UserId' =>  $request->input('user_id'),
                    'UserEmail' => $request->input('user_email')
                ]
            ]);
        } catch (\Exception $Exception) {
            return response()->json([
                'error' => $Exception->getMessage()
            ])->setStatusCode(500);
        }
        return response()->json([
            'Intent' => [
                'Secret' => $Intent->client_secret
            ]
        ])->setStatusCode(201);
    }

    public function createSavedIntent(Request $request)
    {

        try {
            \Stripe\Stripe::setApiKey(env('Stripe_Api_SECRETKey'));
        } catch (\Exception $Exception) {
            return response()->json([
                'error' => 'Payments Configuration Error'
            ])->setStatusCode(500);
        }
        try {
            $Intent = Stripe\PaymentIntent::create([
                'amount' => $request->input('amount'),
                'currency' =>  $request->input('currency'),
                'customer' => Auth::user()->stripe_id,
                'payment_method' => Auth::user()->payment_method,
                'off_session' => true,
                'confirm' => true,
            ]);
        } catch (\Exception $Exception) {
            return response()->json([
                'error' => $Exception->getMessage()
            ])->setStatusCode(500);
        }
        return response()->json([
            'Intent' => [
                'Secret' => $Intent->client_secret
            ]
        ])->setStatusCode(201);
    }

    public function createCustomer(Request $request)
    {
        $user = Auth::user();
        if($user->stripe_id && $user->stripe_id != '')
            return response()->json([
                'customer_id' => $user->stripe_id
            ])->setStatusCode(201);
        try {
            \Stripe\Stripe::setApiKey(env('Stripe_Api_SECRETKey'));
        } catch (\Exception $Exception) {
            return response()->json([
                'error' => 'Payments Configuration Error'
            ])->setStatusCode(500);
        }
        try {
            $customer = \Stripe\Customer::create([
                'email' => Auth::user()->email
            ]);
        } catch (\Exception $Exception) {
            return response()->json([
                'error' => $Exception->getMessage()
            ])->setStatusCode(500);
        }
        User::where('email', Auth::user()->email)->update(['stripe_id' => $customer->id]);
        return response()->json([
            'customer_id' => $customer->id
        ])->setStatusCode(201);
    }

    public function listPayments(Request $request)
    {
        $customer = Auth::user()->stripe_id;
        if(!$customer || $customer == '')
            return response()->json([
                'methods' => [
                    "data"=> [],
                    "payment_method"=> ''
                ]
            ])->setStatusCode(201);
        try {
            \Stripe\Stripe::setApiKey($this->stripeApiKey);
        } catch (\Exception $Exception) {
            return response()->json([
                'error' => 'Payments Configuration Error'
            ])->setStatusCode(500);
        }
        try {
            $methods = \Stripe\PaymentMethod::all([
                'customer' => $customer,
                'type' => 'card',
            ]);
        } catch (\Exception $Exception) {
            $error = $Exception->getMessage();
            if(str_contains($error, "No such customer")) {
                User::where('email', Auth::user()->email)->update(['stripe_id' => null]);
                return response()->json([
                    'methods' => [
                        "data"=> [],
                        "payment_method"=> ''
                    ]
                ])->setStatusCode(201);
            }
            return response()->json([
                'error' => $Exception->getMessage()
            ])->setStatusCode(500);
        }
        $methods->payment_method = Auth::user()->payment_method;
        return response()->json([
            'methods' => $methods
        ])->setStatusCode(201);
    }

    public function setDefaultPayment(Request $request)
    {
        User::where('email', Auth::user()->email)->update(['payment_method' => $request->payment]);
        return "success";
    }

    public function updatePayment(Request $request)
    {
        try {
            \Stripe\Stripe::setApiKey($this->stripeApiKey);
        } catch (\Exception $Exception) {
            return response()->json([
                'error' => 'Payments Configuration Error'
            ])->setStatusCode(500);
        }
        try {
            $exp = explode('/', $request->exp);
            $methods = \Stripe\PaymentMethod::update($request->payment, [
                'billing_details' => [ 'name' => $request->name ],
                'card' => [ 'exp_month' => (int)$exp[0], 'exp_year' => 2000 + (int)$exp[1] ],
            ]);
        } catch (\Exception $Exception) {
            return response()->json([
                'error' => $Exception->getMessage()
            ])->setStatusCode(500);
        }
        return response()->json([
            'result' => 'success'
        ])->setStatusCode(201);
    }

    public function deletePayment(Request $request)
    {
        $payment_id = $request->payment;
        try {
            \Stripe\Stripe::setApiKey($this->stripeApiKey);
        } catch (\Exception $Exception) {
            return response()->json([
                'error' => 'Payments Configuration Error'
            ])->setStatusCode(500);
        }
        try {
            $method = \Stripe\PaymentMethod::retrieve($payment_id);
            $method->detach();
        } catch (\Exception $Exception) {
            return response()->json([
                'error' => $Exception->getMessage()
            ])->setStatusCode(500);
        }
        if($payment_id == Auth::user()->payment_method)
            User::where('email', Auth::user()->email)->update(['payment_method' => null]);
        return response()->json([
            'result' => 'success'
        ])->setStatusCode(201);
    }

    public function getPaymentMethod()
    {
        $payment_id = Auth::user()->payment_method;
        if(!$payment_id || $payment_id == null)
            return response()->json([
                'payment_method' => null
            ])->setStatusCode(201);
        try {
            \Stripe\Stripe::setApiKey($this->stripeApiKey);
        } catch (\Exception $Exception) {
            return response()->json([
                'error' => 'Payments Configuration Error'
            ])->setStatusCode(500);
        }
        try {

            $method = \Stripe\PaymentMethod::retrieve($payment_id);

        } catch (\Exception $Exception) {
            $error = $Exception->getMessage();
            if(str_contains($error, "No such PaymentMethod")) {
                User::where('email', Auth::user()->email)->update(['payment_method' => null]);
                return response()->json([
                    'payment_method' => null
                ])->setStatusCode(201);
            }
            return response()->json([
                'error' => $Exception->getMessage()
            ])->setStatusCode(500);
        }
        return response()->json([
            'payment_method' => $payment_id,
            'detail'=> $method
        ])->setStatusCode(201);
    }

    public function unsetPayment()
    {
        User::where('email', Auth::user()->email)->update(['payment_method' => null]);
        return response()->json([
            'result' => 'success'
        ])->setStatusCode(201);
    }
}
