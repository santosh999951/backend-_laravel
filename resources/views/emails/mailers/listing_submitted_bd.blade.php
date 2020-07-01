@extends('emails.mailers.default_listing_mailer')


@section('content')
<tr>
    <td class="textContent" style="padding-top:40px; padding-left:0px; padding-right:0;" valign="top">
    <h1 style="color:#00a89b; font-size:20px; font-size:40px; text-align:left; font-family: 'Raleway', sans-serif; margin-bottom:0; line-height:30px; font-weight:500; padding-left:50px;">Hi Business Development,</h1>
    </td>
</tr>
<tr>
    <td class="textContent" style=" padding-top:20px;  padding-left:50px; padding-right:50px; " valign="top">
    <p style="color:#00a89b; font-size:15px; margin-bottom:0; line-height:25px; font-family: 'Raleway', sans-serif; font-weight:600;  text-align:left; padding-top:0; margin-top:0; ">We received a new listing request <a href="{{ MAILER_SITE_URL.'/properties/rooms/'.$view_data['property_hash_id'] }}" style="text-decoration:none; color:#14ace7; font-weight:500; display: inline-block;">{{ $view_data['title'] }} </a>. Kindly review the property within the next 24/48 hours.</p><br>

    </td>
</tr>


@stop