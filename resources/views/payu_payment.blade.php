<!DOCTYPE html>
<html>
<head>
      <title></title>
<script>
function submitPaymentForm(){      
      var PaymentForm = document.forms.PaymentForm;
      PaymentForm.submit();      
}
</script>
</head>
<body onload="submitPaymentForm();" >
Please wait while you are redirected to our payment page.
<form  style='' action="<?php echo $action; ?>" method="post" name="PaymentForm">
      <input type="hidden" name="key" value="<?php echo $merchant_key; ?>" />
      <input type="hidden" name="hash" value="<?php echo $hash; ?>"/>
      <input type="hidden" name="txnid" value="<?php echo $txnid; ?>" />

      <input type='hidden' name="amount" value="<?php echo $amount; ?>" />
      <input type='hidden' name="firstname" id="firstname" value="<?php echo $firstname; ?>" />
      <input type='hidden' name="email" id="email" value="<?php echo $email; ?>" />
      <input type='hidden' name="phone" id="phone" value="<?php echo $phone; ?>" />
      <input type='hidden' name="productinfo" value="<?php echo $productinfo; ?>" size="64" />
      
      <input type='hidden' name="surl" value="<?php echo $surl; ?>" size="64" />
      <input type='hidden' name="furl" value="<?php echo $furl; ?>" size="64" />
      <input type='hidden' name="drop_category" value="<?php echo $drop_category; ?>" />

      
      <input type='hidden' name="lastname" id="lastname" value="<?php echo (empty($posted['lastname'])) ? '' : $posted['lastname']; ?>" />
      <input type='hidden' name="curl" value="" />

      @if($si_payment == 1)
            <input type="hidden" name="user_credentials" value="<?php echo $user_credentials; ?>"/>
            <input type="hidden" name="si" value="<?php echo $si; ?>" />
      @endif  
      
</form>
</body>
</html>