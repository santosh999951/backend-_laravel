@extends('emails.mailers.default_listing_mailer')

@section('content')
<tr>
    <td class="textContent" style="padding-top:40px; padding-left:0px; padding-right:0;" valign="top">
    <h1 style="color:#00a89b; font-size:20px; font-size:40px; text-align:left; font-family: 'Raleway', sans-serif; margin-bottom:0; line-height:30px; font-weight:500; padding-left:50px;">Greetings!</h1>
    </td>
</tr>
<tr>
    <td class="textContent" style=" padding-top:20px;  padding-left:50px;  padding-right:50px;" valign="top">
    <p style="color:#00a89b; font-size:15px; margin-bottom:0; line-height:25px; font-family: 'Raleway', sans-serif; font-weight:600;  text-align:left; padding-top:0; margin-top:0; ">We would like to thank you for sharing details of your {{ $view_data['property_type'] }}.
    It sure looks amazing!</p>
    </td>
</tr>
<tr>
    <td class="textContent" style=" padding-top:20px; padding-left:50px;" valign="top">
    <p style="color:#808080; font-size:14px; margin-bottom:0; line-height:20px; padding-right:50px; font-family: 'Raleway', sans-serif; font-weight:500;  text-align:left; padding-top:0; margin-top:0"> Before we show your <a href="{{ MAILER_SITE_URL.'/properties/rooms/'.$view_data['property_hash_id'] }}" style="text-decoration:none; color:#14ace7; font-weight:500;">{{ $view_data['title'] }} </a> to our community of travellers, In case you’re not available during the given time period or have any questions, please reach us on {{GH_CONTACT_NUMBER}}. <br> <br> As soon as the verification process is completed, <a href="{{ MAILER_SITE_URL.'/properties/rooms/'.$view_data['property_hash_id'] }}" style="text-decoration:none; color:#14ace7; font-weight:500;">{{ $view_data['title'] }} </a> will be listed on our website as a verified and trusted GuestHouser accommodation!</p>
    </td>
</tr>

  <tr>
    <td class="textContent" style=" padding-top:60px;  padding-left:50px; padding-bottom:20px; " valign="top">
    <p style="color:#00a89b; font-size:15px; margin-bottom:0; line-height:20px; font-family: 'Raleway', sans-serif; font-weight:600;  text-align:left; padding-top:0; margin-top:0; ">Can’t wait to have you onboard!<br />
    GuestHouser Team</p>
    </td>
</tr>


@stop