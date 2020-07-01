<?php
/**
 * Facebook share containing methods related to booking request
 */

namespace App\Libraries\v1_6;

/**
 * Class FbShare
 */
class FbShare
{


    /**
     * Get Fb share content.
     *
     * @param integer $property_id Property id to get content.
     *
     * @return string
     */
    public static function fbShareProperty(int $property_id)
    {
        $share_data = [

            '17' => '<Name> is zipping off to the gorgeous destination of <destination/city>. But this time,<Name> is going to be staying, not in a hotel, not in a dorm, 
                     but in a magnificent cottage! Be different. Go big and book your own private <property type> only on GuestHouser.com! #goodbyehotels ',
            '6'  => '<Name> is zipping off to the gorgeous destination of <destination/city>. But this time, <Name> is going to be staying, not in a hotel, not in a dorm, 
                     but in a magnificent villa! Be different. Go big and book your own private <property type> only on GuestHouser.com! #goodbyehotels ',

            '1'  => '<Name> found the perfect holiday pad in this apartment in <destination/city> and is enjoying absolute privacy with urbane comforts . You can find one too at GuestHouser.com! #goodbyehotels ',

            '9'  => '<Name> along  is bringing a long-cherished childhood fantasy to life and holidaying in this earthy tree house at <destination/city>! Want one for yourself 
                     too? Your wish is our command! Book your own tree house, only at GuestHouser.com! #goodbyehotels ',

            '2'  => '<Name> is heading over to <destination/city>  and spending the holidays the old-fashioned way—in a self-catering guest house! Fresh home-style meals and cosy 
                     comfort, guest houses ensure this and much more. Book from an array of guest houses located in 1000+ cities only on GuestHouser.com! #goodbyehotels',

            '28' => '<Name> is whizzing off to <destination/city>  for a much-needed break, and staying in a trendy boutique stay. Preparing for a long road-trip? Get you gang 
                     together or fly solo. Instantly book from an array of boutique stays offered on GuestHouser.com! #goodbyehotels ',

            '26' => '<Name> is skipping the conventional sun-kissed beaches of <destination/city> and going aboard this ritzy yacht  for an uber-stylish vacation in <city>. Envious? 
                     Charter one for yourself at GuestHouser.com and keep cruisin’! #goodbyehotels',

            '15' => 'So you’ve tried organic food, how about an organic holiday this time like <Name> is going to enjoy  in <destination/city>? Head over to GuestHouser.com and book an earthhouse now! #goodbyehotels',

            '14' => 'So you’ve tried organic food, how about an organic holiday this time like <Name> is going to enjoy  in <destination/city>? Head over to GuestHouser.com and book an rustic hut now! #goodbyehotels',

            '19' => '<Name> is disconnecting from the world for a bit, to relax and rejuvenate   in the serene destination of <destination/city>. Dying to catch a break yourself? Fret not! 
                     Just visit Guesthouser.com, and find your very own bungalow! #goodbyehotels',
            '22' => '<Name> is disconnecting from the world for a bit, to relax and rejuvenate  in the serene destination of <destination/city>. Dying to catch a break yourself? Fret not! 
                     Just visit Guesthouser.com, and find your very own farmhouse! #goodbyehotels',

            '3'  => 'A cosy night and a sumptuous home-cooked breakfast await <Name>  at this comfy B&B in <destination/city>. Check out over 50,000 amazing accommodations on GuestHouser.com and book one for yourself! #goodbyehotels',
            '21' => 'A cosy night and a sumptuous home-cooked breakfast await <Name>   at this comfy homestay in <destination/city>. Check out over 50,000 amazing accommodations on GuestHouser.com and book one for yourself! #goodbyehotels',

            '23' => '<Name> is undertaking a travel back in time to <destination/city>, when holidays were all about long rides in horse-drawn carriages and afternoon siestas under sun-kissed 
                     patios. Come travel back in time and experience the charm of living in a quaint heritage home, only on Guesthouser.com! #goodbyehotels',

            '16' => '<Name> is kicking back the trekking boots  for some serious stargazing while on this campsite in <destination/city>. Feeling adventurous yet? Pack your rucksack and explore 
                     GuestHouser.com to get your own private tent! #goodbyehotels',
            '8'  => '<Name> is reliving happy ol’ hostel days in a dorm in <destination/city>, . Want a getaway with your buddies without letting your money getaway? Save your bucks by booking 
                     a dorm for you and your gang at GuestHouser.com! #goodbyehotels',

        ];

        if (array_key_exists($property_id, $share_data) === true) {
            return $share_data[$property_id];
        } else {
            return "<Name> will be experiencing <destination/city> in all new way by staying not in a hotel but at a gorgeous <property type> ! Find your unique rental at GuestHouser.com and travel 'hat ke' this season! #goodbyehotels";
        }

    }//end fbShareProperty()


}//end class
