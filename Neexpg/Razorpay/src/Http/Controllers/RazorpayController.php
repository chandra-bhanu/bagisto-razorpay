<?php

namespace Neexpg\Razorpay\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Checkout\Facades\Cart;
use App\Exceptions\Handler;
use Webkul\Sales\Models\OrderPayment as OrderPayment;

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
                $payment=array();
                $payment['apikey']=$merchentId;
                $payment['oid']=$pgorderId;
                $payment['name']=core()->getConfigData('sales.paymentmethods.razorpay.merchantname');
                $payment['description']=core()->getConfigData('sales.paymentmethods.razorpay.merchantdesc');
                $payment['mobile']= $cart->billing_address->phone;
                $payment['email']= $cart->billing_address->email;
                $payment['address']=$cart->billing_address->city.','.$cart->billing_address->state.','.$cart->billing_address->postcode.','.$cart->billing_address->country;
                
                
                $order = $this->orderRepository->create(Cart::prepareDataForOrder());
                $this->order = $this->orderRepository->findOneWhere([
                'cart_id' => Cart::getCart()->id
                ]);
            
                
                $pgUpdateD = OrderPayment::where('order_id',$this->order->id)->firstOrFail();
                $additional=array();
                $additional['status']='Pending Payment';
                $additional['oid']=$pgorderId;
                $additional['pgreference']='';                
                $pgUpdateD->additional=$additional;
                $pgUpdateD->save();
              //  $pgUpdateD->update(['additional->status' => 'Pending Payment','additional->oid' =>$pgorderId,'additional->pgreference' =>'']);
              
                
                $this->orderRepository->update(['status' => 'pending_payment'], $this->order->id);
                
             

          return view('razorpay::drop-in-ui', compact('payment'));

            }
            catch(\Exception $e)
            {
                
                echo 'Message: ' .$e->getMessage();
                
                
               // return redirect()->back();
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
            
            //verify agnaist payment
            
        endif;
    }
    
     //PAYMENT CANCELLED
    public function paymentFail(Request $request)
    {
        //$order = $this->orderRepository->create(Cart::prepareDataForOrder());
                $this->order = $this->orderRepository->findOneWhere([
                'cart_id' => Cart::getCart()->id
                ]);
            
                $pgUpdateD = OrderPayment::where('order_id',$this->order->id)->firstOrFail();
                $additional=array();
                $additional['status']='Canceled Payment';
                $pgUpdateD->additional=$additional;
                $pgUpdateD->save();
                
                
                
                $this->orderRepository->update(['status' => 'canceled'], $this->order->id);
        Cart::deActivateCart();
         session()->flash('danger','Order Cancelled!');
        //CANCELL ORDER
         return redirect()->route('shop.checkout.fail')->with('errormessage', 'Order Cancelled!');
    }
    
    
    //PAYMENT SUCEESS
    public function paymentSuccess(Request $request)
    {
         if(core()->getConfigData('sales.paymentmethods.razorpay.sandbox')):
            $merchentId = core()->getConfigData('sales.paymentmethods.razorpay.testclientid');
            $privateKey = core()->getConfigData('sales.paymentmethods.razorpay.testclientsecret');
        else:
            $merchentId = core()->getConfigData('sales.paymentmethods.razorpay.clientid');
            $privateKey = core()->getConfigData('sales.paymentmethods.razorpay.clientsecret');
        endif;
        
        
        
        if(isset($request['error'])):
           // $order = $this->orderRepository->create(Cart::prepareDataForOrder());
                $this->order = $this->orderRepository->findOneWhere([
                'cart_id' => Cart::getCart()->id
                ]);
            
                $pgUpdateD = OrderPayment::where('order_id',$this->order->id)->firstOrFail();
                $additional=array();
                $additional['status']=$request['error']['description'];
                $pgUpdateD->additional=$additional;
                $pgUpdateD->save();
        
        
        
                $this->orderRepository->update(['status' => 'pending_payment'], $this->order->id);
            Cart::deActivateCart();
            session()->flash('danger',$request['error']['description']);
            return redirect()->route('shop.checkout.fail')->with('errormessage', $request['error']['description']);
        else:
            
            
            $oid =  $request['razorpay_order_id'];
            $api = new Api($merchentId,$privateKey);
            $expected_signature = $request['razorpay_order_id']. '|' .$request['razorpay_payment_id'] ;
            $generatedSignature = hash_hmac('sha256', $expected_signature, $privateKey);
            if ($generatedSignature == $request['razorpay_signature']):
                
                //PROCESS THE ORDER
               // $order = $this->orderRepository->create(Cart::prepareDataForOrder());
                $this->order = $this->orderRepository->findOneWhere([
                'cart_id' => Cart::getCart()->id
                ]);
            
               $pgUpdateD = OrderPayment::where('order_id',$this->order->id)->firstOrFail();
                $additional=array();
                $additional['status']='Paid';
                $additional['oid']=$request['razorpay_order_id'];
                $additional['pgreference']=$request['razorpay_payment_id'];                
                $pgUpdateD->additional=$additional;
                $pgUpdateD->save();
            
                $this->orderRepository->update(['status' => 'processing'], $this->order->id);
                $this->invoiceRepository->create($this->prepareInvoiceData());

                Cart::deActivateCart();
                session()->flash('order', $this->order);                
                session()->flash('success', trans('Payment Successfull!'));
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
