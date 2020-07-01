<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width"/>
    
    <title>Booking Cancelled Host</title>
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
            .textContent, .textContentLast{color:#404040; font-family:'Helvetica','Arial',sans-serif; font-size:16px; line-height:125%; text-align:Left; padding:0 35px;}
            .textContent a, .textContentLast a{color:#2C9AB7; text-decoration:underline;}
            .nestedContainer{background-color:#E5E5E5; border:1px solid #CCCCCC;}
            .emailButton{background-color:#2C9AB7; border-collapse:separate; border-radius:4px;}
            .buttonContent{color:#FFFFFF; font-family:'Helvetica','Arial',sans-serif; font-size:18px; font-weight:bold; line-height:100%; padding:15px; text-align:center;}
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
            <td class="textContent" style="padding-top:20px; text-align:center; padding-left:25px;" valign="top"><a href="{{$base_url}}"><img src="{{$base_url}}/images/siteLogo_2x.png" style="float:left;width:230px;" /> </a></td>
        </tr>

        

        <tr>
            <td class="textContent" style="padding-top:0px; text-align:center; padding-left:0px; padding-right:0;" valign="top"><img src="{{$base_url}}/images/project_mailers/bookings/reject_graphic.png" style="display: inline-block;" /></td>
        </tr>

        <tr>
            <td class="textContent" style="padding-top:20px; padding-left:40px; padding-right:40px;" valign="top">
            <h1 style=" font-size:35px; margin-top:20px; line-height:50px; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; color:#00a99d; margin-bottom:0; font-weight:500; text-align:center;">Oops! We are sorry!</h1>
            &nbsp;
            </td>
        </tr>
        <tr>
            <td class="textContent" style="padding-top:0px; padding-left:40px; padding-right:40px;" valign="top">

            <h2 style="font-family: 'Helvetica','Arial',sans-serif;margin-bottom:20px;display:inline-block; color:#808184;  font-weight:500; text-align:center; font-size:13px; line-height:20px;"><span style="width:100%; display:block; margin-bottom:10px;">Hi, {{$to_name}}!</span>
            We are sorry to inform you that booking request for property has been cancelled by the traveller. <br><br>
           Property:<a href="{{ $property_link }}" style="color:#14ace7; text-decoration:none;">{{ $property_title }}</a></h2>
            </td>
        </tr>

        <tr>
            <td class="textContent" style="padding-top:20px; padding-left:40px; padding-right:40px; text-align:center;" valign="top">
            
            

            <div style="display:inline-block; margin:0; padding:0; vertical-align:top; overflow:hidden; clear:both;width:100%;">
                 <div style="display:inline-block; vertical-align:top; padding:0; margin:0; max-width:50%;">
                     <a href="" style="display:block; height:140px; width:210px; background:url('{{$image}}'); background-size:cover;"></a>
                 </div>
                 <div style="display:inline-block; vertical-align:top; padding:0; margin:0; max-width:50%; padding-left:20px;">
                     <h1 style="display:block;  font-size:15px; font-weight:400; color:#808184; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; text-transform:capitalize; word-wrap:break-word; padding:0; margin:0;"><a href="{{ $property_link }}" style="color:#14ace7; text-decoration:none;">{{ $property_title }}</a><h1>
                     <h1 style="display:block; font-size:15px; font-weight:400; color:#808184; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; text-transform:capitalize; word-wrap:break-word; padding-top:3px;">Booking ID: {{ $booking_id }}</h1>
                     <h2 style="display:block; font-size:12px; font-weight:400; color:#808184; font-family: 'Helvetica','Arial',sans-serif; text-transform:capitalize; word-wrap:break-word; padding-top:10px;"> {{ date('d-M-Y', strtotime($check_in)) }} to {{ date('d-M-Y', strtotime($check_out)) }}</h2>
                     <h2 style="display:block; font-size:12px; font-weight:400; color:#808184; font-family: 'Helvetica','Arial',sans-serif; text-transform:capitalize; word-wrap:break-word; padding-top:3px;"> 
                         @if($guests > 1)
                            Guests: {{ $guests  }} 
                        @else
                            Guest: {{ $guests }}
                        @endif
                    </h2>
                 </div>
            </div>
                
            </td>
        </tr>

        <tr>
            <td class="textContent" style="padding-top:40px; padding-left:40px; padding-right:40px; text-align:center;" valign="top">
                <a href="{{ $link }}" style="text-decoration:none; display:inline-block; color:#fff; font-size:0.9em; padding:8px 20px; background:#7bc697; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;"> Click to View Details</a> 
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
            <p style="display:inline-block; text-align:center; font-family: 'Helvetica','Arial',sans-serif; font-weight:500; color:#666;  font-size:0.6em;  line-height:2em; margin-bottom:0; vertical-align:top;  padding-top:3px; margin-top:0;">Contact us at 1800-102-0404 (Toll Free) | &copy; GuestHouser.com 2015. All rights reserved</p>
            </div>

            <div style="float:right; display:inline-block; padding-right:5px; ">
            <ul style="width:100%;  text-align:center; vertical-align:top; margin:0; padding:0; margin-top:0px; margin-right:15px;">
                <li style="float:right; margin-left:2px;list-style:none; display:inline-block;"><a href="https://instagram.com/guesthouser" style="width:23px; height:25px; display:block; background:url('{{$base_url}}/images/project_mailers/socials.png') no-repeat -111px -3px; color:transparent;"title='Instagram' target="_blank" >&nbsp;</a></li>
                <li style="float:right; margin-left:2px;list-style:none; display:inline-block;"><a href="https://www.youtube.com/channel/UCMEBrXZHgYsmVdsxt5Ie-nA" style="width:23px; height:25px; display:block; background:url('{{$base_url}}/images/project_mailers/socials.png') no-repeat -144px -3px; color:transparent;" title='Youtube' target="_blank" >&nbsp;</a></li>
                <li style="float:right; margin-left:2px;list-style:none; display:inline-block;"><a href="https://plus.google.com/+Guesthouser" style="width:23px; height:25px; display:block; background:url('{{$base_url}}/images/project_mailers/socials.png') no-repeat -77px -3px; color:transparent;" target="_blank" title='Google+'>&nbsp;</a></li>
                <li style="float:right; margin-left:2px; list-style:none; display:inline-block;"><a href="https://twitter.com/guesthouser" style="width:23px; height:25px; display:block; background:url('{{$base_url}}/images/project_mailers/socials.png') no-repeat -42px -3px; color:transparent;" target="_blank" title='Twitter'>&nbsp;</a></li>
                <li style="float:right; margin-left:2px; list-style:none; display:inline-block;"><a href="https://www.facebook.com/guesthouser?ref=br_tf" style="width:23px; height:25px; display:block; background:url('{{$base_url}}/images/project_mailers/socials.png') no-repeat -8px -3px; color:transparent; " target="_blank" title='Facebook'>&nbsp;</a></li>
                
            </ul>
            </div>
            </td>
        </tr>
    </tbody>
</table>
</center>
</body>
</html>