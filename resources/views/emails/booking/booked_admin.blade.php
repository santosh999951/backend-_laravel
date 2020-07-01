Hi Admin, <br><br>

Congratulations!<br>

We have a new booking.<br><br>
Request Id: {{ $view_data['request_hash_id'] }}<br>
Property Id: {{ $view_data['property_hash_id'] }}<br>
Traveller Id: {{ $view_data['traveller_hash_id'] }}<br>
Traveller Name: {{ $view_data['traveller_name'] }}<br>
Sub Total: {{ $view_data['payable_amount'] }}<br>
Units: {{ $view_data['units'] }}<br>
Check-In: {{ $view_data['check_in'] }}<br>
Check-Out: {{ $view_data['check_out'] }}<br>

<br>
Login to admin panel to see details.
<br>
<a href="{{ MAILER_SITE_URL.'/admin/login' }}">click here to login</a>
<br>
<br>
Regards
Guesthouser