Hi, Your booking request ({{ $view_data['request_hash_id'] }}) for the property {{ $view_data['property_title'] }} has been approved by the host. Please make the payment within {{ $view_data['expiry_time'] }} to complete your booking.@if($view_data['payment_url'] != '') Pay here: {{ $view_data['payment_url'] }} @endif