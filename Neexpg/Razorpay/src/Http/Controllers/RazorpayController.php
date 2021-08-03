<?php

namespace neexPg\Razorpay\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Checkout\Facades\Cart;
use App\Exceptions\Handler;


class RazorpayController extends Controller
{

    /**
     * InvoiceRepository object
     *
     * @var object
     */
    protected $invoiceRepository;

    /**
     * OrderRepository object
     *
     * @var array
     */
    protected $orderRepository;

    public function __construct(
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository
    )
    {
        $this->orderRepository = $orderRepository;

        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Redirects to the Braintree.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirect()
    {
        if(core()->getConfigData('sales.paymentmethods.razorpay.sandbox')):
            $merchentId = core()->getConfigData('sales.paymentmethods.razorpay.testclientid');
            $privateKey = core()->getConfigData('sales.paymentmethods.razorpay.testclientsecret');
        else:
            $merchentId = core()->getConfigData('sales.paymentmethods.razorpay.clientid');
            $privateKey = core()->getConfigData('sales.paymentmethods.razorpay.clientsecret');
        endif;
            
        
       

        if($merchentId && $privateKey)
        {
            try {
                
              //GENERATE RZP ORDER
                $api = new Api($merchentId,$privateKey);

                $cart = Cart::getCart();
                
               //GET CART DATA
                $cartAmount = $cart->base_grand_total;
                $cartID= $cart->id;
                
                
                // Orders
                $orderAPI  = $api->order->create(array('receipt' => $cartID, 'amount' => ($cartAmount*100), 'currency' => 'INR', 'payment_capture' =>  '1')); // Creates order
              
                $pgorderId = $orderAPI['id']; // Get the created Order ID
                
                //CREATE FORM DATA AND PUSH TO VIEW   
                $paymentData=array();
                $payment['apikey']=$merchentId;
                $payment['oid']=$pgorderId;
                $payment['name']=core()->getConfigData('sales.paymentmethods.razorpay.merchantname');
                $payment['description']=core()->getConfigData('sales.paymentmethods.razorpay.merchantdesc');
                $payment['mobile']= $cart->billing_address->phone;
                $payment['email']=$cart->billing_address->email;
                $payment['address']=$cart->billing_address->city.','.$cart->billing_address->state.','.$cart->billing_address->postcode.','.$cart->billing_address->country;
                
                
                
                
            

            return view('razorpay::drop-in-ui', compact('paymentData');
'));
            }
            catch(\Exception $e)
            {
                return redirect()->back();
            }
        }
        else {
           return redirect()->back();
        }
    }

    /**
     * Perform the transaction
     *
     * @return response
     */
    public function transaction(Request $request)
    {
        if(core()->getConfigData('sales.paymentmethods.braintree.debug') == '1')
            { $debug = 'sandbox'; }
        else
           { $debug = 'production'; }

        $gateway = new Braintree_Gateway([
            'environment' => $debug,
            'merchantId' => core()->getConfigData('sales.paymentmethods.braintree.braintree_merchant_id'),
            'publicKey' => core()->getConfigData('sales.paymentmethods.braintree.braintree_public_key'),
            'privateKey' => core()->getConfigData('sales.paymentmethods.braintree.braintree_private_key')
        ]);

        $clientToken = $gateway->clientToken()->generate();

        $cartAmount = \Cart::getCart()->base_grand_total;

        $payload = $request->input('payload', false);
        $nonceFromTheClient = $payload['nonce'];

        $result = $gateway->transaction()->sale([
            'amount' => $cartAmount,
            'paymentMethodNonce' => $nonceFromTheClient,
            'options' => [
              'submitForSettlement' => True
            ]
          ]);

        if($result->success == 'true') {

            $order = $this->orderRepository->create(Cart::prepareDataForOrder());

            $this->order = $this->orderRepository->findOneWhere([
                'cart_id' => Cart::getCart()->id
                ]);

            $this->orderRepository->update(['status' => 'processing'], $this->order->id);

            Cart::deActivateCart();

            session()->flash('order', $order);

            $this->invoiceRepository->create($this->prepareInvoiceData());

            session()->flash('success', trans('braintree::app.payment-successfull'));

        }
        // return redirect()->route('shop.checkout.success');
        return response()->json($result);

    }

    public function verifyPaymentHook(Request $request)
    {
          if(core()->getConfigData('sales.paymentmethods.razorpay.sandbox')):
            $merchentId = core()->getConfigData('sales.paymentmethods.razorpay.testclientid');
            $privateKey = core()->getConfigData('sales.paymentmethods.razorpay.testclientsecret');
        else:
            $merchentId = core()->getConfigData('sales.paymentmethods.razorpay.clientid');
            $privateKey = core()->getConfigData('sales.paymentmethods.razorpay.clientsecret');
        endif;
        
        
        $eventId = $request->header('x-razorpay-event-id');
        $webhookSignature=$request->header('x-razorpay-signature');
        $webhookBody= json_decode($request->getContent());
        $pass=0;
        
        $generatedSignature = hash_hmac('sha256',$request->getContent(), $privateKey);
        if ($generatedSignature == $webhookSignature):
            
        endif;
    }
    
     //PAYMENT CANCELLED
    public function cancelOrder(Request $request)
    {
        //CANCELL ORDER
         return redirect()->route('shop.checkout.fail')->with('errormessage', 'Order Cancelled!');
    }
    
    
    //PAYMENT SUCEESS
    public function confirmOrder(Request $request)
    {
         if(core()->getConfigData('sales.paymentmethods.razorpay.sandbox')):
            $merchentId = core()->getConfigData('sales.paymentmethods.razorpay.testclientid');
            $privateKey = core()->getConfigData('sales.paymentmethods.razorpay.testclientsecret');
        else:
            $merchentId = core()->getConfigData('sales.paymentmethods.razorpay.clientid');
            $privateKey = core()->getConfigData('sales.paymentmethods.razorpay.clientsecret');
        endif;
        
        
        
        if(isset($request['error'])):
            return redirect()->route('shop.checkout.fail')->with('errormessage', $request['error']['description']);
        else:
            $oid =  $request['razorpay_order_id'];
            $api = new Api($merchentId,$privateKey);
            $expected_signature = $request['razorpay_order_id']. '|' .$request['razorpay_payment_id'] ;
            $generatedSignature = hash_hmac('sha256', $expected_signature, $privateKey);
            if ($generatedSignature == $request['razorpay_signature']):
                
                //PROCESS THE ORDER
                
                
                return redirect()->route('shop.checkout.success');
            else:
               return redirect()->route('shop.checkout.fail')->with('errormessage', 'Something is not right, for security reason the transaction can\'t be processed.'); 
            endif;
        endif;
    }
    
    
    
    /**
     * Prepares order's invoice data for creation
     *
     * @return array
     */
    protected function prepareInvoiceData()
    {
        $invoiceData = [
            "order_id" => $this->order->id
        ];

        foreach ($this->order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }

}
