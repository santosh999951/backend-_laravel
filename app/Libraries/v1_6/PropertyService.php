<?php
/**
 * Property service containing all property releated functions
 */

namespace App\Libraries\v1_6;
use \Carbon\Carbon;
use App\Libraries\v1_6\AwsService;

use App\Models\{Property, NeighbourhoodAttractions, PropertyDetail, PropertyPricing, HostConversionLead, PropertyTagMapping,
    PropertyImage, PropertyVideo, PropertyAddress, RelationshipManager, Admin, PropertyType, ChannelManagerProperties, CountryCodeMapping , ProperlyTeam};
use App\Libraries\Helper;

/**
 * Class PropertyService
 */
class PropertyService
{

    /**
     * Property Model object for Database Interaction.
     *
     * @var $property
     */
    protected $property;

    /**
     * Email service object for sending emails.
     *
     * @var $email_service
     */
    protected $email_service;

    /**
     * Sms service object for sending sms.
     *
     * @var $sms_service
     */
    protected $sms_service;


    /**
     * Email and Sms service object for sending notifications.
     *
     * @param Property     $property      Object.
     * @param EmailService $email_service Object.
     * @param SmsService   $sms_service   Object.
     */
    public function __construct(Property $property=null, EmailService $email_service=null, SmsService $sms_service=null)
    {
        $this->property      = $property;
        $this->email_service = $email_service;
        $this->sms_service   = $sms_service;

    }//end __construct()


     /**
      * Create New Property.
      *
      * @param integer $user_id          User id.
      * @param array   $property         Property data.
      * @param array   $property_details Property Details data.
      * @param array   $property_pricing Property Pricing data.
      * @param array   $image_info       Property Images data.
      * @param array   $property_tags    Property  Tags data.
      * @param array   $video_data       Property  Video data.
      * @param integer $admin_id         Admin Id data.
      *
      * @return array.
      */
    public function createProperty(int $user_id, array $property, array $property_details, array $property_pricing, array $image_info, array $property_tags, array $video_data, int $admin_id=0)
    {
        // Save Property info.
        $property_response = $this->savePropertyData($user_id, $property, $admin_id);

        if (empty($property_response) === true) {
            return [];
        }

        // Save Property Details.
        $property_detail_response = $this->savePropertyDetailData($user_id, $property_response->id, $property_details);

        // Save Property Pricing.
        $property_pricing_response = $this->savePropertyPricingData($property_response->id, $property_pricing);

        // Save Property Images.
        $property_image_response = $this->savePropertyImages($property_response->id, $image_info);

        // Save Property Tags.
        $property_tags_response = $this->savePropertyTags($property_response->id, $property_tags);

        // Save Property Video.
        $property_video_response = $this->savePropertyVideos($property_response->id, $video_data);

        return [
            'property'         => $property_response,
            'property_details' => $property_detail_response,
            'property_pricing' => $property_pricing_response,
            'property_tags'    => $property_tags_response,
            'property_images'  => $property_image_response,
            'property_video'   => $property_video_response,
        ];

    }//end createProperty()


    /**
     * Save Property Data.
     *
     * @param integer $user_id       User id.
     * @param array   $property_data Property data.
     * @param integer $admin_id      Admin Id.
     *
     * @return object.
     */
    public function savePropertyData(int $user_id, array $property_data, int $admin_id=0)
    {
        $property = $this->property->saveProperty(
            [
            // Required data.
                'user_id'              => $user_id,
                'property_type'        => $property_data['property_type'],
                'room_type'            => $property_data['room_type'],
                'units'                => $property_data['units'],
                'accomodation'         => $property_data['accomodation'],
                'bedrooms'             => $property_data['bedrooms'],
                'beds'                 => $property_data['beds'],
                'bathrooms'            => $property_data['bathrooms'],
                'title'                => ucfirst($property_data['title']),
                'properly_title'       => ucfirst($property_data['properly_title']),
                'currency'             => $property_data['currency'],
                'cancelation_policy'   => $property_data['cancelation_policy'],
                'area'                 => $property_data['area'],
                'city'                 => $property_data['city'],
                'state'                => $property_data['state'],
                'country_code'         => $property_data['country_code'],
                'zipcode'              => $property_data['zipcode'],
                'enabled'              => $property_data['user_verfied'],
                'encripted_address'    => Helper::encodePropertyAddress($property_data['address']),

                'description'          => Helper::emptyOrDefault($property_data, 'description', ''),
                'tags'                 => Helper::emptyOrDefault($property_data, 'property_tags'),
                'amenities'            => Helper::emptyOrDefault($property_data, 'amenities'),
                'latitude'             => Helper::emptyOrDefault($property_data, 'latitude'),
                'longitude'            => Helper::emptyOrDefault($property_data, 'longitude'),
                'virtual_lat'          => (empty($property_data['latitude']) === false) ? Helper::getVirtualLocationValue($property_data['latitude']) : '',
                'virtual_long'         => (empty($property_data['longitude']) === false) ? Helper::getVirtualLocationValue($property_data['longitude']) : '',

                'noc_status'           => Helper::issetOrDefault($property_data, 'noc_status', 0),
                'min_nights'           => Helper::issetOrDefault($property_data, 'min_nights'),
                'max_nights'           => Helper::issetOrDefault($property_data, 'max_nights'),
                'check_in_time'        => Helper::issetOrDefault($property_data, 'check_in_time'),
                'check_out_time'       => Helper::issetOrDefault($property_data, 'check_out_time'),
                'search_keyword'       => Helper::issetOrDefault($property_data, 'search_keyword'),
                'is_listed_by_admin'   => ($admin_id > 0) ? 1 : 0,
                'admin_id'             => $admin_id,
                'converted_by'         => Helper::issetOrDefault($property_data, 'converted_by', 0),
                'gstin'                => Helper::issetOrDefault($property_data, 'gstin', ''),
                'image_caption'        => Helper::issetOrDefault($property_data, 'image_caption', '[]'),
                'video_link'           => ($admin_id > 0) ? Helper::issetOrDefault($property_data, 'video_link') : null,

            // Default Data.
                'availability'         => 'always',
                'loaded_booking_allow' => 0,
                'service_fee'          => SERVICE_FEE,
            ]
        );

        $property = $property->updatePropertyHashId(Helper::encodePropertyId($property->id));

        PropertyAddress::savePropertyAddress($property->id, $property_data['address']);

        return $property;

    }//end savePropertyData()


    /**
     * Save Property Details Data.
     *
     * @param integer $user_id          User id.
     * @param integer $property_id      Property id.
     * @param array   $property_details Property Details data.
     *
     * @return object.
     */
    public function savePropertyDetailData(int $user_id, int $property_id, array $property_details)
    {
        $property_details_obj = new PropertyDetail;

        return $property_details_obj->savePropertyDetails(
            [
                'user_id'                => $user_id,
                'property_id'            => $property_id,
                'policy_services'        => Helper::issetOrDefault($property_details, 'policy_services'),
                'your_space'             => Helper::issetOrDefault($property_details, 'your_space', ''),
                'house_rule'             => Helper::issetOrDefault($property_details, 'house_rule'),
                'guest_brief'            => Helper::issetOrDefault($property_details, 'guest_brief', ''),
                'interaction_with_guest' => Helper::issetOrDefault($property_details, 'interaction_with_guest', ''),
                'local_experience'       => Helper::issetOrDefault($property_details, 'local_experience', ''),
                'from_airport'           => Helper::issetOrDefault($property_details, 'from_airport', ''),
                'train_station'          => Helper::issetOrDefault($property_details, 'train_station', ''),
                'bus_station'            => Helper::issetOrDefault($property_details, 'bus_station', ''),
                'extra_detail'           => Helper::issetOrDefault($property_details, 'extra_detail', ''),
                'usp'                    => Helper::issetOrDefault($property_details, 'usp'),
            ]
        );

    }//end savePropertyDetailData()


    /**
     * Get Listing Property Data.
     *
     * @param integer $user_id     User id.
     * @param integer $property_id Property id.
     *
     * @return object.
     */
    public function getListingPropertyData(int $user_id, int $property_id)
    {
        $property_details = $this->property->getHostListingPropertyById($property_id, $user_id);

        return $property_details;

    }//end getListingPropertyData()


    /**
     * Save Property Pricing Data.
     *
     * @param integer $property_id      Property id.
     * @param array   $property_pricing Property Pricing data.
     *
     * @return array.
     */
    public function savePropertyPricingData(int $property_id, array $property_pricing)
    {
        if (empty($property_pricing['per_unit_extra_guests']) === false) {
            $property_pricing['per_unit_extra_guests'] = ($property_pricing['accomodation'] - $property_pricing['per_unit_extra_guests']);
        }

        $extra_guest_count = Helper::issetOrDefault($property_pricing, 'per_unit_extra_guests', 0);
        $extra_guest_fee   = ($extra_guest_count > 0) ? Helper::issetOrDefault($property_pricing, 'extra_guest_price', 0) : 0;

        $property_pricing_obj = new PropertyPricing;
        return $property_pricing_obj->savePropertyPricing(
            [
                'property_id'        => $property_id,
                'per_night_price'    => $property_pricing['per_night_price'],
                'gh_commission'      => $property_pricing['gh_commission'],
                'markup_service_fee' => ($property_pricing['gh_commission'] < GH_MARKUP_MAX_PERCENTAGE) ? (GH_MARKUP_MAX_PERCENTAGE - $property_pricing['gh_commission']) : 0,
                'extra_guest_count'  => $extra_guest_count,
                'extra_guest_fee'    => $extra_guest_fee,

                'per_week_price'     => Helper::issetOrDefault($property_pricing, 'per_week_price', 0),
                'per_month_price'    => Helper::issetOrDefault($property_pricing, 'per_month_price', 0),
                'cleaning_mode'      => Helper::issetOrDefault($property_pricing, 'cleaning_mode', -1),
                'cleaning_fee'       => Helper::issetOrDefault($property_pricing, 'cleaning_fee', 0),

            ]
        );

    }//end savePropertyPricingData()


    /**
     * Save Property Image Data.
     *
     * @param integer $property_id         Property id.
     * @param array   $property_image_data Property Image data.
     *
     * @return array.
     */
    public function savePropertyImages(int $property_id, array $property_image_data)
    {
        $property_image_obj = new PropertyImage;

        $property_image_response = [];

        foreach ($property_image_data as $image_data) {
            $property_image_response[] = $property_image_obj->savePropertyImage($property_id, $image_data['image'], $image_data['caption'], $image_data['is_hide'], $image_data['order']);
        }

        // Add property to Watermark Queue.
        if (empty($property_image_response) === false) {
            $this->addPropertyToQueueForWatermarking($property_id);
        }

        return $property_image_response;

    }//end savePropertyImages()


    /**
     * Save Property Video Data.
     *
     * @param integer $property_id         Property id.
     * @param array   $property_video_data Property video data.
     *
     * @return array.
     */
    public function savePropertyVideos(int $property_id, array $property_video_data)
    {
        if (empty($property_video_data) === true) {
            return [];
        }

        $property_video_obj = new PropertyVideo;

        $property_video = $property_video_obj->savePropertyVideo($property_id, $property_video_data['video'], $property_video_data['thumbnail'], VIDEO_PENDING_FOR_ENCODING);

        if (empty($property_video) === false) {
            // Add Video for transcoding.
            $this->pushPropertyVideoToTranscodingQueue($property_id, $property_video->id, $property_video->name, $property_video->thumbnail);
        }

        return $property_video;

    }//end savePropertyVideos()


        /**
         * Watermark images.
         *
         * @param integer $property_id Property id.
         *
         * @return boolean
         */
    private function addPropertyToQueueForWatermarking(int $property_id)
    {
        try {
            $sqs = AwsService::getSqsClient('ap-southeast-1');

            $sqs->sendMessage(
                [
                    'QueueUrl'    => WATERMARK_PROPERTY_IMAGES_QUEUE,
                    'MessageBody' => json_encode(['pid' => $property_id, 'type' => 'manual_uploaded_pid_images']),
                ]
            );
        } catch (Exception $e) {
            Helper::logError('WATERMARKING - Error in creating job to move watermarking images ', $e->getMessage());
            return false;
        }

        return true;

    }//end addPropertyToQueueForWatermarking()


     /**
      * Transcode video.
      *
      * @param integer $property_id Property id.
      * @param integer $video_id    Property Video id.
      * @param string  $video_name  Property Video Name.
      * @param string  $thumbnail   Video Thumbnail.
      * @param integer $count       Count.
      *
      * @return boolean
      */
    private function pushPropertyVideoToTranscodingQueue(int $property_id, int $video_id, string $video_name, string $thumbnail, int $count=1)
    {
        try {
            $sqs = AwsService::getSqsClient(TRANSCODER_AWS_REGION);
            $sqs->sendMessage(
                [
                    'QueueUrl'    => VIDEOS_PENDING_FOR_TRANSCODING_QUEUE,
                    'MessageBody' => json_encode(
                        [
                            'vid'       => $video_id,
                            'name'      => $video_name,
                            'thumbnail' => $thumbnail,
                            'pid'       => $property_id,
                            'count'     => $count,
                        ]
                    ),
                ]
            );
        } catch (Exception $e) {
            Helper::logError('Error in pushing to queue ', $e->getMessage());
            return false;
        }

        return true;

    }//end pushPropertyVideoToTranscodingQueue()


    /**
     * Delete Transcode video.
     *
     * @param integer $property_id Property id.
     * @param string  $video_name  Property Video Name.
     *
     * @return boolean
     */
    private function pushPropertyVideoForDeleteQueue(int $property_id, string $video_name)
    {
        try {
            $sqs = AwsService::getSqsClient(TRANSCODER_AWS_REGION);
            $sqs->sendMessage(
                [
                    'QueueUrl'    => VIDEOS_TO_DELETE,
                    'MessageBody' => json_encode(
                        [
                            'name' => $video_name,
                            'pid'  => $property_id,
                        ]
                    ),
                ]
            );
        } catch (Exception $e) {
            Helper::logError('Error in pushing to queue ', $e->getMessage());
            return false;
        }

        return true;

    }//end pushPropertyVideoForDeleteQueue()


    /**
     * Map Property with Tags.
     *
     * @param integer $property_id   Property id.
     * @param array   $property_tags Tags.
     *
     * @return array.
     */
    public function savePropertyTags(int $property_id, array $property_tags)
    {
        $property_tag_mapping_obj = new PropertyTagMapping;

        $tags_data = [];
        foreach ($property_tags as $tag) {
            $tags_data[] = $property_tag_mapping_obj->savePropertyTag($property_id, $tag);
        }

        return $tags_data;

    }//end savePropertyTags()


    /**
     * Update Host Property.
     *
     * @param integer $user_id          User id.
     * @param integer $property_id      Property id.
     * @param array   $property         Property data.
     * @param array   $property_details Property Details data.
     * @param array   $property_pricing Property Pricing data.
     * @param array   $image_info       Property Images data.
     * @param array   $property_tags    Property  Tags data.
     * @param array   $video_data       Property  Video data.
     * @param integer $admin_id         Admin Id.
     *
     * @return array.
     */
    public function updateProperty(int $user_id, int $property_id, array $property, array $property_details, array $property_pricing, array $image_info, array $property_tags, array $video_data, int $admin_id=0)
    {
        // Old Property Data.
        $existing_property = $this->property->getPropertyById($property_id);

        // Old Property Detail Data.
        $property_details_obj      = new PropertyDetail;
        $existing_property_details = $property_details_obj->getPropertyDetails($property_id);

        // Old Property pricing.
        $property_pricing_obj      = new PropertyPricing;
        $existing_property_pricing = $property_pricing_obj->getPropertyPricing($property_id);

        // Check if user can edit content.
        $block_content = 0;
        if (empty($existing_property->content_edited) === false) {
            $block_content = 1;
        }

        if ($admin_id > 0) {
            $admin = Admin::getAdminById($admin_id);

            // Allow Edit content For Super Admin and creative Team.
            if (in_array($admin->department_id, [4]) === true || $admin->role === 9) {
                $block_content = 0;
            }
        }

        if ($block_content === 1) {
            // Block Content from editiong.
            unset($property['title']);
            unset($property['description']);
            unset($property_details['guest_brief']);
            unset($property_details['your_space']);
        }

        // Save Property info.
        $property_response = $this->updatePropertyData($existing_property, $property, $admin_id);

        // Save Property Details.
        $property_detail_response = $this->updatePropertyDetailData($existing_property_details, $property_details);

        // Save Property Pricing.
        $property_pricing_response = $this->updatePropertyPricingData($existing_property_pricing, $property_pricing);

        // Save Property Images.
        $property_image_response = $this->updatePropertyImages($property_id, $image_info);

        // Save Property Tags.
        $property_tags_response = $this->updatePropertyTags($property_id, $property_tags);

        // Save Property Video.
        $property_video_response = $this->updatePropertyVideos($property_id, $video_data);

        // Get Update Key Value Pair.
        $updated_data = [];

        // Get Property Updated Data.
        foreach ($existing_property as $key => $value) {
            if ($existing_property->{$key} !== $property_response->{$key}) {
                $updated_data[] = [
                    'key' => $key,
                    'old' => $existing_property->{$key},
                    'new' => $property_response->{$key},
                ];
            }
        }

        // Get Property Detail Updated Data.
        foreach ($existing_property_details as $key => $value) {
            if ($existing_property_details->{$key} !== $property_detail_response->{$key}) {
                $updated_data[] = [
                    'key' => $key,
                    'old' => $existing_property_details->{$key},
                    'new' => $property_detail_response->{$key},
                ];
            }
        }

        // Get Property Pricing Updated Data.
        foreach ($existing_property_pricing as $key => $value) {
            if ($key === 'last_updated_by_host') {
                continue;
            }

            if ($existing_property_pricing->{$key} !== $property_pricing_response->{$key}) {
                $updated_data[] = [
                    'key' => $key,
                    'old' => $existing_property_pricing->{$key},
                    'new' => $property_pricing_response->{$key},
                ];
            }
        }

        return [
            'property'         => $property_response,
            'property_details' => $property_detail_response,
            'property_pricing' => $property_pricing_response,
            'property_tags'    => $property_tags_response,
            'property_images'  => $property_image_response,
            'updated_data'     => $updated_data,
        ];

    }//end updateProperty()


    /**
     * Save Property Data.
     *
     * @param Property $existing_property Property object.
     * @param array    $property_data     Property data.
     * @param integer  $admin_id          Admin Id.
     *
     * @return object.
     */
    public function updatePropertyData(Property $existing_property, array $property_data, int $admin_id=0)
    {
        $property_params = [
            // Required data.
            'property_id'             => $existing_property->id,
            'property_type'           => Helper::issetOrDefault($property_data, 'property_type', $existing_property->property_type),
            'room_type'               => Helper::issetOrDefault($property_data, 'room_type', $existing_property->room_type),
            'units'                   => Helper::issetOrDefault($property_data, 'units', $existing_property->units),
            'accomodation'            => Helper::issetOrDefault($property_data, 'accomodation', $existing_property->accomodation),
            'bedrooms'                => Helper::issetOrDefault($property_data, 'bedrooms', $existing_property->bedrooms),
            'beds'                    => Helper::issetOrDefault($property_data, 'beds', $existing_property->beds),
            'bathrooms'               => Helper::issetOrDefault($property_data, 'bathrooms', $existing_property->bathrooms),
            'title'                   => ucfirst(Helper::issetOrDefault($property_data, 'title', $existing_property->title)),
            'properly_title'          => ucfirst(Helper::issetOrDefault($property_data, 'properly_title', $existing_property->properly_title)),
            'currency'                => Helper::issetOrDefault($property_data, 'currency', $existing_property->currency),
            'cancelation_policy'      => Helper::issetOrDefault($property_data, 'cancelation_policy', $existing_property->cancelation_policy),
            'area'                    => Helper::issetOrDefault($property_data, 'area', $existing_property->area),
            'city'                    => Helper::issetOrDefault($property_data, 'city', $existing_property->city),
            'state'                   => Helper::issetOrDefault($property_data, 'state', $existing_property->state),
            'country_code'            => Helper::issetOrDefault($property_data, 'country_code', $existing_property->country),
            'zipcode'                 => Helper::issetOrDefault($property_data, 'zipcode', $existing_property->zipcode),
            'enabled'                 => $existing_property->enabled,
            'encripted_address'       => (isset($property_data['address']) === true) ? Helper::encodePropertyAddress($property_data['address']) : $existing_property->address,

            'description'             => Helper::issetOrDefault($property_data, 'description', $existing_property->description),
            'tags'                    => Helper::emptyOrDefault($property_data, 'property_tags', $existing_property->tags),
            'amenities'               => Helper::emptyOrDefault($property_data, 'amenities', $existing_property->amenities),
            'latitude'                => Helper::emptyOrDefault($property_data, 'latitude', $existing_property->latitude),
            'longitude'               => Helper::emptyOrDefault($property_data, 'longitude', $existing_property->longitude),
            'virtual_lat'             => (empty($property_data['latitude']) === false) ? Helper::getVirtualLocationValue($property_data['latitude']) : $existing_property->v_lat,
            'virtual_long'            => (empty($property_data['longitude']) === false) ? Helper::getVirtualLocationValue($property_data['longitude']) : $existing_property->v_lng,

            'noc_status'              => Helper::issetOrDefault($property_data, 'noc_status', $existing_property->noc),
            'min_nights'              => Helper::issetOrDefault($property_data, 'min_nights', $existing_property->min_nights),
            'max_nights'              => Helper::issetOrDefault($property_data, 'max_nights', $existing_property->min_nights),
            'check_in_time'           => Helper::issetOrDefault($property_data, 'check_in_time', $existing_property->check_in),
            'check_out_time'          => Helper::issetOrDefault($property_data, 'check_out_time', $existing_property->checkout),
            'search_keyword'          => ($admin_id > 0) ? Helper::issetOrDefault($property_data, 'search_keyword', $existing_property->search_keyword) : $existing_property->search_keyword,
            'is_listed_by_admin'      => $existing_property->by_admin,
            'admin_id'                => $existing_property->admin_id,
            'converted_by'            => Helper::issetOrDefault($property_data, 'converted_by', $existing_property->converted_by),
            'gstin'                   => Helper::issetOrDefault($property_data, 'gstin', $existing_property->gst_no),
            'image_caption'           => Helper::issetOrDefault($property_data, 'image_caption', $existing_property->image_caption),
            'video_link'              => ($admin_id > 0) ? Helper::issetOrDefault($property_data, 'video_link', $existing_property->video_link) : $existing_property->video_link,

            // Default Data.
            'availability'            => $existing_property->availability,
            'loaded_booking_allow'    => $existing_property->loaded_booking_allow,
            'service_fee'             => $existing_property->service_fee,

            'status'                  => ($existing_property->admin_score > 0 && empty($admin_id) === true) ? PROPERTY_STATUS_MODIFIED : $existing_property->status,
            'last_updated_by_admin'   => ($admin_id > 0) ? $admin_id : 0,
            'accomodation_changed_by' => (isset($property_data['accomodation']) === true && $existing_property->accomodation !== $property_data['accomodation']) ? $admin_id : $existing_property->accomodation_changed_by,
        ];

        $property = $this->property->saveProperty($property_params);

        if (isset($property_data['address']) === true && $property_data['address'] !== $existing_property->address) {
            PropertyAddress::savePropertyAddress($property->id, $property_data['address']);
        }

        if (isset($property_data['accomodation']) === true && $existing_property->accomodation !== $property_data['accomodation']) {
            // Change Channel Manager Data.
            $channel_manager = ChannelManagerProperties::getBcomDataByProperty($property->id);

            if (empty($channel_manager) === false) {
                $channel_manager_custom_data = (empty($channel_manager->custom_data) === false) ? json_decode($channel_manager->custom_data, true) : [];
                $channel_manager_custom_data['modify_data']['room_details']['occupancy'] = true;

                $channel_manager = $channel_manager->saveCustomData($channel_manager_custom_data, 10);
            }
        }

        // Need to Code.
        // On change Proeprty Units Change Inventory Pricing Data.
        return $property;

    }//end updatePropertyData()


    /**
     * Save Property Details Data.
     *
     * @param PropertyDetail $existing_property_details PropertyDetail object.
     * @param array          $property_details          Property Details data.
     *
     * @return object.
     */
    public function updatePropertyDetailData(PropertyDetail $existing_property_details, array $property_details)
    {
        return $existing_property_details->savePropertyDetails(
            [
                'user_id'                => $existing_property_details->user_id,
                'property_id'            => $existing_property_details->pid,
                'policy_services'        => Helper::issetOrDefault($property_details, 'policy_services', $existing_property_details->policy_services),
                'your_space'             => Helper::issetOrDefault($property_details, 'your_space', $existing_property_details->your_space),
                'house_rule'             => Helper::issetOrDefault($property_details, 'house_rule', $existing_property_details->house_rule),
                'guest_brief'            => Helper::issetOrDefault($property_details, 'guest_brief', $existing_property_details->guest_brief),
                'interaction_with_guest' => Helper::issetOrDefault($property_details, 'interaction_with_guest', $existing_property_details->interaction_with_guest),
                'local_experience'       => Helper::issetOrDefault($property_details, 'local_experience', $existing_property_details->local_experience),
                'from_airport'           => Helper::issetOrDefault($property_details, 'from_airport', $existing_property_details->from_airport),
                'train_station'          => Helper::issetOrDefault($property_details, 'train_station', $existing_property_details->train_station),
                'bus_station'            => Helper::issetOrDefault($property_details, 'bus_station', $existing_property_details->bus_station),
                'extra_detail'           => Helper::issetOrDefault($property_details, 'extra_detail', $existing_property_details->extra_detail),
                'usp'                    => Helper::issetOrDefault($property_details, 'usp', $existing_property_details->usp),
            ]
        );

    }//end updatePropertyDetailData()


    /**
     * Save Property Pricing Data.
     *
     * @param PropertyPricing $existing_property_pricing Property Pricing object.
     * @param array           $property_pricing          Property Pricing data.
     *
     * @return array.
     */
    public function updatePropertyPricingData(PropertyPricing $existing_property_pricing, array $property_pricing)
    {
        if (empty($existing_property_pricing) === true) {
            return [];
        }

        if (empty($property_pricing['per_unit_extra_guests']) === false) {
            $property_pricing['per_unit_extra_guests'] = ($property_pricing['accomodation'] - $property_pricing['per_unit_extra_guests']);
        }

        $extra_guest_count = Helper::issetOrDefault($property_pricing, 'per_unit_extra_guests', $existing_property_pricing->extra_guest_count);
        $extra_guest_fee   = ($extra_guest_count > 0) ? Helper::issetOrDefault($property_pricing, 'extra_guest_price', $existing_property_pricing->extra_guest_fee) : 0;

        return $existing_property_pricing->savePropertyPricing(
            [
                'property_id'        => $existing_property_pricing->pid,
                'per_night_price'    => Helper::issetOrDefault($property_pricing, 'per_night_price', $existing_property_pricing->per_night_price),
                'gh_commission'      => $existing_property_pricing->gh_commission,
                'markup_service_fee' => ($existing_property_pricing->gh_commission < GH_MARKUP_MAX_PERCENTAGE) ? (GH_MARKUP_MAX_PERCENTAGE - $existing_property_pricing->gh_commission) : 0,
                'extra_guest_count'  => $extra_guest_count,
                'extra_guest_fee'    => $extra_guest_fee,

                'per_week_price'     => Helper::issetOrDefault($property_pricing, 'per_week_price', $existing_property_pricing->per_week_price),
                'per_month_price'    => Helper::issetOrDefault($property_pricing, 'per_month_price', $existing_property_pricing->per_month_price),
                'cleaning_mode'      => Helper::issetOrDefault($property_pricing, 'cleaning_mode', $existing_property_pricing->cleaning_mode),
                'cleaning_fee'       => Helper::issetOrDefault($property_pricing, 'cleaning_fee', $existing_property_pricing->cleaning_fee),
            ]
        );

    }//end updatePropertyPricingData()


    /**
     * Save Property Image Data.
     *
     * @param integer $property_id         Property id.
     * @param array   $property_image_data Property Image data.
     *
     * @return array.
     */
    public function updatePropertyImages(int $property_id, array $property_image_data)
    {
        $property_image_obj = new PropertyImage;

        $property_image_response = [];

        foreach ($property_image_data as $image_data) {
            $property_image_response[] = $property_image_obj->savePropertyImage($property_id, $image_data['image'], $image_data['caption'], $image_data['is_hide'], $image_data['order'], $image_data['unlink']);
        }

        // Add property to Watermark Queue.
        if (empty($property_image_response) === false) {
            $this->addPropertyToQueueForWatermarking($property_id);
        }

        return $property_image_response;

    }//end updatePropertyImages()


    /**
     * Map Property with Tags.
     *
     * @param integer $property_id   Property id.
     * @param array   $property_tags Tags.
     *
     * @return array.
     */
    public function updatePropertyTags(int $property_id, array $property_tags)
    {
        $property_tag_mapping_obj = new PropertyTagMapping;

        $existing_tags = $property_tag_mapping_obj->getPropertyTags($property_id);

        $delete_tags = array_diff($existing_tags, $property_tags);

        $new_tags = array_diff($property_tags, $existing_tags);

        // Delete Property Tags.
        $property_tag_mapping_obj->deletePropertyTags($property_id, $delete_tags);

        // Add Proeprty Tags.
        foreach ($new_tags as $tag) {
            $property_tag_mapping_obj->savePropertyTag($property_id, $tag);
        }

        return $property_tags;

    }//end updatePropertyTags()


    /**
     * Update Property Video Data.
     *
     * @param integer $property_id         Property id.
     * @param array   $property_video_data Property Video data.
     *
     * @return array.
     */
    public function updatePropertyVideos(int $property_id, array $property_video_data)
    {
        $property_video_obj = new PropertyVideo;

        $property_video_response = [];

        foreach ($property_video_data as $video_data) {
            if ($video_data['unlink'] === 1) {
                $property_video            = $property_video_obj->deletePropertyVideo($property_id, $video_data['video']);
                $property_video_response[] = $property_video;

                // Queue video for delete.
                $this->pushPropertyVideoForDeleteQueue($property_id, $video_data['video']);
            } else {
                $property_video            = $property_video_obj->savePropertyVideo($property_id, $video_data['video'], $video_data['thumbnail'], VIDEO_PENDING_FOR_ENCODING);
                $property_video_response[] = $property_video;

                // Queue Video for Transcode.
                $this->pushPropertyVideoToTranscodingQueue($property_id, $property_video->id, $property_video->name, $property_video->thumbnail);
            }
        }

        return $property_video_response;

    }//end updatePropertyVideos()


    /**
     * Get nearby location tags and keywords.
     *
     * @param array $data Property data.
     *
     * @return array nearby Location data.
     */
    public static function getNearbyLocationTagsAndKeywords(array $data)
    {
        // Input params.
        $city    = $data['city'];
        $state   = $data['state'];
        $country = $data['country'];
        $area    = $data['area'];

        $property_type          = (isset($data['property_type']) === true) ? $data['property_type'] : '';
        $selected_city_for_stay = (isset($data['property_type']) === true) ? $data['selected_city_for_stay'] : '';

        // Set search keyword location.
        $keyword_locations           = [];
        $location_search_tags        = [];
        $location_search_names       = [];
        $popular_similar_locations   = [];
        $total_property_top_location = [];
        $top_location                = [];

        // Get search keyword by location.
        $search_keywords_of_location = [];
        if (empty($country) === false && empty($state) === false && empty($city) === false && strtolower($state) !== 'goa') {
            // State, country, city is provided but state is not goa and page is first.
            $search_keywords_of_location = Property::getPropertiesAsPerLocation(
                [
                    'country' => $country,
                    'state'   => $state,
                    'city'    => $city,
                ]
            );
        } else if (empty($country) === false && strtolower($state) === 'goa') {
            // State is goa, country is provided but city is not provided and page is first.
            $search_keywords_of_location = Property::getPropertiesAsPerLocation(
                [
                    'country' => $country,
                    'state'   => $state,
                ]
            );
        }

        // Iterate over search keywords.
        foreach ($search_keywords_of_location as $one_search_keyword) {
            // Explode name and tag seperated by "-".
            $exploded_search_keyword = explode('-', $one_search_keyword['search_keyword']);

            // Get location name and tag.
            $location_name = trim($exploded_search_keyword[0]);
            $location_tag  = (isset($exploded_search_keyword[1]) === true) ? trim($exploded_search_keyword[1]) : '';

            // New location name.
            if (in_array($location_name, $location_search_names) === false) {
                $keyword_locations[]           = [
                    'name'     => $location_name,
                    'tag'      => $location_tag,
                    'link'     => self::getSearchLink($one_search_keyword['city'], $property_type),
                    'show'     => 1,
                    'selected' => (strtolower($selected_city_for_stay) === strtolower($one_search_keyword['city']) || strtolower($area) === strtolower($location_name)) ? 1 : 0,
                ];
                $top_location[]                = [
                    'name' => $location_name,
                    'tag'  => $location_tag,
                ];
                $total_property_top_location[] = $one_search_keyword['total'];

                // Location tag is set and is new.
                if (empty($location_tag) === false && in_array(ucwords($location_tag), $location_search_tags) === false) {
                    $location_search_tags[] = ucwords($location_tag);
                }
            }

            // Append location name.
            $location_search_names[] = $location_name;
        }//end foreach

        array_multisort($total_property_top_location, SORT_DESC, $top_location);

        // Iterate over all searched keyword locations.
        foreach ($top_location as $top_location) {
            // New location name.
            if (in_array($top_location['name'], $popular_similar_locations) === false) {
                $popular_similar_locations[] = [
                    'name' => $top_location['name'],
                    'tag'  => $top_location['tag'],
                ];
            }

            // Get only 10 popular searched locations.
            if (count($popular_similar_locations) === 10) {
                break;
            }
        }

        $keyword_locations = array_merge(
            [[
                'name'     => 'All',
                'tag'      => 'all',
                'link'     => self::getSearchLink($state, $property_type),
                'show'     => 0,
                'selected' => (strtolower($selected_city_for_stay) === strtolower($state) || empty($selected_city_for_stay) === true ) ? 1 : 0,
            ],
            ],
            $keyword_locations
        );

        return [
            'keyword_locations'         => $keyword_locations,
            'location_search_tags'      => $location_search_tags,
            'popular_similar_locations' => $popular_similar_locations,
        ];

    }//end getNearbyLocationTagsAndKeywords()


    /**
     * Get nearby locations for stay pages.
     *
     * @param array $data Property data.
     *
     * @return array nearby Location data.
     */
    public static function getNearbyLocationsForStays(array $data)
    {
        // Input params.
        $city    = $data['city'];
        $state   = $data['state'];
        $country = $data['country'];
        $area    = $data['area'];

        $property_type          = (isset($data['property_type']) === true) ? $data['property_type'] : '';
        $selected_city_for_stay = (isset($data['property_type']) === true) ? $data['selected_city_for_stay'] : '';

        // Set search keyword location.
        $keyword_locations     = [];
        $location_search_names = [];

        // Get search keyword by location.
        $search_keywords_of_location = [];

        $search_keywords_of_location = Property::getPropertiesExceptCurrentCity(
            [
                'country' => $country,
                'state'   => $state,
                'city'    => $city,
            ]
        );

        // Iterate over search keywords.
        foreach ($search_keywords_of_location as $one_search_keyword) {
            $location_name = ucfirst($one_search_keyword['city']);
            if (in_array($location_name, $location_search_names) === false) {
                $keyword_locations[] = [
                    'name'     => $location_name,
                    'tag'      => '',
                    'link'     => self::getSearchLink($one_search_keyword['city'], $property_type),
                    'show'     => (strtolower($selected_city_for_stay) === strtolower($one_search_keyword['city'])) ? 0 : 1,
                    'selected' => (strtolower($selected_city_for_stay) === strtolower($one_search_keyword['city']) || strtolower($area) === strtolower($location_name)) ? 1 : 0,
                ];
            }

            // Append location name.
            $location_search_names[] = $location_name;
        }//end foreach

        $keyword_locations = array_merge(
            [[
                'name'     => 'All',
                'tag'      => 'all',
                'link'     => self::getSearchLink($state, $property_type),
                'show'     => (strtolower($selected_city_for_stay) === strtolower($state) || empty($selected_city_for_stay) === true ) ? 0 : 1,
                'selected' => (strtolower($selected_city_for_stay) === strtolower($state) || empty($selected_city_for_stay) === true ) ? 1 : 0,
            ],
            ],
            $keyword_locations
        );

        return $keyword_locations;

    }//end getNearbyLocationsForStays()


    /**
     * Get search url for stay page.
     *
     * @param string $city    City.
     * @param string $amenity Amenity.
     *
     * @return string Search url.
     */
    private static function getSearchLink(string $city, string $amenity)
    {
        $city    = strtolower(str_replace(' ', '-', $city));
        $amenity = (empty($amenity) === true) ? 'stay' : strtolower(str_replace(' ', '-', str_replace('&', 'and', $amenity))).'s';
        return $amenity.'-in-'.$city;

    }//end getSearchLink()


    /**
     * Get data to display in 2 divs displaying refund policy and payment method. also get data for footer.
     *
     * @param array  $data Array of data to check.
     * @param string $page Page to fetch data for.
     *
     * @return array Array of data to show.
     */
    public static function getFooterAndCancellationPolicyDivData(array $data, string $page='prepayment')
    {
        //phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
        $payment_option['full_payment'] = $payment_option['partial_payment'] = $payment_option['si_payment'] = $payment_option['coa_payment'] = [
            'title' => '',
            'text'  => 'Reserve',
        ];
        $cancellation_option            = [
            'title' => '',
            'text'  => 'Reserve',
        ];
        //phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
        $footer['full_payment'] = $footer['partial_payment'] = $footer['si_payment'] = $footer['coa_payment'] = [
            'title'        => '',
            'sub'          => '',
            'button_text'  => 'Reserve',
            'final_amount' => $data['payment_methods']['full_payment']['payable_amount'],
        ];

        // Check if partially paid amount is refundable.
        if (empty($data['start_date']) === false) {
            $is_refundable = Helper::isAmountRefundable($data['cancellation_policy']['policy_days'], $data['start_date']);
        } else {
            $is_refundable = ($data['cancellation_policy']['policy_days'] > 0) ? true : false;
        }

        if (isset($data['payment_methods']['coa_payment']) === true) {
            $payment_option['coa_payment'] = [
                'title' => PAY_AT_CHECKIN_TITLE,
                'text'  => PAY_AT_CHECKIN_TEXT,
            ];

            $footer['coa_payment'] = [
                'title'        => ($is_refundable === true) ? FREE_CANCELLATION_TITLE : '',
                'sub'          => ($is_refundable === true) ? FREE_CANCELLATION_TEXT : '',
                'button_text'  => 'Reserve',
                'final_amount' => $data['payment_methods']['coa_payment']['payable_amount'],
            ];

            if ($page === 'prepayment') {
                $footer['coa_payment'] = [
                    'title'        => 'Payable now',
                    'sub'          => CURRENCY_SYMBOLS[$data['currency']]['webicon'].'0',
                    'button_text'  => 'Confirm',
                    'final_amount' => $data['payment_methods']['coa_payment']['payable_amount'],
                ];
            }
        }//end if

        if (isset($data['payment_methods']['si_payment']) === true) {
            $payment_option['si_payment'] = [
                'title' => SI_PAYMENT_TITLE,
                'text'  => str_replace('{{policy_days}}', $data['cancellation_policy']['policy_days'], SI_PAYMENT_TEXT),
            ];

            $footer['si_payment'] = [
                'title'        => ($is_refundable === true) ? FREE_CANCELLATION_TITLE : '',
                'sub'          => ($is_refundable === true) ? 'Reserve with '.CURRENCY_SYMBOLS[$data['currency']]['webicon'].'1' : '',
                'button_text'  => 'Reserve',
                'final_amount' => $data['payment_methods']['si_payment']['payable_amount'],
            ];

            if ($page === 'prepayment') {
                $footer['si_payment'] = [
                    'title'        => 'Payable now',
                    'sub'          => CURRENCY_SYMBOLS[$data['currency']]['webicon'].'1',
                    'button_text'  => 'Pay',
                    'final_amount' => $data['payment_methods']['si_payment']['payable_amount'],
                ];
            }
        }//end if

        if (isset($data['payment_methods']['partial_payment']) === true) {
            $payment_option['partial_payment'] = [
                'title' => PARTIAL_PAYMENT_TITLE,
                'text'  => PARTIAL_PAYMENT_TEXT,
            ];

            $footer['partial_payment'] = [
                'title'        => ($is_refundable === true) ? 'Full refund' : 'Partial Payment',
                'sub'          => 'Reserve with '.Helper::getFormattedMoney($data['payment_methods']['partial_payment']['payable_now'], CURRENCY_SYMBOLS[$data['currency']]['webicon']),
                'button_text'  => 'Reserve',
                'final_amount' => $data['payment_methods']['partial_payment']['payable_amount'],
            ];

            if ($page === 'prepayment') {
                $footer['partial_payment'] = [
                    'title'        => 'Payable now',
                    'sub'          => Helper::getFormattedMoney($data['payment_methods']['partial_payment']['payable_now'], CURRENCY_SYMBOLS[$data['currency']]['webicon']),
                    'button_text'  => 'Pay',
                    'final_amount' => $data['payment_methods']['partial_payment']['payable_amount'],
                ];
            }
        }//end if

        if (isset($data['payment_methods']['full_payment']) === true) {
            if ($is_refundable === true) {
                $payment_option['full_payment'] = [
                    'title' => FULL_PAYMENT_TITLE,
                    'text'  => FULL_PAYMENT_TEXT,
                ];

                $footer['full_payment'] = [
                    'title'        => 'Full refund',
                    'sub'          => FREE_CANCELLATION_TITLE,
                    'button_text'  => 'Reserve',
                    'final_amount' => $data['payment_methods']['full_payment']['payable_amount'],
                ];

                if ($page === 'prepayment') {
                    $footer['full_payment'] = [
                        'title'        => 'Payable now',
                        'sub'          => Helper::getFormattedMoney($data['payment_methods']['full_payment']['payable_now'], CURRENCY_SYMBOLS[$data['currency']]['webicon']),
                        'button_text'  => ($data['payment_methods']['full_payment']['payable_amount'] > 0) ? 'Pay' : 'Confirm',
                        'final_amount' => $data['payment_methods']['full_payment']['payable_amount'],
                    ];
                }
            } else {
                $footer['full_payment'] = [
                    'title'        => 'No refund',
                    'sub'          => 'No cancellation',
                    'button_text'  => 'Reserve',
                    'final_amount' => $data['payment_methods']['full_payment']['payable_amount'],
                ];
                if ($page === 'prepayment') {
                    $footer['full_payment'] = [
                        'title'        => 'Payable now',
                        'sub'          => Helper::getFormattedMoney($data['payment_methods']['full_payment']['payable_now'], CURRENCY_SYMBOLS[$data['currency']]['webicon']),
                        'button_text'  => 'Pay',
                        'final_amount' => $data['payment_methods']['full_payment']['payable_amount'],
                    ];
                }
            }//end if
        }//end if

        if ($is_refundable === true || (empty($data['start_date']) === true && $data['cancellation_policy']['policy_days'] > 0)) {
            $cancellation_option = [
                'title' => TOTAL_AMOUNT_REFUNDABLE_TITLE,
                'text'  => str_replace('{{cancellation_days}}', $data['cancellation_policy']['policy_days'], TOTAL_AMOUNT_REFUNDABLE_TEXT),
            ];
        }

        if (empty($data['start_date']) === true) {
            //phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
            $footer['full_payment'] = $footer['partial_payment'] = $footer['si_payment'] = $footer['coa_payment'] = [
                'title'        => ($is_refundable === true) ? 'Full refund' : 'No refund',
                'sub'          => ($is_refundable === true) ? FREE_CANCELLATION_TITLE : 'No cancellation',
                'button_text'  => 'Select dates',
                'final_amount' => $data['payment_methods']['full_payment']['payable_amount'],
            ];
        }

        if (isset($data['selected_payment_method']) === true) {
            // In case of non instant request.
            if ($page === 'prepayment' && isset($data['is_instant_bookable']) === true && $data['is_instant_bookable'] === 0) {
                $footer[$data['selected_payment_method']] = [
                    'title'        => '',
                    'sub'          => '',
                    'button_text'  => 'Request availability',
                    'final_amount' => $data['payment_methods'][$data['selected_payment_method']]['payable_amount'],
                ];
            }

            return [
                'footer'                  => $footer[$data['selected_payment_method']],
                'left_div'                => $cancellation_option,
                'right_div'               => $payment_option[$data['selected_payment_method']],
                'selected_payment_method' => $data['selected_payment_method'],
            ];
        }

        if (isset($data['payment_methods']['coa_payment']) === true) {
            // In case of non instant request.
            if ($page === 'prepayment' && isset($data['is_instant_bookable']) === true && $data['is_instant_bookable'] === 0) {
                $footer['coa_payment'] = [
                    'title'        => '',
                    'sub'          => '',
                    'button_text'  => 'Request availability',
                    'final_amount' => $data['payment_methods']['coa_payment']['payable_amount'],
                ];
            }

            return [
                'footer'                  => $footer['coa_payment'],
                'left_div'                => $cancellation_option,
                'right_div'               => $payment_option['coa_payment'],
                'selected_payment_method' => 'coa_payment',
            ];
        } else if (isset($data['payment_methods']['si_payment']) === true) {
            // In case of non instant request.
            if ($page === 'prepayment' && isset($data['is_instant_bookable']) === true && $data['is_instant_bookable'] === 0) {
                $footer['si_payment'] = [
                    'title'        => '',
                    'sub'          => '',
                    'button_text'  => 'Request availability',
                    'final_amount' => $data['payment_methods']['si_payment']['payable_amount'],
                ];
            }

            return [
                'footer'                  => $footer['si_payment'],
                'left_div'                => $cancellation_option,
                'right_div'               => $payment_option['si_payment'],
                'selected_payment_method' => 'si_payment',
            ];
        } else if (isset($data['payment_methods']['partial_payment']) === true) {
            // In case of non instant request.
            if ($page === 'prepayment' && isset($data['is_instant_bookable']) === true && $data['is_instant_bookable'] === 0) {
                $footer['partial_payment'] = [
                    'title'        => '',
                    'sub'          => '',
                    'button_text'  => 'Request availability',
                    'final_amount' => $data['payment_methods']['partial_payment']['payable_amount'],
                ];
            }

            return [
                'footer'                  => $footer['partial_payment'],
                'left_div'                => $cancellation_option,
                'right_div'               => $payment_option['partial_payment'],
                'selected_payment_method' => 'partial_payment',
            ];
        } else {
            // In case of non instant request.
            if ($page === 'prepayment' && isset($data['is_instant_bookable']) === true && $data['is_instant_bookable'] === 0) {
                $footer['full_payment'] = [
                    'title'        => '',
                    'sub'          => '',
                    'button_text'  => 'Request availability',
                    'final_amount' => $data['payment_methods']['full_payment']['payable_amount'],
                ];
            }

            return [
                'footer'                  => $footer['full_payment'],
                'left_div'                => $cancellation_option,
                'right_div'               => $payment_option['full_payment'],
                'selected_payment_method' => 'full_payment',
            ];
        }//end if

    }//end getFooterAndCancellationPolicyDivData()


    /**
     * Get Attraction Images.
     *
     * @param array  $headers       Headers Data.
     * @param string $property_vlat Property Virtual Latitude.
     * @param string $property_vlng Property Virtual Longitude.
     *
     * @return array
     */
    public function getAttractionImages(array $headers, string $property_vlat, string $property_vlng)
    {
        $attractions       = new NeighbourhoodAttractions;
        $attraction_images = $attractions->getNeighbourhoodAttractionImages($property_vlat, $property_vlng);

        // Get base_url for image (s3 path,directory) based on device type (size, connection strength).
        $base_url = Property::imageBaseUrlAsPerDeviceSizeAndConnection($headers);

        $property_attraction_images = [];

        // Iterate over each property image.
        foreach ($attraction_images as $images) {
            $property_image = $base_url['image_base_url'].$images['image'];
            $image_caption  = $images['caption'];

            array_push($property_attraction_images, ['image' => $property_image, 'caption' => $image_caption]);
        }

        return $property_attraction_images;

    }//end getAttractionImages()


    /**
     * Clone Property.
     *
     * @param Property $property       Property Model.
     * @param string   $property_title Property title.
     * @param integer  $converted_by   Admin Id.
     *
     * @return boolean|integer
     */
    public static function cloneProperty(Property $property, string $property_title, int $converted_by)
    {
        $clone_property = Property::cloneProperty($property, $property_title, $converted_by);
        if ($clone_property === false) {
            return false;
        }

        $pid = $clone_property->id;

        $lead_count_update = HostConversionLead::updateLeadCount($property->lead_id);

        // Fetch property detail corresponding to current property model and replicate.
        $property_details_clone = PropertyDetail::clonePropertyDetail($property, $pid);
        if ($property_details_clone === false) {
            return false;
        }

        $property_price_clone = PropertyPricing::clonePropertyPrice($property, $pid);
        if ($property_price_clone === false) {
            return false;
        }

        return $pid;

    }//end cloneProperty()


    /**
     * Get Prive properties.
     *
     * @param integer $prive_owner_id Prive Owner id.
     * @param array   $headers        Headers.
     * @param integer $offset         Offset.
     * @param integer $limit          Limit.
     * @param boolean $active         Active.
     *
     * @return array
     */
    public function getPriveProperties(int $prive_owner_id, array $headers, int $offset=0, int $limit=100, bool $active=true)
    {
        $device_type       = (empty($headers['device-type']) === false) ? $headers['device-type'][0] : '';
        $property          = new Property;
        $property_listings = $property->getPriveProperties($prive_owner_id, $offset, $limit, $active);

           // Get property ids (unique) listed by prive.
        $property_ids = array_unique(array_column($property_listings, 'id'));

         // Get first property image to display.
        $properties_images = PropertyImage::getPropertiesImagesByIds($property_ids, $headers, 1);

        $properties_data = [];
        $property_list   = [];

        // Web is device type for msite.
        // Webiste is device type of website.
        if ($device_type === 'web') {
            $url = MSITE_URL;
        } else {
            $url = WEBSITE_URL;
        }

        foreach ($property_listings as $key => $one_property) {
            $property_hash_id      = Helper::encodePropertyId($one_property['id']);
            $properties_data['id'] = $one_property['id'];

            $properties_data['property_hash_id']  = $property_hash_id;
            $properties_data['properties_images'] = $properties_images[$one_property['id']];
            $properties_data['property_title']    = ucfirst($one_property['title']);
            $properties_data['url']               = $url.'/rooms/'.$property_hash_id;
            $properties_data['units']             = $one_property['units'];
            $properties_data['city']              = $one_property['city'];
            $properties_data['state']             = $one_property['state'];
            $properties_data['per_night_price']   = Helper::getFormattedMoney($one_property['per_night_price'], $one_property['currency']);
            $properties_data['status']            = ($one_property['status'] === 1 && $one_property['enabled'] === 1) ? 1 : 0;
            $property_list[] = $properties_data;
        }

        return $property_list;

    }//end getPriveProperties()


    /**
     * Get Prive Manager properties.
     *
     * @param integer $prive_manager_id Prive Manager id.
     * @param array   $headers          Headers.
     * @param integer $offset           Offset.
     * @param integer $limit            Limit.
     * @param boolean $active           Active.
     *
     * @return array
     */
    public function getPriveManagerProperties(int $prive_manager_id, array $headers, int $offset=0, int $limit=100, bool $active=true)
    {
        $property_listings = $this->property->getPriveManagerProperties($prive_manager_id, $offset, $limit, $active);

        // Get property ids (unique) listed by prive.
        $property_ids = array_unique(array_column($property_listings, 'id'));

        // Total Properties Counts.
        $total_count = $this->property->getPriveMangerPropertyCounts($prive_manager_id, $active);

        // Get first property image to display.
        $property_images = PropertyImage::getPropertiesImagesByIds($property_ids, $headers, 1);

        // All country codes mapped with names.
        $countries = CountryCodeMapping::getCountries();

        $property_list = [];

        foreach ($property_listings as $key => $one_property) {
            $property_hash_id = Helper::encodePropertyId($one_property['id']);
            $country          = $countries[$one_property['country']];

            $properly_title = $one_property['id'].' • '.ucfirst($one_property['title']);

            if (empty($one_property['properly_title']) === false) {
                $properly_title = $one_property['id'].' • '.ucfirst($one_property['properly_title']);
            }

            $property_tile   = [
                'property_hash_id' => $property_hash_id,
                'property_images'  => (array_key_exists($one_property['id'], $property_images) === true) ? $property_images[$one_property['id']] : [],
                'property_title'   => $properly_title,
                'location'         => [
                    'area'          => ucfirst($one_property['area']),
                    'city'          => ucfirst($one_property['city']),
                    'state'         => ucfirst($one_property['state']),
                    'country'       => $country,
                    // Country name from code.
                    'location_name' => Helper::formatLocation($one_property['area'], $one_property['city'], $one_property['state']),
                    'latitude'      => $one_property['latitude'],
                    'longitude'     => $one_property['longitude'],
                ],
                'url'              => VERSION_PREFIX.'/property/'.$property_hash_id,
                'units'            => $one_property['units'],
                'per_night_price'  => Helper::getFormattedMoney($one_property['per_night_price'], $one_property['currency']),
                'occupancy_rate'   => 0,
            ];
            $property_list[] = $property_tile;
        }//end foreach

        return [
            'property_list' => $property_list,
            'total_count'   => $total_count,
        ];

    }//end getPriveManagerProperties()


    /**
     * Get Prive Manager property Ids.
     *
     * @param integer $prive_manager_id Prive Manager id.
     * @param array   $selected_pids    Selected Property Id.
     * @param boolean $active           Active.
     *
     * @return array
     */
    public function getPriveManagerPropertyIds(int $prive_manager_id, array $selected_pids=[], bool $active=true)
    {
        $property_listings = $this->property->getPriveManagerProperties($prive_manager_id, 0, 1000, $active);

        $properties = [];

        foreach ($property_listings as $key => $property) {
            $properly_title = $property['id'].' • '.ucfirst($property['title']);

            if (empty($property['properly_title']) === false) {
                $properly_title = $property['id'].' • '.ucfirst($property['properly_title']);
            }

            $properties[] = [
                'property_hash_id' => Helper::encodePropertyId($property['id']),
                'title'            => $properly_title,
                'selected'         => (in_array($property['id'], $selected_pids) === true) ? 1 : 0,
            ];
        }//end foreach

        return $properties;

    }//end getPriveManagerPropertyIds()


    /**
     * Get all of property description in proper formatting as array.
     *
     * @param integer $property_id Property id to fetch property description for.
     * @param string  $check_in    Checkin date.
     * @param string  $check_out   Checko out date.
     *
     * @return array property description
     */
    public static function getPropertyDescription(int $property_id, string $check_in, string $check_out)
    {
        // Get property description details.
        $property_details = PropertyDetail::getAllDetailsOfProperty($property_id);

        $description   = [];
        $description[] = [
            'key'   => 'space',
            'title' => 'The space',
            'value' => $property_details['space'],
        ];
        $description[] = [
            'key'   => 'extra_details',
            'title' => 'Extra details',
            'value' => $property_details['extra_details'],
        ];
        $description[] = [
            'key'   => 'local_experience',
            'title' => 'Local Experience',
            'value' => $property_details['local_experience'],
        ];
        $description[] = [
            'key'   => 'interaction_with_guest',
            'title' => 'Guest interaction',
            'value' => $property_details['interaction_with_guest'],
        ];

        if (empty($property_details['policy_services']) === false) {
            $description[] = [
                'key'   => 'policy_services',
                'title' => 'Policies',
                'value' => $property_details['policy_services'],
            ];
        } else {
            if (empty($property_details['guest_brief']) === false) {
                $description[] = [
                    'key'   => 'guest_brief',
                    'title' => 'Guest brief',
                    'value' => $property_details['guest_brief'],
                ];
            } else {
                $description[] = [
                    'key'   => 'guest_brief',
                    'title' => 'Guest brief',
                    'value' => '',
                ];
            }

            if (empty($property_details['house_rules']) === false) {
                $description[] = [
                    'key'   => 'house_rules',
                    'title' => 'House rules',
                    'value' => $property_details['house_rules'],
                ];
            } else {
                $description[] = [
                    'key'   => 'house_rules',
                    'title' => 'House rules',
                    'value' => '',
                ];
            }
        }//end if

        $check_in  = @Carbon::createFromFormat('H:i:s', $check_in)->format('g:i a');
        $check_out = @Carbon::createFromFormat('H:i:s', $check_out)->format('g:i a');

        $description[] = [
            'key'      => 'timings',
            'title'    => 'Timings',
            'value'    => 'Followings are the timings:',
            'checkin'  => [
                'title' => 'Check in',
                'value' => $check_in,
            ], 'checkout' => [
                'title' => 'Check out',
                'value' => $check_out,
            ],
        ];

        return [
            'description'  => $description,
            'how_to_reach' => $property_details['how_to_reach'],
            'usp'          => $property_details['usp'],
        ];

    }//end getPropertyDescription()


    /**
     * Send Property Listing Emails to Host
     *
     * @param Property $property   Property Id.
     * @param string   $host_email Host Email.
     *
     * @return void
     */
    public function sendPropertyLisingMailToHost(Property $property, string $host_email)
    {
        $to_email         = $host_email;
        $property_hash_id = Helper::encodePropertyId($property->id);
        $property_title   = ucfirst($property->title);
        $property_type    = PropertyType::getPropertyTypeByPid($property->id);

        $this->email_service->sendPropertyListingUnderReviewEmailToHost(
            $to_email,
            $property_title,
            $property_type,
            $property_hash_id
        );

    }//end sendPropertyLisingMailToHost()


    /**
     * Send Property Emails to Admin
     *
     * @param Property $property Property Object.
     *
     * @return void
     */
    public function sendPropertyLisingMailToAdmin(Property $property)
    {
        $property_hash_id = Helper::encodePropertyId($property->id);
        $property_title   = ucfirst($property->title);

        $to_emails = LISTING_ADMIN_EMAIL_FOR_NOTIFICATIONS;

        // Find property managers email.
        $relationship_manager = RelationshipManager::getRMEmailOfHost($property->user_id);

        if (empty($relationship_manager) === false) {
            $to_emails[] = $relationship_manager->email;
        }

        $this->email_service->sendPropertyListingUnderReviewToBDTeamEmail(
            array_unique($to_emails),
            $property_title,
            $property_hash_id
        );

    }//end sendPropertyLisingMailToAdmin()


    /**
     * Send Proeprty Modification Emails to Admin
     *
     * @param Property $property     Property Object.
     * @param array    $updates_data Updated data.
     *
     * @return void
     */
    public function sendPropertyModifyMailToAdmin(Property $property, array $updates_data)
    {
        $property_hash_id = Helper::encodePropertyId($property->id);
        $property_title   = ucfirst($property->title);

        $to_emails = LISTING_ADMIN_EMAIL_FOR_NOTIFICATIONS;

        // Find property managers email.
        $relationship_manager = RelationshipManager::getRMEmailOfHost($property->user_id);

        if (empty($relationship_manager) === false) {
            $to_emails[] = $relationship_manager->email;
        }

        $this->email_service->sendPropertyListingModifyToBDTeamEmail(
            array_unique($to_emails),
            $property_title,
            $property_hash_id,
            $updates_data
        );

    }//end sendPropertyModifyMailToAdmin()


    /**
     * Get Traveller Prive properties.
     *
     * @param array   $headers Headers.
     * @param integer $offset  Offset.
     * @param integer $limit   Limit.
     * @param array   $city    City.
     *
     * @return array
     */
    public function getPriveTravellerProperties(array $headers, int $offset=0, int $limit=100, array $city=[])
    {
        $total_count           = 0;
        $city_wise_total_count = 0;
        $property_listings     = Property::getTravellerPriveProperties($offset, $limit, $city);

        // Get property ids (unique) listed by prive.
        $property_ids = array_unique(array_column($property_listings, 'id'));

         // Get first property image to display.
        $properties_images = PropertyImage::getPropertiesImagesByIds($property_ids, $headers, 1);

        $properties_data = [];
        $property_list   = [];

        foreach ($property_listings as $key => $one_property) {
            $property_hash_id = Helper::encodePropertyId($one_property['id']);

            $properties_data['property_hash_id']  = $property_hash_id;
            $properties_data['properties_images'] = $properties_images[$one_property['id']];
            $properties_data['property_title']    = ucfirst($one_property['title']);
            $properties_data['url']               = WEBSITE_URL.'/rooms/'.$property_hash_id;
            $properties_data['city']              = $one_property['city'];
            $properties_data['state']             = $one_property['state'];
            $properties_data['area']              = $one_property['area'];
            $property_list[] = $properties_data;
        }

        // Get Total Prive property count citywise.
        $prive_cities = Property::getPriveCityWisePropertyCount();

        foreach ($prive_cities as $key => $value) {
            $cities[$key]['name']     = $value['city'];
            $cities[$key]['selected'] = (in_array(strtolower($value['city']), $city) === true) ? 1 : 0;
            if (in_array(strtolower($value['city']), $city) === true) {
                $city_wise_total_count += $value['property_count'];
            } else {
                $total_count += $value['property_count'];
            }
        }

         return [
             'property_list' => $property_list,
             'total_count'   => (empty($city_wise_total_count) === false) ? $city_wise_total_count : $total_count,
             'cities'        => $cities,
         ];

    }//end getPriveTravellerProperties()


     /**
      * Get Prive Manager property Ids.
      *
      * @param integer $prive_manager_id Prive Manager id.
      * @param array   $selected_ids     Selected Team Id.
      *
      * @return array
      */
    public function getPriveManagerTeam(int $prive_manager_id, array $selected_ids=[])
    {
        $teams         = [];
        $property      = new ProperlyTeam;
        $team_listings = $property->getMemberFilterResult($prive_manager_id, ['status' => PROPERLY_TEAM_MEMBER_ACTIVE]);
        $properties    = [];

        foreach ($team_listings as $key => $team) {
            $teams[] = [
                'id'       => Helper::encodeUserId($team->id),
                'name'     => ucfirst($team->name.' '.$team->last_name),
                'role'     => $team->team_name,
                'selected' => (in_array($team->id, $selected_ids) === true) ? 1 : 0,
            ];
        }//end foreach

        return $teams;

    }//end getPriveManagerTeam()


    /**
     * Get Live Properties by owner Id.
     *
     * @param integer $prive_owner_id Prive Owner Id.
     *
     * @return array
     */
    public function getLivePropertiesByOwnerId(int $prive_owner_id)
    {
        $property_list   = [];
        $live_properties = Property::getLivePropertiesByOwnerId($prive_owner_id);

        foreach ($live_properties as $key => $one_property) {
            $property_hash_id = Helper::encodePropertyId($one_property['id']);

            $properties_data['property_hash_id'] = $property_hash_id;
            $properties_data['property_title']   = ucfirst($one_property['title']);
            $property_list[] = $properties_data;
        }

        return $property_list;

    }//end getLivePropertiesByOwnerId()


     /**
      * Get Prive Owner property Ids.
      *
      * @param integer $prive_owner_id Prive Owner id.
      * @param array   $selected_pids  Selected Property Id.
      * @param boolean $active         Active.
      *
      * @return array
      */
    public function getPriveOwnerPropertyIds(int $prive_owner_id, array $selected_pids=[], bool $active=true)
    {
        $property          = new Property;
        $property_listings = $property->getPriveProperties($prive_owner_id, 0, 1000, $active);

        $properties = [];

        foreach ($property_listings as $key => $property) {
            $properties[] = [
                'property_hash_id' => Helper::encodePropertyId($property['id']),
                'title'            => $property['id'].' • '.ucfirst($property['title']),
                'selected'         => (in_array($property['id'], $selected_pids) === true) ? 1 : 0,
            ];
        }//end foreach

        return $properties;

    }//end getPriveOwnerPropertyIds()


}//end class
