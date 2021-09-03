# bagisto-razorpay
Razorpay payment gateway for bagisto laravel ecommerce

*Now with Webhook Support, and step by step guide for full installation*




php artisan package:make-payment-method Neexpg/Razorpay

Remove all thing from 
packages/Neexpg directory

Place the content of the Repo to packages/Neexpg 


IN config/app.php.
return [
    ...
    'providers' => [
        ...
        Neexpg/Razorpay\Providers\RazorpayServiceProvider::class,
        ...
    ]
    ...
];

After that, add you payment method namespace in psr-4 key in composer.json file for auto loading.
"autoload": {
    ...
    "psr-4": {
        ...
        "Neexpg\\Razorpay\\": "packages/Neexpg/Razorpay/src"
        ...
    }
    ...
}

(Refer https://devdocs.bagisto.com/1.x/advanced/create-payment-method.html#_1-by-using-bagisto-package-generator)


"files":[
          "packages/Neexpg/razorpay-php/Razorpay.php"
        ],

iN mIDDLEWIRE/verifyCRFtoken file
,'razorpay/payment/success','razorpay/payment/fail','razorpay/payment/razorpay-hook'

Run composer dump-autoload.

After that run php artisan config:cache.

Now go to admin/config/sales/paymentmethods/ you will get razorpay

To get the payment info : status, razorpay_orderId, razorpay_paymentID
 $additional=$order->payment->additional;
 Will update it to synk to webhooks in next update

*WebHook*
webhook Url : <YOUR DOMAIN>/razorpay/payment/razorpay-hook
    
Create a DB table rzp_payment to handle webhooks: SQL statement is available on header of : packages/Neexpg/Razorpay/src/Models/RazorpayEvents.php
 /*CREATE TABLE `rzp_payments` (
  `id` int(11) NOT NULL,
  `razorpay_event_id` varchar(50) DEFAULT NULL,  
  `razorpay_invoice_id` varchar(40) DEFAULT NULL,
  `razorpay_order_id` varchar(80) DEFAULT NULL,
  `razorpay_payment_id` varchar(40) DEFAULT NULL,
  `razorpay_invoice_status` varchar(40) DEFAULT 'issued',
  `razorpay_invoice_receipt` varchar(40) DEFAULT NULL,
  `razorpay_signature` longtext,  
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;*/   




*Note*
As I didn't got any free extention to integrate  Razorpay in bagisto, created it in sheer need.
Use it as at your own risk, as it involves financial transaction, I don't bear any responsibility.
I solely recomend to use this code as a reference guide, and not to use it directly copy and pasting.

If this really helped you, buy me a beer.
Donate to  : https://paypal.me/chandrabhanudas

Thanks



