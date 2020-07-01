<!DOCTYPE html>
<html>
   <head>
      <title>invoice</title>
      <link href="{{ MAILER_SITE_URL.'/web/css/fonts-style.css' }}" rel="stylesheet" type='text/css' >
      <style type="text/css">
         body {
         font: 100%;
         font-size: 14px;
         color: #777779;
         overflow-y: auto;
         text-align: center;
         background: #fff;
         max-width: 600px;
         margin: 0 auto;
         overflow-x: hidden;
         font-family: "MuseoSans"; 
          }
          table{
            width: 100%;
          }
         .text-left{
          text-align: left;
         }
         .logo{
          text-align: left;
         }
         .text-right{
          text-align: right!important;
         }
         .invoice {
         width: 100%;

         float: left;
         font-family: "MuseoSans";
         margin-top: 40px;
         border: 1px solid #eaeaea;
         box-sizing: border-box;
         margin: 40px auto; } 
         .invoice .heading {
         font-size: 33px;
         font-family: "MuseoSans";
         /**/
         text-align: center;
         color: #00ba8c;
         margin-top: 35px; }
         .invoice .voucher {
         font-size: 22px;
         font-family: "MuseoSans";
         font-weight: 500;
         text-align: center;
         color: #a8a8a8;
         margin-top: 10px; }
         .invoice .status {
         font-size: 15px;
         font-family: "MuseoSans";
         font-weight: 500;
         margin-top: 10px;
         color: #858585; }
         .invoice .property-details {
         width: 100%;
         margin-top: 41px; 
         font-family: "MuseoSans";
       }
         .invoice .property-details .name {
         font-size: 15px;
         font-weight: 500;
         text-align: left;
         vertical-align: top;
         width: 25%;
         display: inline-block;
         font-family: "MuseoSans";
         color: #00ba8c; }
         .invoice .property-details .content {
         font-size: 15px;
         font-weight: 500;
         text-align: left;
         display: inline-block;
         width: 74%;
         font-family: "MuseoSans";
         color: #9b9b9b; }
         .invoice .property-book {
         text-align: left;
         width: 100%;
         margin-top: 20px;
         border-bottom: 1px solid #f2f2f2;
         padding-bottom: 10px;
          font-family: "MuseoSans";
         }

   .title-icon {
      font-family: "MuseoSans";
         display: inline-block;
         margin-right: 12px;
         width: 25px;
         height: 25px;
         background: url({{ MAILER_SITE_URL.'images/web/common/common-sprite.svg' }});
         vertical-align: top; }
      .title-icon.checkin {
         background-position: -801px -415px; }
      .title-icon.checkout {
         background-position: -801px -472px; }
         .title-icon.guest {
         background-position: -801px -521px; }
         .title-icon.room {
         background-position: -801px -571px; }
      .title-details {
         text-align: left;
         display: inline-block; }
        .title-details .title {
         font-size: 14px;
         font-weight: 500;
         font-family: "MuseoSans";
         text-align: left;
         color: #00b98b; }
         .title-details .sub-title {
         font-size: 14px;
         font-weight: 500;
         line-height: 1.21;
         text-align: left;
         font-family: "MuseoSans";
         color: #9b9b9b; }

          .price-details {
            width: 100%;
            padding:0  40px;
          }

 
 .price-details .heading {
   font-size: 17px;
   
   text-align: left;
   font-family: "MuseoSans";
   margin-top: 30px;
   margin-bottom: 25px;
   color: #00ba8c; 
 }

 .title {
         font-size: 16px;
         font-weight: 300;
         text-align: left;
         font-family: "MuseoSans";
         color: #9b9b9b; 
        padding-bottom: 5px;
       }
.subtitle {
         font-size: 14px;
         font-weight: 300;
         font-family: "MuseoSans";
         text-align: left;
         color: #9b9b9b; }
 .cost {
         font-size: 16px;
         font-weight: 300;
         font-family: "MuseoSans";
         text-align: right;
         color: #9b9b9b; }

/*.final-amount.cost {
         padding: 20px 60px 20px 20px;
         border-radius: 4px 0px 0px 4px;
         font-family: "MuseoSans";
         background-color: #00ba8c;
         font-size: 20px;
         
         text-align: right;
         color: #ffffff;
         top:0;
         margin-right: -62px;
         position: absolute;
         right: 0; }*/
       .small {
         margin-bottom: 15px; }
 .small.title {
         font-size: 14px;
         font-weight: 300;
         font-family: "MuseoSans";
         text-align: left;
         color: #9b9b9b; }
  .small.cost {
         font-size: 14px;
         font-weight: 300;
         text-align: right;
         color: #9b9b9b; }
 .small.card {
         font-size: 12px;
         margin-top: 3px;
         font-family: "MuseoSans";
         font-weight: 300;
         text-align: left;
         color: #00ba8c; }
         .invoice .note {
         font-size: 13px;
         font-family: "MuseoSans";
         font-weight: 300;
         text-align: center;
         width: 100%;
         float: left;
         color: #a4a4a4;
         padding: 0 40px;
         margin-top: 27px;
         margin-bottom: 40px; }
         .invoice .devider {
         width: 100%;
         float: left;
         height: 1px;
         margin: 10px 0 25px 0;
         background-color: #f2f2f2; }
         /*# sourceMappingURL=invoice-view.css.map */
         .date{
            font-size: 14px;
            font-weight: 500;
            text-align: right;
            color: #9b9b9b;
         }
         .upper{
          padding:0 40px 40px 40px;
          background-color: #f9f8f5;
         }
         .upper .heading{
          font-size: 16px;
          font-weight: 500;
          text-align: left;
          color: #7b7b7b;
         }
         .upper .subheading{
            font-size: 16px;
            font-weight: 300;
            margin-top: 10px;
            color: #9b9b9b;
         }
      </style>
   </head>
   <body>
      <div class="invoice">
        <table style="padding:40px 40px 0 40px ; background-color: #f9f8f5;">
          <tr>
            <td>
               <div class="logo"><img src="{{ S3_INVOICE_IMAGES_URL.'page3x.png' }}" width='201' height='50'></div>
            </td>
            <td class="text-right">
              <div class="date">{{ $view_data['formatted_created_at'] }}</div>
            </td>
          <tr>
        </table>
        <table class="upper">
          <tr>
            <td>
               <div class="heading text-left">Invoice</div>
               <div class="subheading text-left">#{{ $view_data['request_hash_id'] }}</div>
            </td>
            <td class="">
               <div class="heading text-right">Prepared for</div>
               <div class="subheading text-right">{{ $view_data['traveller_name'] }}</div>
            </td>
          <tr>
           
        </table>
         <table class="price-details" >
              <tr>
                <td>
                  <div class="heading">Invoice summary</div>
               </td>
              </tr>
              <tr>
                  <td style="padding-bottom:20px;">
                      <div class="title text-left">Base price</div>
                      <div class="subtitle text-left">for 1 night, 1 @if($view_data['room_type'] == 1) unit @else room @endif, {{ $view_data['guests'] - $view_data['extra_guests'] }}@if($view_data['guests'] > 1) guests @else guest @endif</div>
                  </td>
                  <td style="padding-bottom:20px;">
                       <div class="cost" >{{ $view_data['price']['formatted_host_per_night_price'] }}</div>
                  </td>
              </tr>
              @if($view_data['price']['extra_guest_cost'] > 0)
              <tr>
                  <td style="padding-bottom:20px;">
                        <div class="title text-left">Extra guest cost</div>
                        <div class="subtitle text-left">for 1 night, 1 room, {{ $view_data['extra_guests'] }} @if($view_data['extra_guests'] > 1) guests @else guest @endif</div>

                  </td>
                  <td style="padding-bottom:20px;">
                        <div class="cost">{{ $view_data['price']['formatted_extra_guest_cost_per_night'] }}</div>
      
                  </td>
              </tr>
              @endif
              <tr>
                <td style="padding-bottom:20px;">
                      <div class="title text-left">Price for 1 night</div>
                      <div class="subtitle text-left">{{ $view_data['price']['formatted_host_per_night_price'] }} * {{ $view_data['units'] }} @if($view_data['room_type'] == 1) @if($view_data['units'] > 1)Units @else Unit @endif @else @if($view_data['units'] > 1) rooms @else room @endif @endif @if($view_data['price']['extra_guest_cost'] > 0) + {{ $view_data['price']['formatted_extra_guest_cost_per_night'] }}@endif </div>
                </td>
                <td style="padding-bottom:20px;">
                      <div class="cost">{{ $view_data['price']['formatted_host_total_price_per_night'] }}</div>
                </td>
              </tr>
              <tr>
                <td colspan="2"><div class="devider"></div><br><br></td>
              </tr>
              <tr>
                  <td style="padding-bottom:20px;">
                    <div class="title text-left">Total amount</div>
                    <div class="subtitle text-left">{{ $view_data['price']['formatted_host_total_price_per_night'] }} x {{$view_data['total_nights']}} @if($view_data['total_nights'] > 1)nights @else night @endif </div>
                  </td>
                  <td style="padding-bottom:20px;">
                    <div class="cost">{{ $view_data['price']['formatted_host_total_price'] }}</div>
                  </td>
              </tr>
              @if($view_data['price']['formatted_coa_amount'] != '')
              <tr>
                  <td style="padding-bottom:20px;">
                    <div class="title text-left">COA fee</div>
                 </td>
                 <td style="padding-bottom:20px;">
                    <div class="cost">{{ $view_data['price']['formatted_coa_amount'] }}</div>
                 </td>
              </tr>
              @endif
             @if($view_data['all_discounts']['formatted_discount'] != '')
              <tr>
                  <td style="padding-bottom:20px;">
                    <div class="title text-left">Discount</div>
                 </td>
                 <td style="padding-bottom:20px;">
                    <div class="cost">{{ $view_data['all_discounts']['formatted_discount'] }}</div>
                 </td>
              </tr>
              @endif
              @if($view_data['all_discounts']['formatted_miles_discount'] != '')
              <tr>
                 <td style="padding-bottom:20px;">
                    <div class="title text-left">Feathers Discount</div>
                 </td>
                 <td style="padding-bottom:20px;">
                    <div class="cost">{{ $view_data['all_discounts']['formatted_miles_discount'] }}</div>
                 </td>
              </tr>
              @endif
              @if($view_data['all_discounts']['formatted_coupon_discount'] != '')
              <tr>
                 <td style="padding-bottom:20px;">
                    <div class="title text-left">Coupon Discount</div>
                 </td>
                 <td style="padding-bottom:20px;">
                    <div class="cost">{{ $view_data['all_discounts']['formatted_coupon_discount'] }}</div>
                 </td>
              </tr>
              @endif @if($view_data['all_discounts']['formatted_agent_meal_price'] != '')
                <tr>
                    <td style="padding-bottom:20px;">
                        <div class="title text-left">Meal Cost</div>
                    </td>
                    <td style="padding-bottom:20px;">
                         <div class="cost">{{ $view_data['all_discounts']['formatted_agent_meal_price'] }}</div>
                     </td>
                </tr>
                @endif @if($view_data['price']['formatted_gh_commission_amount'] != '')
                <tr>
                    <td style="padding-bottom:20px;">
                       <div class="title text-left">GH Commission</div>
                    </td>
                    <td style="padding-bottom:20px;">
                       <div class="cost">{{ $view_data['price']['formatted_gh_commission_amount'] }}</div>
                    </td>
                </tr>
                @endif
                <tr>
                   <td style="padding-bottom:20px; position:relative;">
                      <div class="title final-amount text-left">Host amount</div>
                   </td>
                   <td style="padding-bottom:20px; position:relative;">
                      <div class="final-amount cost">{{ $view_data['price']['formatted_host_amount'] }}</div>
                   </td>
                </tr>
        </table>
        <table style="background: #f9f8f5; color:#fff; padding: 28px 40px">
          <tr >
            <td style="width:15%">
                   <div style="font-size: 11px; font-weight: 500; text-align: left; color: #9b9b9b;">Subtotal</div>
                   <div class="text-left" style="color: #9b9b9b; font-size: 14px; padding-top:10px;">{{ $view_data['price']['formatted_host_amount'] }}</div>
            </td>
            @if($view_data['price']['formatted_host_gst_amount'] != '')
            <td style="width:10%;font-size:24px; color: #858585;">
               +
             </td>
            <td class="text-left" style="width:15%">
                    <div class="text-left" style="font-size: 11px; font-weight: 500; text-align: left; color: #9b9b9b;">GST</div>
                    <div class="text-left" style="color: #9b9b9b; font-size: 14px; padding-top:10px;">{{ $view_data['price']['formatted_host_gst_amount'] }}</div>
            </td>
            @endif
            <td class="text-right" style="font-size: 25px;" style="width:55%">
              <div class="text-right" style="font-size: 13px; font-weight: 500; text-align: left; color: #9b9b9b;">Booking amount</div>
                <div style="font-size: 20px;color: #858585;padding-top:10px;">{{ $view_data['price']['formatted_host_amount_with_gst'] }}</div>
             </td>
          </tr>
        </table>
        <table class="note ">
          <tr>
            <td class="text-left">*Please share your GST number otherwise Guesthouser will deduct your GST from your booking amount.</td>
          </tr>
        </table>
      </div>
   </body>
</html>