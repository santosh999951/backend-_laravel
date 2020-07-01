<!DOCTYPE html>
<html>
<head>
      <title></title>
<script type="text/javascript">
	//appPaymentFailureAction();

	function PaymentAction() {
	    //notify ios app
	    @if($source === "app"){
		    try {
		    	setTimeout(function(){
		        	iosPaymentSuccess("{{$request_hash_id}}", "{{$status}}", "{{$booking_status}}", "{{$amount}}", "{{$currency}}");
		    	}, 2000);
		    }
		    catch(err) {}

		    //notify android app
		    try {
		    	setTimeout(function(){
		        	Android.webPaymentSuccess("{{$request_hash_id}}", "{{$status}}", "{{$booking_status}}", "{{$amount}}", "{{$currency}}");
		    	}, 2000);
		        
		    }
		    catch(err) {}
	   }
	   @elseif($source === "web"){
	   		window.location = "{{$url}}";
	   }
	   @endif
	}
	</script>
</head>
<body onload="PaymentAction();" >
Please wait while you are being redirected.
</body>
</html>