<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width"/>
    <link href="http://fonts.googleapis.com/css?family=Raleway:400,300,500,600,700,800" rel="stylesheet" type="text/css" />
    <title>Guesthouser Feathers</title>
    <style type="text/css">/*////// RESET STYLES //////*/
            body, #bodyTable, #bodyCell{height:100% !important; margin:0; padding:0; width:100% !important;}
            table{border-collapse:collapse;}
            img, a img{border:0; outline:none; text-decoration:none;}
            h1, h2, h3, h4, h5, h6{margin:0; padding:0;}
            p{margin: 0em 0;}

            /*////// CLIENT-SPECIFIC STYLES //////*/
            .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail/Outlook.com to display emails at full width. */
            .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div{line-height:100%;} /* Force Hotmail/Outlook.com to display line heights normally. */
            table, td{mso-table-lspace:0pt; mso-table-rspace:0pt;} /* Remove spacing between tables in Outlook 2007 and up. */
            #outlook a{padding:0;} /* Force Outlook 2007 and up to provide a "view in browser" message. */
            img{-ms-interpolation-mode: bicubic;} /* Force IE to smoothly render resized images. */
            body, table, td, p, a, li, blockquote{-ms-text-size-adjust:100%; -webkit-text-size-adjust:100%;} /* Prevent Windows- and Webkit-based mobile platforms from changing declared text sizes. */

            /*////// FRAMEWORK STYLES //////*/
            .flexibleContainerCell{}


            .flexibleImage{height:auto;}
            .bottomShim{padding-bottom:20px;}
            .imageContent, .imageContentLast{padding-bottom:20px;}
            .nestedContainerCell{padding-top:20px; padding-Rignewht:20px; padding-Left:20px;}

            /*////// GENERAL STYLES //////*/
            body, #bodyTable{background-color:#F5F5F5;}
            #bodyCell{padding-top:40px; padding-bottom:40px;}
            #emailBody{background-color:#FFFFFF; border:0; border-collapse:separate; border-radius:4px;}
            h1, h2, h3, h4, h5, h6{color:#202020; font-family:Helvetica; font-size:20px; line-height:125%; text-align:Left;}
            .textContent, .textContentLast{color:#404040; font-family:Helvetica; font-size:16px; line-height:125%; text-align:Left; padding:0 35px;}
            .textContent a, .textContentLast a{color:#2C9AB7; text-decoration:underline;}
            .nestedContainer{background-color:#E5E5E5; border:1px solid #CCCCCC;}
            .emailButton{background-color:#2C9AB7; border-collapse:separate; border-radius:4px;}
            .buttonContent{color:#FFFFFF; font-family:Helvetica; font-size:18px; font-weight:bold; line-height:100%; padding:15px; text-align:center;}
            .buttonContent a{color:#FFFFFF; display:block; text-decoration:none;}
            .emailCalendar{background-color:#FFFFFF; border:1px solid #CCCCCC;}
            .emailCalendarMonth{background-color:#2C9AB7; color:#FFFFFF; font-size:16px; font-weight:bold; padding-top:10px; padding-bottom:10px; text-align:center;}
            .emailCalendarDay{color:#2C9AB7;  font-size:60px; font-weight:bold; line-height:100%; padding-top:20px; padding-bottom:20px; text-align:center;}
    </style>
</head>
<body>
<center style="width:100%;  background:#7ac697; padding:50px 0; min-width:700px; ">
<table border="0" cellpadding="0" cellspacing="0" style="background-image:url('{{STATIC_BASE_URL}}images/project_mailers/feathers/feathers_notification/notification_bg.png'); background-repeat:no-repeat; background-color:#fff; " width="600">
    <tbody>
        <tr>
            <td class="textContent" style="padding-top:20px; text-align:center; padding-left:30px;" valign="top"><a href="http://www.guesthouser.com/"><img src="http://guesthouser.com/images/logo/project_mailer/guesthouser.png" style=" float:left;" /> </a></td>
        </tr>
        <tr>
            <td class="textContent" style="padding-top:10px; padding-left:0px; padding-right:0; text-align:center;" valign="top">
            <img src="{{STATIC_BASE_URL}}images/project_mailers/feathers/feathers_notification/selfie.png" alt="Selfie" style="display:inline-block;" />
            </td>
        </tr>
        <tr>
            <td class="textContent" style="padding-top:10px; padding-left:0px; padding-right:0;" valign="top">
            <h1 style="color:#00a89b; font-size:28px; text-align:center; font-family: 'Raleway', sans-serif; margin-bottom:0; line-height:30px; font-weight:600;  ">Congratulations! <br>
You have earned {{$feathers}} feathers!</h1>
            </td>
        </tr>
        <tr>
            <td class="textContent" style=" padding-top:15px; text-align:center; padding-left:0; padding-right:0;  " valign="top">
            <p style="color:#00a89b; font-size:14px; margin-bottom:0; line-height:18px; font-family: 'Raleway', sans-serif; font-weight:500;  text-align:center; padding-top:0; margin-top:0; padding-left:85px; padding-right:85px;"> A little birdy told us that {{$messages}}! Bravo! Well done! 
Here’s {{$feathers}} Feathers for being awesome! </p><br> 
            <a href="{{URL::to('feathers/invite')}}" style="color:#fff; font-size:12px; display:inline-block; margin-bottom:0; line-height:20px; font-family: 'Raleway', sans-serif; font-weight:500; margin-right:15px; text-decoration:none; background:#00a99d; padding:3px 20px;">Earn more feathers</a>
            <a href="{{URL::to('/feathers/invite?showlogin=1')}}" style="color:#fff; font-size:12px; display:inline-block; margin-bottom:0; line-height:20px; font-family: 'Raleway', sans-serif; font-weight:500; margin-right:30px; text-decoration:none; background:#00a99d; padding:3px 20px;">Your total feathers</a>
            </td>
        </tr>
        
        <tr>
            <td class="textContent" style=" padding-top:20px;  padding-left:40px; padding-right:40px; padding-bottom:70px; " valign="top">
               
            <p style="color:#00a89b; font-size:15px; display:block; margin-bottom:0; line-height:20px; font-family: 'Raleway', sans-serif; font-weight:600;  text-align:center; padding-top:0px; margin-top:0; ">Explore The Local Way To Stay!<br>
Team GuestHouser </p>
            </td>
        </tr>

        
      
        <tr>
             
            <td class="textContent" style="text-align:center; background:#fcde66; font-family: 'Raleway', sans-serif; font-weight:300;  padding-top:20px; padding-bottom:20px; padding-left:20px;  padding-right:20px; color:#666;" valign="top" width="600">
                <ul style="width:100%;  text-align:center; vertical-align:top; margin:0; padding:0;">
                <li style=" list-style:none; display:inline-block; margin-right:10px; margin-left:0;"><a href="https://www.facebook.com/guesthouser?ref=br_tf" style="width:30px; height:30px; display:block; background:url('{{STATIC_BASE_URL}}images/project_mailers/social-icons.png') no-repeat -6px -3px; color:transparent; "> </a></li>
                <li style=" list-style:none; display:inline-block; margin-right:10px; margin-left:0;"><a href="http://www.twitter.com/guesthousersite" style="width:30px; height:30px; display:block; background:url('{{STATIC_BASE_URL}}images/project_mailers/social-icons.png') no-repeat -51px -3px; color:transparent;"> </a></li>
                <li style="list-style:none; display:inline-block; margin-left:0;"><a href="https://plus.google.com/+Guesthouser" style="width:30px; height:30px; display:block; background:url('{{STATIC_BASE_URL}}images/project_mailers/social-icons.png') no-repeat -95px -3px; color:transparent;"> </a></li>
            </ul>
            <p style="display:inline-block; text-align:center; font-family: 'Raleway', sans-serif; font-weight:600; color:#666;  font-size:0.7em;  vertical-align:top;  padding-top:10px; margin-top:0;">Contact us at {{GH_CONTACT_NUMBER_DISPLAY}} (Toll Free) © GuestHouser.com 2014 All rights reserved. |  </p>
            <a href="#" style="display:inline-block;text-align:center;  margin-left:-7px;font-family: 'Raleway', sans-serif;  font-weight:600; color:#0071bc;  font-size:0.7em; vertical-align:top;  padding-top:10px;">Unsubscribe </a>

           
            </td>
        </tr>
        <tr>
        </tr>
    </tbody>
</table>
</center>
</body>
</html>
