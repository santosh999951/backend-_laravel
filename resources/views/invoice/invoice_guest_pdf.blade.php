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
         max-width: 600px;
         margin: 0 auto;
         overflow-x: hidden;
         font-family: "MuseoSans" !important; 
          }
         .text-left{
          text-align: left;
         }
  
         .invoice {
         width: 100%;
         float: left;
         font-family: "MuseoSans" !important;
         margin-top: 40px;
         border: 1px solid #eaeaea;
         padding: 40px 60px;
         box-sizing: border-box;
         margin: 40px auto; } 
         
         .invoice .heading {
         font-size: 33px;
         font-family: "MuseoSans" !important;
         text-align: center;
         color: #00ba8c;
         margin-top: 35px; }
         .invoice .voucher {
         font-size: 22px;
         font-family: "MuseoSans" !important;
         font-weight: 500;
         text-align: center;
         color: #a8a8a8;
         margin-top: 10px; }
         .invoice .status {
         font-size: 15px;
         font-family: "MuseoSans" !important;
         font-weight: 500;
         margin-top: 10px;
         color: #858585; }
         .invoice .property-details {
         width: 100%;
         margin-top: 41px; 
         font-family: "MuseoSans" !important;
       }
         .invoice .property-details .name {
         font-size: 15px;
         font-weight: 500;
         text-align: left;
         vertical-align: top;
         width: 25%;
         display: inline-block;
         font-family: "MuseoSans" !important;
         color: #00ba8c; }
         .invoice .property-details .content {
         font-size: 15px;
         font-weight: 500;
         text-align: left;
         display: inline-block;
         width: 74%;
         font-family: "MuseoSans" !important;
         color: #9b9b9b; }
         .invoice .property-book {
         text-align: left;
         width: 100%;
         margin-top: 20px;
         border-bottom: 1px solid #f2f2f2;
         padding-bottom: 10px;
          font-family: "MuseoSans" !important;
         }

   .title-icon {
      font-family: "MuseoSans" !important;
         display: inline-block;
         margin-right: 12px;
         width: 25px;
         height: 25px;
         /*background: url('{{STATIC_BASE_URL}}images/web/common/common-sprite.svg?v=3');*/
         vertical-align: top; }
      .title-icon.checkin {
         }
      .title-icon.checkout {
         }
         .title-icon.guest {
          }
         .title-icon.room {
          }
      .title-details {
         text-align: left;
         display: inline-block; 
         font-family: "MuseoSans" !important;
       }
        .title-details .title {
         font-size: 14px;
         font-weight: 500;
         font-family: "MuseoSans" !important;
         text-align: left;
         color: #00b98b; }
         .title-details .sub-title {
         font-size: 12px;
         font-weight: 500;
         line-height: 1.21;
         text-align: left;
         font-family: "MuseoSans" !important;
         color: #9b9b9b; }

          .price-details {
            width: 100%;
            font-family: "MuseoSans" !important;
          }

 
 .price-details .heading {
   font-size: 17px;
   text-align: left;
   font-family: "MuseoSans" !important;
   margin-top: 30px;
   margin-bottom: 25px;
   color: #00ba8c; 
 }

 .title {
         font-size: 16px;
         font-weight: 300;
         text-align: left;
         font-family: "MuseoSans" !important;
         color: #9b9b9b; 
        padding-bottom: 5px;
       }
.subtitle {
         font-size: 14px;
         font-weight: 300;
         font-family: "MuseoSans" !important;
         text-align: left;
         color: #9b9b9b; }
 .cost {
         font-size: 16px;
         font-weight: 300;
         font-family: "MuseoSans" !important;
         text-align: right;
         color: #9b9b9b; }
  .final-amount{

    font-family: "MuseoSans" !important;

  }
.final-amount.title {
         line-height: 60px; 
         font-family: "MuseoSans" !important;
       }
.final-amount.cost {
         padding: 20px 60px 20px 20px;
         border-radius: 4px 0px 0px 4px;
         font-family: "MuseoSans" !important;
         background-color: #00ba8c;
         font-size: 20px;
         text-align: right;
         color: #ffffff;
         top:0;
         margin-right: -62px;
         position: absolute;
         right: 0; }
       .small {
         margin-bottom: 15px; 
         font-family: "MuseoSans" !important;
       }
 .small.title {
         font-size: 14px;
         font-weight: 300;
         font-family: "MuseoSans" !important;
         text-align: left;
         color: #9b9b9b; }
  .small.cost {
         font-size: 14px;
         font-weight: 300;
         text-align: right;
         color: #9b9b9b; 
       font-family: "MuseoSans" !important;}
 .small.card {
         font-size: 12px;
         margin-top: 3px;
         font-family: "MuseoSans" !important;
         font-weight: 300;
         text-align: left;
         color: #00ba8c; }
         .invoice .note {
         font-size: 13px;
         font-family: "MuseoSans" !important;
         font-weight: 300;
         text-align: center;
         width: 100%;
         float: left;
         color: #a4a4a4;
         margin-top: 40px; }
         .invoice .devider {
         width: 100%;
         float: left;
         height: 1px;
         margin: 10px 0 25px 0;
         background-color: #f2f2f2; 
         font-family: "MuseoSans" !important;
       }
         /*# sourceMappingURL=invoice-view.css.map */
      </style>
   </head>
   <body>
    <base href="{{ MAILER_SITE_URL }}">
      <div class="invoice">
         <div ><img src="{{ S3_INVOICE_IMAGES_URL.'page3x.png' }}" width='201' height='50'></div>
         <div class="heading" style="font-family: 'MuseoSans';"><span class="voucher-h">Booking voucher</span></div>
         <div class="voucher">#{{ $view_data['request_hash_id'] }}</div>
         @if($view_data['booking_status'] == BOOKED)
         <div class="status">Your booking is confirmed.</div>
         @else
         <div class="status">Required Content for other req option.</div>
         @endif
         <div class="property-details">
            <div class="name">Property name :</div>
            <div class="content">{{ $view_data['property_title'] }}</div>
         </div>

         <table class="property-book">
          <tr>
            <td style="padding-bottom:25px;width:40%">
               <div class="title-icon checkin"><img src="{{ S3_INVOICE_IMAGES_URL.'checkin.png' }}" width='25' height='25'></div>
               <div class="title-details">
                  <div class="title text-left">Check In</div>
                  <div class="sub-title">{{ $view_data['formatted_from_date'] }}</div>
               </div>
            </td>
             <td style="padding-bottom:25px; width:40%">
               <div class="title-icon checkout"><img src="{{ S3_INVOICE_IMAGES_URL.'checkout.png' }}" width='25' height='25'></div>
               <div class="title-details">
                  <div class="title text-left">Check Out</div>
                  <div class="sub-title">{{ $view_data['formatted_to_date'] }}</div>
               </div>
            </td>
          </tr>

          <tr>
            <td style="padding-bottom:25px; width:40%">
               <div class="title-icon guest"><img src="{{ S3_INVOICE_IMAGES_URL.'user.png' }}" width='25' height='25'></div>
               <div class="title-details">
                  <div class="title text-left">@if($view_data['guests'] > 1) Guests @else Guest @endif</div>
                  <div class="sub-title">@if($view_data['extra_guests'] > 0) {{ $view_data['guests'] - $view_data['extra_guests'] }}  + {{ $view_data['extra_guests'] }} @else {{ $view_data['guests'] }}@endif</div>
               </div>
            </td>
            <td style="padding-bottom:25px; width:40%">
               <div class="title-icon room"><img src="{{ S3_INVOICE_IMAGES_URL.'home.png' }}" width='25' height='25'></div>
               <div class="title-details">
                  <div class="title text-left">@if($view_data['room_type'] == 1) @if($view_data['units'] > 1) Units/Rooms @else Unit/room @endif @else @if($view_data['units'] > 1) Rooms @else Room @endif @endif</div>
                  <div class="sub-title">{{ $view_data['units'] }}</div>
               </div>
            </td>
          </tr>
         </table>

         <table class="price-details" >
              <tr>
                <td>
                  <div class="heading" style="font-family: 'MuseoSans';">Price breakup</div>
               </td>
              </tr>
              <tr>
                  <td style="padding-bottom:20px;">
                      <div class="title text-left">Base price</div>
                      <div class="subtitle text-left">for 1 night, 1 room, 2 guests</div>
                  </td>
                  <td style="padding-bottom:20px;">
                       <div class="cost" >{{ $view_data['price']['formatted_per_night_price'] }}</div>
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
                      <div class="subtitle text-left">{{ $view_data['price']['formatted_per_night_price'] }} X {{ $view_data['units'] }} @if($view_data['room_type'] == 1) @if($view_data['units'] > 1)Units @else Unit @endif @else @if($view_data['units'] > 1) rooms @else room @endif @endif @if($view_data['price']['extra_guest_cost'] > 0) + {{ $view_data['price']['formatted_extra_guest_cost_per_night'] }}@endif </div>
                </td>
                <td style="padding-bottom:20px;">
                      <div class="cost">{{ $view_data['price']['formatted_per_night_all_unit_with_extra_guest_cost'] }}</div>
                </td>
              </tr>
              <tr>
                <td colspan="2"><div class="devider"></div><br><br></td>
              </tr>
              <tr>
                  <td style="padding-bottom:20px;">
                    <div class="title text-left">Total amount</div>
                    <div class="subtitle text-left">{{ $view_data['price']['formatted_per_night_all_unit_with_extra_guest_cost'] }} x {{ $view_data['total_nights'] }} @if($view_data['total_nights'] > 1)nights @else night @endif </div>
                  </td>
                  <td style="padding-bottom:20px;">
                    <div class="cost">{{ $view_data['price']['formatted_all_night_all_unit_with_extra_guest_cost'] }}</div>
                  </td>
              </tr>
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
              @if($view_data['all_discounts']['formatted_wallet_discount'] != '')
              <tr>
                  <td style="padding-bottom:20px;">
                      <div class="title text-left">Wallet Discount</div>
                  </td>
                  <td style="padding-bottom:20px;">
                      <div class="cost">{{ $view_data['all_discounts']['formatted_wallet_discount'] }}</div>
                  </td>
              </tr>

              @endif @if($view_data['all_discounts']['formatted_miles_discount'] != '')
              <tr>
                 <td style="padding-bottom:20px;">
                    <div class="title text-left">Feathers Discount</div>
                 </td>
                 <td style="padding-bottom:20px;">
                    <div class="cost">{{ $view_data['all_discounts']['formatted_miles_discount'] }}</div>
                 </td>
              </tr>
              @endif @if($view_data['all_discounts']['formatted_coupon_discount'] != '')
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
                @endif @if($view_data['price']['formatted_gst_amount'] != '')
              <tr>
                  <td style="padding-bottom:20px;">
                    <div class="title text-left">GST</div>
                 </td>
                 <td style="padding-bottom:20px;">
                    <div class="cost">{{ $view_data['price']['formatted_gst_amount'] }}</div>
                 </td>
              </tr>
             @endif
              
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
            
                <tr>
                   <td style="padding-bottom:20px; position:relative;font-family: 'MuseoSans';">
                      <div class="title final-amount text-left">Booking amount</div>
                   </td>
                   <td style="padding-bottom:0px; position:relative;">
                      <div class="final-amount cost">{{ $view_data['price']['formatted_payable_amount'] }}</div>
                   </td>
                </tr>
               @if($view_data['price']['formatted_paid_amount'] != '')
                <tr>
                   <td style="padding-bottom:20px;">
                      <div class="title small text-left">Amount paid</div>
                   </td>
                   <td style="padding-bottom:20px;">
                      <div class="cost small">{{ $view_data['price']['formatted_paid_amount'] }}</div>
                   </td>
                </tr>
                @endif

                @if($view_data['price']['formatted_convenience_fee'] != '')
                <tr>
                   <td style="padding-bottom:20px;">
                      <div class="title small text-left">Total Convenience fee paid</div>
                   </td>
                   <td style="padding-bottom:20px;">
                      <div class="cost small">{{ $view_data['price']['formatted_convenience_fee'] }}</div>
                   </td>
                </tr>
                @endif
                
              @if($view_data['price']['formatted_balance_fee'] != '') 
              <tr>
                 <td style="padding-bottom:20px;">
                    <div class="title small text-left">Remaining amount</div>
                    @if($view_data['paylater_payment_date'] != '' )
                    <div class="small card">To be charged on {{ $view_data['paylater_payment_date'] }} </div>
                    @endif
                 </td>
                 <td style="padding-bottom:20px;">
                    <div class="cost small"> {{ $view_data['price']['formatted_balance_fee'] }} </div>
                 </td>
              </tr>

              @endif
               <tr>
                <td colspan="2"><div class="devider"></div><br><br></td>
              </tr>
        </table>
              <table>
                <tr>
                  <td class="note">*Guests are requested to provide valid identification at the time of check-in.</td>
                </tr>
              </table>
      </div>
   </body>
</html>