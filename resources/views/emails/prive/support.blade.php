
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width"/>
    <link href="https://fonts.googleapis.com/css?family=Poppins:500&display=swap" rel="stylesheet">
    <title>Properly Password</title>
    
    <style>
        body{font-family: 'Poppins' !important;}
        .contact-us{position: absolute;bottom: 10px;text-align: center;width: 100%;margin-top: 260px;}
        .main-content{background: rgb(255, 255, 255);border-radius: 4px;height: 636px;position: relative;}
        .main-center{width:100%; padding:30px 0 30px 0;}
        .main-table{background-color: #f3f3f3;width: 100%;height: 100%;font-family: 'Poppins' !important;}
        .header{width: 656px;height: 60px;background: rgb(6, 18, 35);border-radius: 0px;}
        .strip{width: 8px;height: 60px;background: rgb(42, 68, 105);border-radius: 0px;float: left;}
        .header-text{padding: 20px 25px 20px;}
        .h-text-1{    width: 85px;height: 20px;color: #afafaf;font-weight: 500;text-transform: uppercase;letter-spacing: 2.63px;}
        .h-text-2{width: 82px;height: 20px;color: rgb(255, 255, 255);font-weight: 500;text-transform: uppercase;letter-spacing: 2.63px;}
        .image-icon{ height: 74px;width: 90px;padding-top: 67px;object-fit: contain;}
        .heading-1{color: rgba(155, 155, 155, 0.3);font-weight: 400;text-align: center;letter-spacing: 0px;}
        .heading-2{color: rgb(13, 13, 13);font-weight: 400;text-align: center;letter-spacing: 0px;line-height: 23px;padding: 0px 25px;}
        .link{color: rgb(0, 153, 250);font-size: 14px;padding: 0px 25px;font-weight: 500;}
        .contact-us span{color: rgb(155, 155, 155);font-weight: 500;}

        @media screen and (min-width: 480px) {
            .main-content{margin: 16px 16px 16px}
            .header-text{font-size: 12px;}
            .heading-1{font-size: 26px;}
            .heading-2{font-size:12px;}
            .contact-us{font-size: 8px;}
        }
        @media screen and (min-width: 600px) {
            .main-content{margin: 24px 24px 24px}
            .header-text{font-size: 14px;}
            .heading-1{font-size: 28px;}
            .heading-2{font-size:14px;}
            .contact-us{font-size: 9px;}
          
        }
        @media screen and (min-width: 800px) {
            .main-content{margin: 32px 32px 32px}
            .header-text{font-size: 14px;}
            .heading-1{font-size: 32px;}
            .heading-2{font-size:14px;}
            .contact-us{font-size: 10px;}
          
        }
    </style>
</head>
<body style="font-family: 'Poppins' !important;">

<center class="main-center">
<table class="main-table" border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>

<td class="header" valign="top" style="font-family: 'Poppins' !important;">
    
<div class="strip"></div>
<div class="header-text"><span class="h-text-1">Properly </span><span class="h-text-2">Support</span></div></td>
</tr>
<tr>
<td style="background-color: #f3f3f3;" valign="top" align="center">
<div class="main-content" style="font-family: 'Poppins' !important;">
    <img src="{{ $view_data['support']}}" class="image-icon" align="middle">
<h1 class="heading-1">Support</h1>
Hi Properly Support!				
<br>You have a query-<br>
 <h2 class="heading-2">{!! $view_data['message'] !!} </h2>
 
<div class="contact-us"><span>Contact us {{GH_CONTACT_NUMBER}} | © GuestHouser.com 2015. All rights reserved</span></div>

    </div>
</td>
</tr>

</tbody>
</table>
</center>
</body>

</html>
