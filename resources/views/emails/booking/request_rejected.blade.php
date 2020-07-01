<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width"/>
    
    <title>Rejection</title>
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
<table border="0" cellpadding="0" cellspacing="0" style="background:#fff; " width="600">
    <tbody>
        <tr>
            <td class="textContent" style="padding-top:20px; text-align:center; padding-left:25px;" valign="top"><a href="{{ MAILER_SITE_URL }}"><img src="{{ $view_data['logo'] }}" style="float:left;width:230px;" /> </a></td>
        </tr>

        

        <tr>
            <td class="textContent" style="padding-top:0px; text-align:center; padding-left:0px; padding-right:0;" valign="top"><img src="{{ $view_data['cancel_request_image'] }}" style="display: inline-block;" /></td>
        </tr>

        <tr>
            <td class="textContent" style="padding-top:0px; padding-left:40px; padding-right:40px;" valign="top">
            <h1 style=" font-size:35px; margin-top:20px; line-height:50px; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; color:#00a99d; margin-bottom:0; font-weight:500; text-align:center;">We’re sorry! </h1>
            &nbsp;
            </td>
        </tr>
        <tr>
            <td class="textContent" style="padding-top:0px; padding-left:40px; padding-right:40px;" valign="top">

            <h2 style="font-family: 'Helvetica','Arial',sans-serif;rgin-bottom:20px; color:#808184;  font-weight:500; text-align:center; font-size:13px; line-height:20px;"><span style="width:100%; display:block; margin-bottom:10px;">Hi {{ $view_data['traveller_name'] }}!</span>
            We regret to inform you that your booking request for the following property cannot be currently fulfilled, as the property is unavailable. 
            <br/><br/>Property: <a href="{{ $view_data['property_url'] }}" style="color:#14ace7; font-weight:500; text-decoration:none;">{{ $view_data['property_title'] }}</a> <br><br>
            @if(count($view_data['similar_property']) > 0)
            However, you can continue exploring similar vacation homes! Here are our recommendations: </span></h2>
            @endif
            </td>
        </tr>
          @if(count($view_data['similar_property']) >= 3 && is_array($view_data['similar_property']))
            <tr>
                <td class="textContent" style="padding-top:20px; padding-left:40px; padding-right:40px;" valign="top">
                    @foreach($view_data['similar_property'] as $similar_property)
                        <div style="width:31.2%; display:inline-block; padding:0 5px; float:left;">
                            <a href="{{ MAILER_SITE_URL.'/properties/rooms'.$similar_property['property_hash_id'] }}" style="width:100%; height:150px; display:block; background:rgba(0, 0, 0, 0) url('{{ $similar_property['property_images'][0]['image'] }}') repeat scroll 0 0 / cover;"></a>
                            <h3 style="font-family: 'Helvetica','Arial',sans-serif; color:#808184; margin-bottom:0; margin-top:0; font-weight:500; text-align:left; font-size:12px; line-height:20px;">
                                {{ ucwords($similar_property['location']['area']) }}{{ $similar_property['location']['city'] }}, {{ $similar_property['location']['state'] }}
                            </h3>
                            <h4 style="font-family: 'Helvetica','Arial',sans-serif; color:#808184; margin-top:0; font-weight:500; text-align:left; font-size:12px; line-height:20px;">
                                {{ $similar_property['prices']['price_after_discount'] }}/-
                            </h4>
                        </div>
                    @endforeach
                </td>
            </tr>
            @endif
            
        @if($view_data['search_url'] != "")
        <tr>
            <td class="textContent" style="padding-top:30px; padding-left:40px; text-align:center;  padding-right:40px;" valign="top">
                <a href="$view_data['search_url']" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; text-decoration:none; background:#7ac697;  color:#fff;  padding:8px 25px;margin-top:0; font-weight:500; text-align:left; font-size:12px; display:inline-block; line-height:15px;" > Find Similar Properties</a>
            </td>
        </tr>
        @endif

        <tr>
            <td class="textContent" style="padding-top:20px; padding-left:40px; padding-right:40px;" valign="top">
            <br>
            <h2 style="font-family: 'Helvetica','Arial',sans-serif;margin-top:0; font-weight:500; text-align:center; font-size:13px; line-height:20px; color:#808184;">Explore The Local Way To Stay!<br />
            Team GuestHouser.</h2>
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
