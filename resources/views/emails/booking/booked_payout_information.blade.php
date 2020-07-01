<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width"/>
    <link href="http://fonts.googleapis.com/css?family=Raleway:400,300,500,600,700,800" rel="stylesheet" type="text/css" />
    <title>Welcome To Guesthouser</title>
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
<center style="width:100%;  background:#7ac697; padding:30px 0 30px 0; min-width:600px;">
<table border="0" cellpadding="0" cellspacing="0" style="background:#fff; margin:0 auto; " width="600">
    <tbody>
        <tr>
            <td class="textContent" style="padding-top:20px; text-align:center; padding-left:25px;" valign="top"><a href="{{ MAILER_SITE_URL }}"><img src="{{ $view_data['logo'] }}" style="float:left;width:230px;" /> </a></td>
        </tr>
        <tr>
            <td class="textContent" style="padding-top:0px; text-align:center; padding-left:0px; padding-right:0;" valign="top"><img src="{{ $view_data['booking_confirmed_image'] }}" style="display: inline-block;" /></td>
        </tr>
         <tr>
            <td valign="top" class="textContent" style="padding-top:20px; padding-left:40px; padding-right:40px;">
             <h1 style=" font-size:35px; margin-top:0; line-height:50px; font-family: 'Helvetica','Arial',sans-serif; color:#00a99d; margin-bottom:0; font-weight:500; text-align:center;">And it's Done</h1>
            &nbsp; 
            <h2 style="font-family: 'Raleway', sans-serif; color:#00a99d; font-weight:500; font-size:0.955em; line-height:20px; text-align:center;"> Hi! {{ $view_data['host_name'] }}, <br>
            Congratulations! Your property <a href="{{ $view_data['property_url'] }}" style="text-decoration:none; color:#14ACE8; ">{{ $view_data['property_title'] }}</a> has been booked by our explorer. <br></h2>
            
           
             

            </td>
        </tr>
        <tr>
            <td valign="top" class="textContent" style="padding-top:0px; padding-left:40px; padding-right:40px;">
            
            <h2 style="font-family: 'Raleway', sans-serif; color:#00a99d; font-weight:500; font-size:0.955em; line-height:20px; text-align:center;"> 
          
           To complete the payment process.
           </h2>
            
           
             

            </td>.
        </tr>

         <tr>
            <td valign="top" class="textContent" style="padding-top:40px; padding-left:40px; padding-right:40px; text-align:center;">
             <a href="{{ $view_data['payout_url'] }}" style="text-decoration:none; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; color:#fff; font-size:0.9em; padding:8px 20px; background:#7bc697; display:inline-block;line-height:15px;"> Add Payout Details</a>
            </td>
        </tr>

         <tr>
            <td class="textContent" style="padding-top:20px; padding-left:40px; padding-right:40px;" valign="top">
            <h2 style="font-family: 'Helvetica','Arial',sans-serif;n-top:0; font-weight:500; text-align:center; font-size:13px; line-height:20px; color:#808184;">Explore The Local Way To Stay!<br />
            - Team GuestHouser</h2>
            <br />
            &nbsp;</td>
        </tr>
        <tr style="font-size:initial;">
            <td class="textContent" style="text-align:center; background:#fcde66; font-family:'Helvetica','Arial',sans-serif; font-weight:300;  padding-top:10px; padding-bottom:10px; padding-left:20px;  padding-right:10px; color:#666;" valign="top" width="600">
            <div style="float:left; display:inline-block; ">
            <p style="display:inline-block; text-align:center; font-family: 'Helvetica','Arial',sans-serif; font-weight:600; color:#666;  font-size:0.6em;  vertical-align:top;  padding-top:5px; margin-top:0;">Contact us at {{GH_CONTACT_NUMBER}}
 | &copy; GuestHouser.com 2015. All rights reserved |</p>
            <a href="#" style="display:inline-block;text-align:center;  margin-left:-2px;font-family: 'Helvetica','Arial',sans-serif;  font-weight:600; color:#0071bc;  font-size:0.6em; vertical-align:top;  padding-top:5px;">Unsubscribe </a></div>

            <div style="float:right; display:inline-block; ">
            <ul style="width:100%;  text-align:center; vertical-align:top; margin:0; padding:0; margin-top:0px; margin-right:15px;">
                <li style=" list-style:none; display:inline-block; margin-right:5px; margin-left:0;"><a href="https://www.facebook.com/guesthouser?ref=br_tf" style="width:23px; height:25px; display:block; background:url('https://guesthouser.com/images/project_mailers/social-icon.png') no-repeat -5px -3px; color:transparent; ">&nbsp;</a></li>
                <li style=" list-style:none; display:inline-block; margin-right:5px; margin-left:0;"><a href="https://twitter.com/guesthouser" style="width:23px; height:25px; display:block; background:url('https://guesthouser.com/images/project_mailers/social-icon.png') no-repeat -39px -3px; color:transparent;">&nbsp;</a></li>
                <li style="list-style:none; display:inline-block; margin-left:0;"><a href="https://plus.google.com/+Guesthouser" style="width:23px; height:25px; display:block; background:url('https://guesthouser.com/images/project_mailers/social-icon.png') no-repeat -73px -3px; color:transparent;">&nbsp;</a></li>
            </ul>
            </div>
            </td>
        </tr>
    </tbody>
</table>
</center>
</body>
</html>
