<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width"/>
        <link href='https://fonts.googleapis.com/css?family=Raleway:400,100,200,300,500,600,700,800,900' rel='stylesheet' type='text/css'>
        <title>Welcome To Guesthouser</title>
        <style type="text/css">
            /*////// RESET STYLES //////*/
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
            .nestedContainerCell{padding-top:20px; padding-Right:20px; padding-Left:20px;}

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
            .emailCalendarMonth{background-color:#2C9AB7; color:#FFFFFF; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:bold; padding-top:10px; padding-bottom:10px; text-align:center;}
            .emailCalendarDay{color:#2C9AB7; font-family:Helvetica, Arial, sans-serif; font-size:60px; font-weight:bold; line-height:100%; padding-top:20px; padding-bottom:20px; text-align:center;}

        </style>
    </head>
    <body>
    <?php
    $name = isset($name) ? $name : 'Vishal';
?>
        <center>
            <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
                <tr>
                    <td align="center" valign="top" id="bodyCell">
                        <!-- EMAIL CONTAINER // -->
                        <!--
                            The table "emailBody" is the email's container.
                            Its width can be set to 100% for a color band
                            that spans the width of the page.
                        -->
                        <table border="0" cellpadding="0" cellspacing="0" width="670" id="emailBody">


                            <!-- MODULE ROW // -->
                            <!--
                                To move or duplicate any of the design patterns
                                in this email, simply move or copy the entire
                                MODULE ROW section for each content block.
                            -->
                            <tr>
                                <td align="center" valign="top">
                                    <!-- CENTERING TABLE // -->
                                    <!--
                                        The centering table keeps the content
                                        tables centered in the emailBody table,
                                        in case its width is set to 100%.
                                    -->
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td align="center" valign="top">
                                                <!-- FLEXIBLE CONTAINER // -->
                                                <!--
                                                    The flexible container has a set width
                                                    that gets overridden by the media query.
                                                    Most content tables within can then be
                                                    given 100% widths.
                                                -->
                                                <table border="0" cellpadding="0" cellspacing="0" width="670" class="flexibleContainer">
                                                    <tr>
                                                        <td align="center" valign="top" width="700" class="flexibleContainerCell">


                                                            <!-- CONTENT TABLE // -->
                                                            <!--
                                                                The content table is the first element
                                                                that's entirely separate from the structural
                                                                framework of the email.
                                                            -->
                                                            <table border="0" cellpadding="0" cellspacing="0" width="100%"  style="background-image:url({{WEBSITE_URL.'/images/mailer_images/reset-password/background.png'}}); background-repeat:no-repeat; background-position:20px 55px; background-color:#f1f1f1; position:relative; ">
                                                                    

                                                                    <tr>
                                                                        <td valign="top" class="textContent" style="padding-top:30px; padding-right:30px;">
                                                                            <img src="{{WEBSITE_URL.'/images/logo/project_mailer/logo.png'}}" style="float:right;">
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td valign="top" class="textContent" style="padding-top:20px; padding-left:40px; padding-right:40px;">
                                                                        
                                                                        <h1 style="font-family: 'Raleway', sans-serif; color:#00a99d; font-weight:500; font-size:1em; line-height:20px;">
Hello {{$name}},<br><br><br> This is to confirm that your account has been deactivated<sup> *</sup>. We hope you had an amazing experience as a GuestHouser. When you are ready to be a GuestHouser again, reactivating your account is easy! All you have to do is log in with your previous credentials. <br><sup>*</sup>If your account was deactivated without your consent, please contact connect@guesthouser.com for assistance.<br><br>


Team GuestHouser</h1>

<h2 style="font-family: 'Raleway', sans-serif; color:#00a99d; font-weight:600; font-size:1.1em; line-height:30px;"> Thank You!</h2>
                                                                        </td>
                                                                    </tr>


                                                                    

                                                                
                                                           
                                                                            <tr>
                                                                <td valign="top" width="600"class="textContent" style="text-align:center;  background:url({{WEBSITE_URL.'/images/mailer_images/new-booking/newbooking-footer.png'}}); background-repeat:no-repeat; background-size:contain; font-family: 'Raleway', sans-serif; font-weight:600; height:320px;   color:#666; margin-top:20px;">
                                                                    
                                                                </td>
                                                            </tr>
                                                                        </table>
                                                                        
                                                                    </td>
                                                                    </tr>



                                                            </table>
                                                            <!-- // CONTENT TABLE -->


                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- // CENTERING TABLE -->

                                    
                                </td>
                            </tr>
                            <!-- // MODULE ROW -->


        
                        </table>
                        <!-- // EMAIL CONTAINER -->
                    </td>
                </tr>
            </table>
        </center>
    </body>
</html>
