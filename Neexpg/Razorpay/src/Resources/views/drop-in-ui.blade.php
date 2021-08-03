<!DOCTYPE html>
<html lang="en">
  <head>   
	<title>Redirecting to payment page</title>	
        <style>
            
.cp-spinner {
	width: 48px;
	height: 48px;
	position: relative;
	display: inline-block;
	-webkit-box-sizing: border-box;
	box-sizing: border-box;
	top:7%;
	
}
.cp-round::after, .cp-round::before {
	content: " ";
	width: 48px;
	height: 48px;
	display: inline-block;
	-webkit-box-sizing: border-box;
	box-sizing: border-box;
	position: relative;
	top: -30px;
	left: 0;
}
.cp-round::before {
	border-radius: 50%;
	border: 6px solid grey;
}
.cp-round::after {
	border-radius: 50%;
	border-top: 6px solid #030000;
	border-right: 6px solid transparent;
	border-bottom: 6px solid transparent;
	border-left: 6px solid transparent;
	-webkit-animation: spin 1s ease-in-out infinite;
	animation: spin 1s ease-in-out infinite;
	top: -82px;
}


@-webkit-keyframes spin {
 0% {
  -webkit-transform:rotate(0deg);
  transform:rotate(0deg)
 }
 to {
  -webkit-transform:rotate(1turn);
  transform:rotate(1turn)
 }
}
@keyframes spin {
 0% {
  -webkit-transform:rotate(0deg);
  transform:rotate(0deg)
 }
 to {
  -webkit-transform:rotate(1turn);
  transform:rotate(1turn)
 }
}
        </style>
  </head>
  <body style="background: #edfffe; color: #000000" onload="document.forms['neexpg_payment'].submit()">
  	
    <div class=""  style=" color: #000000; position:fixed; height:100%; width:100%; top:40%; text-align: center; ">
        <div class="cp-spinner cp-round"></div>
       <div>        
        Sending you to payment page, Please wait....<br/>
        (Do not press back button)
       </div>
    </div>
      
      <form method="POST" action="https://api.razorpay.com/v1/checkout/embedded" name="neexpg_payment" class="hidden" style="display: none">
        <input type="hidden" name="key_id" value="{{$payment['apikey']}}">
        <input type="hidden" name="order_id" value="{{$payment['oid']}}">
        <input type="hidden" name="name" value="{{$payment['name']}}">
        <input type="hidden" name="description" value="{{$payment['description']}}">
        <input type="hidden" name="image" value="https://cdn.razorpay.com/logos/XXXXXXXXXX.png">
        <input type="hidden" name="prefill[contact]" value="{{$payment['mobile']}}">
        <input type="hidden" name="prefill[email]" value="{{$payment['email']}}">
        <input type="hidden" name="notes[shipping address]" value="{{$payment['address']}}">
        <input type="hidden" name="callback_url" value="{{route('razorpay.payment.success')}}">
        <input type="hidden" name="cancel_url" value="{{route('razorpay.payment.fail')}}">
    </form>  
    
</body>
</html>
