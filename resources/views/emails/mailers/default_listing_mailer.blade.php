<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width"/>
    <link href="http://fonts.googleapis.com/css?family=Raleway:400,300,500,600,700,800" rel="stylesheet" type="text/css" />
    <title>Listing Mailers</title>
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
<table border="0" cellpadding="0" cellspacing="0" style="background:#fff; " width="600">
    <tbody>
        <tr>
            <td class="textContent" style="padding-top:50px; text-align:center; padding-left:50px;" valign="top"><a href="{{ MAILER_SITE_URL }}"><img src="{{ $view_data['logo'] }}" style=" float:left;" /> </a></td>
        </tr>

        @yield('content')

       @if(!isset($view_data['hide_sign']))
        <tr>
            <td class="textContent" style=" padding-top:60px;  padding-left:50px; padding-bottom:20px; " valign="top">
            <p style="color:#00a89b; font-size:15px; margin-bottom:0; line-height:20px; font-family: 'Raleway', sans-serif; font-weight:600;  text-align:left; padding-top:0; margin-top:0; ">Explore The Local Way To Stay!<br />
            Team GuestHouser</p>
            </td>
        </tr>
        @endif

          <tr style="font-size:initial;">
            <td class="textContent" style="text-align:center; background:#fcde66; font-family:'Helvetica','Arial',sans-serif; font-weight:300;  padding-top:10px; padding-bottom:10px; padding-left:20px;  padding-right:10px; color:#666;" valign="top" width="600">
                <div style="float:left; display:inline-block; ">
                    <p style="display:inline-block; text-align:center; font-family: 'Helvetica','Arial',sans-serif; font-weight:500; color:#666;  font-size:0.6em;  line-height:2em; margin-bottom:0; vertical-align:top;  padding-top:3px; margin-top:0;">Contact us at {{GH_CONTACT_NUMBER}}
    | &copy; GuestHouser.com 2015. All rights reserved</p>
                </div>
                <div style="float:right; display:inline-block; padding-right:5px; ">
                    <ul style="width:100%;  text-align:center; vertical-align:top; margin:0; padding:0; margin-top:0px; margin-right:15px;">
                        <li style="float:right; margin-left:2px;list-style:none; display:inline-block;">
                            <a href="https://instagram.com/guesthouser" style="width:23px; height:25px; display:block; background:url('{{ $view_data['social_image'] }}') no-repeat -111px -3px; color:transparent;"title='Instagram' target="_blank" >&nbsp;</a>
                        </li>
                        <li style="float:right; margin-left:2px;list-style:none; display:inline-block;">
                            <a href="https://www.youtube.com/channel/UCMEBrXZHgYsmVdsxt5Ie-nA" style="width:23px; height:25px; display:block; background:url('{{ $view_data['social_image'] }}') no-repeat -144px -3px; color:transparent;" title='Youtube' target="_blank" >&nbsp;</a>
                        </li>
                        <li style="float:right; margin-left:2px;list-style:none; display:inline-block;">
                            <a href="https://plus.google.com/+Guesthouser" style="width:23px; height:25px; display:block; background:url('{{ $view_data['social_image'] }}') no-repeat -77px -3px; color:transparent;" target="_blank" title='Google+'>&nbsp;</a>
                        </li>
                        <li style="float:right; margin-left:2px; list-style:none; display:inline-block;">
                            <a href="https://twitter.com/guesthouser" style="width:23px; height:25px; display:block; background:url('{{ $view_data['social_image'] }}') no-repeat -42px -3px; color:transparent;" target="_blank" title='Twitter'>&nbsp;</a>
                        </li>
                        <li style="float:right; margin-left:2px; list-style:none; display:inline-block;">
                            <a href="https://www.facebook.com/guesthouser?ref=br_tf" style="width:23px; height:25px; display:block; background:url('{{ $view_data['social_image'] }}') no-repeat -8px -3px; color:transparent; " target="_blank" title='Facebook'>&nbsp;</a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>

    </tbody>
</table>
</center>
</body>
</html>
