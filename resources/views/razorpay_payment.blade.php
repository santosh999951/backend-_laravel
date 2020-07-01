<!DOCTYPE html>
<html>
<head>
<title></title>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body >
Please wait while you are redirected to our payment page.

<form action="{{$surl}}" method="POST" id='success_form'>
    <input type="hidden" value="{{$txnid}}" name="razorpay_order_id" id='razorpay_order_id'>
    <input type="hidden" value="" name="razorpay_payment_id" id='razorpay_payment_id'>
</form>

<form action="{{$furl}}" method="POST" id='failure_form'>
    <input type="hidden" value="{{$txnid}}" name="razorpay_order_id" id='razorpay_order_id'>
</form>

<script>
var options = {
    "key": "{{$merchant_key}}",
    "amount": "{{$amount_in_paisa}}", // 2000 paise = INR 20
    "name": "GuestHouser",
    "description": "{{$title}}",
    "image": "{{$logo}}",
    "callback_url": "{!! $surl !!}",
    "notes": {
            'txn_id' : "{{$txnid}}",
    },
    "handler": function (response){
        document.getElementById("razorpay_payment_id").value = response.razorpay_payment_id;
        document.getElementById("success_form").submit();
    },
    "modal": {
        "ondismiss": function(){
           document.getElementById("failure_form").submit();
        }
    },
    "prefill": {
        "name": "{{$firstname}}",
        "email": "{{$email}}",
        "contact": "{{$phone}}",
    },
    "theme": {
        "color": "#F37254"
    },
    "order_id":"{{$txnid}}"
};
var rzp1 = new Razorpay(options);
rzp1.open();
</script>

</body>
</html>

