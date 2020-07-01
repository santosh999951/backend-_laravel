@extends('emails.mailers.default_listing_mailer')


@section('content')
<?php $updates = $view_data['updates_data']; ?>
<tr>
    <td class="textContent" style="padding-top:40px; padding-left:0px; padding-right:0;" valign="top">
    <h1 style="color:#00a89b; font-size:20px; font-size:40px; text-align:left; font-family: 'Raleway', sans-serif; margin-bottom:0; line-height:30px; font-weight:500; padding-left:50px;">Hi Business Development,</h1>
    </td>
</tr>
<tr>
    <td class="textContent" style=" padding-top:20px;  padding-left:50px; padding-right:50px; " valign="top">
    <p style="color:#00a89b; font-size:15px; margin-bottom:0; line-height:25px; font-family: 'Raleway', sans-serif; font-weight:600;  text-align:left; padding-top:0; margin-top:0; ">We received a modification request from Property <a href="{{ MAILER_SITE_URL.'/properties/rooms/'.$view_data['property_hash_id'] }}" style="text-decoration:none; color:#14ace7; font-weight:500;">{{ $view_data['title'] }} </a>. Kindly review the changes and remember to make the property live again.</p><br>
    @if(isset($updates))
    Changes:
        <table border='1' cellpadding='2' cellspacing='0' style='max-width: 540px'> 
        @for($i=0 ; $i< sizeof($updates) ; $i++)
          <tr>
            <th colspan='3' style="text-align:left"><strong>{{$updates[$i]['key']}}</strong></th>
          </tr>
          <tr>
            <td >Old</td>
            <td >
            @if(is_array($updates[$i]['old']))  
              {{ print_r($updates[$i]['old']) }}
            @else
              {{$updates[$i]['old']}}
            @endif
            </td>
          </tr>
           <tr>
            <td >New</td>
            <td >
            @if(is_array($updates[$i]['new']))  
              {{ print_r($updates[$i]['new']) }}
            @else
              {{$updates[$i]['new']}}
            @endif 
            </td>
          </tr>
          @endfor
        </table>
      @endif  
    </td>
</tr>


@stop