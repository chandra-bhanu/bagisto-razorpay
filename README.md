# Bagisto Razorpay Payment Gateway Integration
 <img src="https://raw.githubusercontent.com/dwyl/repo-badges/master/highresPNGs/build-passing.png"  height="25">

### Razorpay payment gateway for bagisto laravel ecommerce with test mode & webhook support
 <img src="https://devdocs.bagisto.com/logo.png?__WB_REVISION__=7623b31ea8912e775aa903f3da491179"  height="100"> <img src="https://razorpay.com/assets/razorpay-logo.svg" width="200" height="100">
_____________________________________________________________________________________
**step by step guide for full installation**
_____________________________________________________________________________________

**1. Create A Package**<br/>
   ```php artisan package:make-payment-method Neexpg/Razorpay```<br/>   
**2. Remove all thing from :** <br/> packages/Neexpg directory<br/>
<br/>
**3. Place the content of this Repo to  :**<br/>  packages/Neexpg <br/>
**4. IN config/app.php:**<br/>
```
return [
    ...
    'providers' => [
        ...
        Neexpg/Razorpay\Providers\RazorpayServiceProvider::class,
        ...
    ]
    ...
];
```

**5. Open Laravel composer.json file for auto loading.:**<br/>

**Add you payment method namespace in psr-4 key**
```
"autoload": {
    ...
    "psr-4": {
        ...
        "Neexpg\\Razorpay\\": "packages/Neexpg/Razorpay/src"
        ...
    }
    ...
}
````

**Load Razorpay Vendor File:**

```
"files":[
          "packages/Neexpg/razorpay-php/Razorpay.php"
        ],
```
(Refer https://devdocs.bagisto.com/1.x/advanced/create-payment-method.html#_1-by-using-bagisto-package-generator)


**6. Remove CSRF for razorpay routes to fix session expirre after payment return:**<br/>
**in mIDDLEWIRE/verifyCRFtoken file add the following to the exclude array**<br/>
``` ,'razorpay/payment/success','razorpay/payment/fail','razorpay/payment/razorpay-hook' ```

**7. Set Up Webhoock**<br/>
**In Razorpay update webhook URL : https://YOUR DOMAIN/razorpay/payment/razorpay-hook** <br/>
Create a DB table rzp_payment to handle webhooks<br/>
```
CREATE TABLE `rzp_payments` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

**Set ID to primary and auto increment**
```


**8. Re Generate Autoload & Cache**<br/>
**In Terminal**<br/>
```
composer dump-autoload
php artisan config:cache
```
**All SET**<br/>
Now go to admin/config/sales/paymentmethods/ you will get razorpay<br/>


**To access razorpay payment details : OrderId, Status, PaymentID**
```
On Order Object you can query:
$additional=$order->payment->additional;

Return Fields:
status, razorpay_orderId, razorpay_paymentID

```
 
>**Note & Terms Of USE**<br/>
>As I didn't got any free extention to integrate  Razorpay in bagisto, created it in sheer need.<br/>
>Use it as at your own risk, as it involves financial transaction, I don't bear any responsibility.<br/>
>I solely recomend to use this code as a reference guide, and not to use it directly copy and pasting.<br/>

***If this really helped you, buy me a beer.<br/>
Donate to  : https://paypal.me/chandrabhanudas***

Thanks :slightly_smiling_face:



