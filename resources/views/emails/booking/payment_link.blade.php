<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width"/>
    
    <title>Booking Confirmed</title>
    <style type="text/css">/*////// RESET STYLES //////*/

            body, #bodyTable, #bodyCell{height:100% !important; margin:0; padding:0; width:100% !important; }
            table{border-collapse:collapse;}
            img, a img{border:0; outline:none; text-decoration:none;}
            h1, h2, h3, h4, h5, h6{margin:0; padding:0;}
            p{margin: 0em 0;}

            /*////// GENERAL STYLES //////*/
            body, #bodyTable{background-color:#F5F5F5;}
            #bodyCell{padding-top:40px; padding-bottom:40px;}
            #emailBody{background-color:#FFFFFF; border:0; border-collapse:separate; border-radius:4px;}
            h1, h2, h3, h4, h5, h6{color:#202020; font-family:'Helvetica','Arial',sans-serif; font-size:20px; line-height:125%; text-align:Left;}
            .textContent, .textContentLast{color:#404040; font-family:'Helvetica','Arial',sans-serif; font-size:16px; line-height:125%; text-align:center; padding:0 35px;}
            .textContent a, .textContentLast a{color:#2C9AB7; text-decoration:underline;}
            .nestedContainer{background-color:#E5E5E5; border:1px solid #CCCCCC;}
            .emailButton{background-color:#2C9AB7; border-collapse:separate; border-radius:4px;}
            .buttonContent{color:#FFFFFF; font-family:'Helvetica','Arial',sans-serif; font-size:18px; font-weight:bold; line-height:100%; padding:15px; text-align:center;}
            .buttonContent a{color:#FFFFFF; display:block; text-decoration:none;}
            .emailCalendar{background-color:#FFFFFF; border:1px solid #CCCCCC;}
            .emailCalendarMonth{background-color:#2C9AB7; color:#FFFFFF; font-size:16px; font-weight:bold; padding-top:10px; padding-bottom:10px; text-align:center;}
            .emailCalendarDay{color:#2C9AB7;  font-size:60px; font-weight:bold; line-height:100%; padding-top:20px; padding-bottom:20px; text-align:center;}
    </style>

<?php

echo "<img src='https://www.google-analytics.com/collect?v=1&tid=UA-51742529-2&cid=555&t=event&ec=manual-payment-link-email&ea=email-open&el=".$view_data['request_hash_id']."&dp=%2Fpayment%2Fpayment-invoice&cm=email&cn=Pyament%20link%20mail%20open&dt=Payment%20page'>";
?>
</head>
<body>

<center style="width:100%;  background:#7ac697; padding:30px 0 30px 0;">
<table border="0" cellpadding="0" cellspacing="0" style="background:#fff; margin:0 auto; max-width: 600px;">
    <tbody>
        <tr>
            <td class="textContent" style="padding-top:20px; text-align:center; padding-left:25px;" valign="top"><a href="{{ MAILER_SITE_URL }}"><img src="{{ $view_data['logo'] }}" style="float:left;width:230px;" /> </a></td>
        </tr>


        <tr>
            <td class="textContent" style="padding-top:0px; text-align:center; padding-left:0px; padding-right:0;" valign="top"><img src="{{ $view_data['booking_confirmed_image'] }}" style="display: inline-block;" /></td>
        </tr>

        <tr>
            <td class="textContent" style="padding-top:0px; text-align:center; padding-left:35px;" valign="top">

            <!-- <h1 style=" font-size:35px; margin-top:20px; line-height:50px; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; color:#00a99d; margin-bottom:0; font-weight:500; text-align:center;">Woohoo! Enjoy Your Stay!</h1>
            &nbsp; -->
            </td>
        </tr>
        <tr>
        
            <td class="textContent" style="padding-top:0px; padding-left:40px; padding-right:40px;" valign="top">
            
            <h2 style="font-family: 'Helvetica','Arial',sans-serif;margin-bottom:20px;display:inline-block; color:#808184;
                font-weight:500; text-align:center; font-size:13px; line-height:20px;"><span style="width:100%; display:block; margin-bottom:10px;">
                Hi, {{ $view_data['traveller_name'] }}!
            </span>

        </td>
        </tr>

        <tr>
            <td class="textContent" style="padding-top:20px; padding-left:40px; padding-right:40px; text-align:center;" valign="top">
                Please click on this link <a href="{{ $view_data['payment_link'] }}">pay now </a> to make the payment and complete the transaction.<br>
                The link will expire in {{ $view_data['link_expire_time'] }} Hrs If your link expires before the completion of payment, please contact customer service to generate a new link.
            </td>
        </tr>


      
        <tr>
            <td class="textContent" style="padding-top:20px; padding-left:40px; padding-right:40px;" valign="top">
            <br>
            <h2 style="font-family: 'Helvetica','Arial',sans-serif;margin-top:0; font-weight:500; text-align:center; font-size:13px; line-height:20px; color:#808184;">Explore The Local Way To Stay!<br />
            Team GuestHouser</h2>
            <br /><a href="#" style="display:inline-block;text-align:center;  margin-left:-2px;font-family: 'Helvetica','Arial',sans-serif;  font-weight:500;  font-size:0.3em; vertical-align:top; color:#fff; text-decoration:none;  padding-top:0px;">Unsubscribe </a>
            &nbsp;</td>
        </tr>
           
         <tr style="font-size:initial;">
            <td class="textContent" style="text-align:center; background:#fcde66; font-family:'Helvetica','Arial',sans-serif; font-weight:300;  padding-top:10px; padding-bottom:10px; padding-left:20px;  padding-right:10px; color:#666;" valign="top" width="600">
            <div style="float:left; display:inline-block; ">
            <p style="display:inline-block; text-align:center; font-family: 'Helvetica','Arial',sans-serif; font-weight:500; color:#666;  font-size:0.6em;  line-height:2em; margin-bottom:0; vertical-align:top;  padding-top:3px; margin-top:0;">Contact us at {{GH_CONTACT_NUMBER}}
 | &copy; GuestHouser.com 2015. All rights reserved</p>
            </div>

            <div style="float:right; display:inline-block; padding-right:5px; ">
            <ul style="width:100%;  text-align:center; vertical-align:top; margin:0; padding:0; margin-top:0px; margin-right:15px;">
                <li style="float:right; margin-left:2px;list-style:none; display:inline-block;"><a href="https://instagram.com/guesthouser" style="width:23px; height:25px; display:block; background:url('{{ $view_data['social_image'] }}') no-repeat -111px -3px; color:transparent;"title='Instagram' target="_blank" >&nbsp;</a></li>
                <li style="float:right; margin-left:2px;list-style:none; display:inline-block;"><a href="https://www.youtube.com/channel/UCMEBrXZHgYsmVdsxt5Ie-nA" style="width:23px; height:25px; display:block; background:url('{{ $view_data['social_image'] }}') no-repeat -144px -3px; color:transparent;" title='Youtube' target="_blank" >&nbsp;</a></li>
                <li style="float:right; margin-left:2px;list-style:none; display:inline-block;"><a href="https://plus.google.com/+Guesthouser" style="width:23px; height:25px; display:block; background:url('{{ $view_data['social_image'] }}') no-repeat -77px -3px; color:transparent;" target="_blank" title='Google+'>&nbsp;</a></li>
                <li style="float:right; margin-left:2px; list-style:none; display:inline-block;"><a href="https://twitter.com/guesthouser" style="width:23px; height:25px; display:block; background:url('{{ $view_data['social_image'] }}') no-repeat -42px -3px; color:transparent;" target="_blank" title='Twitter'>&nbsp;</a></li>
                <li style="float:right; margin-left:2px; list-style:none; display:inline-block;"><a href="https://www.facebook.com/guesthouser?ref=br_tf" style="width:23px; height:25px; display:block; background:url('{{ $view_data['social_image'] }}') no-repeat -8px -3px; color:transparent; " target="_blank" title='Facebook'>&nbsp;</a></li>
                
            </ul>
            </div>
            </td>
        </tr>
    </tbody>
</table>
</center>
</body>
</html>
