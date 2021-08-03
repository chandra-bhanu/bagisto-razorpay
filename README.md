# bagisto-razorpay
Razorpay payment gateway for bagisto laravel ecommerce

*Will update complete installation. meanwhile feel free to explore and make it work*




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

Run composer dump-autoload.

After that run php artisan config:cache.

Now go to admin/config/sales/paymentmethods/ you will get razorpay


*Note*
As I didn't got any free extention to integrate  Razorpay in bagisto, created it in sheer need.
Use it as at your own risk, as it involves financial transaction, I don't bear any responsibility.
I solely recomend to use this code as a reference guide, and not to use it directly copy and pasting.

If this really helped you, buy me a beer.
Donate to  : paypal.me/chandrabhanudas

Thanks



