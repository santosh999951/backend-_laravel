<?php
/**
 * OfferBanner Controller containing methods related to Offer Banner page
 */

namespace App\Http\Controllers\v1_6\Admin;

use App\Http\Controllers\v1_6\Controller as Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Libraries\{ApiResponse, Helper};
use App\Libraries\v1_6\{OfferService,AwsService};
use App\Http\Requests\{PostHomeBannerRequest, PutHomeBannerRequest, DeleteHomeBannerRequest, PostPromotionalImageRequest};
use App\Http\Requests\{DeletePromoBannerRequest, PostPromoBannerRequest , PutPromoBannerRequest, PostOffersRequest , DeleteOffersRequest , PutOffersRequest};


/**
 * Class OfferBannerController
 */
class OfferBannerController extends Controller
{


    /**
     * Upload Promotional Images on S3
     *
     * @param \App\Http\PostPromotionalImageRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/admin/uploadPromotionalimage",
     *     tags={"Offers"},
     *     description="Upload Promotional Image.",
     *     operationId="offers.post.admin.postPromotionalImage",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/mobile_image_in_form"),
     * @SWG\Parameter(ref="#/parameters/web_image_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array containing name of both mobile and web images. "
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters."
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     *     ),
     * )
     */
    public function postPromotionalImage(PostPromotionalImageRequest $request)
    {
        $allowed_extension = implode(',', WATERMARK_ALLOWDED_IMAGES_EXTENSION);
        $input_params      = $request->input();

        // Mobile Image.
        $uploaded_image_mobile = $input_params['mobile_image'];
        if (empty($uploaded_image_mobile) === false) {
            $file_name_mobile      = $uploaded_image_mobile->getClientOriginalName();
            $file_path_mobile      = $uploaded_image_mobile->getPathName();
            $extension_mobile      = Helper::getImageExtension($file_name_mobile);
            $new_image_name_mobile = rand(100, 9999).'_'.uniqid().'_'.time().'.'.$extension_mobile;

            // Put MobileImage in S3 Bucket.
            $put_mobile_image = $this->putObjectInS3Bucket(S3_PROMOTIONAL_DIR_TMP.$new_image_name_mobile, $file_path_mobile);
            if ($put_mobile_image === false) {
                return ApiResponse::errorMessage('Error While Uploading Images');
            }
        }

        // Web Image.
        $uploaded_image_web = $input_params['web_image'];
        if (empty($uploaded_image_web) === false) {
            $file_name_web      = $uploaded_image_web->getClientOriginalName();
            $file_path_web      = $uploaded_image_web->getPathName();
            $extension_web      = Helper::getImageExtension($file_name_web);
            $new_image_name_web = rand(100, 9999).'_'.uniqid().'_'.time().'.'.$extension_web;

            // Put Web Image in S3 Bucket.
            $put_web_image = $this->putObjectInS3Bucket(S3_PROMOTIONAL_DIR_TMP.$new_image_name_web, $file_path_web);
            if ($put_web_image === false) {
                return ApiResponse::errorMessage('Error While Uploading Images');
            }
        }

        return ApiResponse::success(
            [
                'web_image'    => (empty($uploaded_image_web) === false) ? $new_image_name_web : '',
                'mobile_image' => (empty($uploaded_image_mobile) === false) ? $new_image_name_mobile : '',
                'message'      => 'Images uploaded successfully.',
            ]
        );

    }//end postPromotionalImage()


    /**
     * Get Offers listing
     *
     * @param \Illuminate\Http\Request $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/admin/offers",
     *     tags={"Offers"},
     *     description="get listing of offer data.",
     *     operationId="offers.get.admin.offers",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array containing offer data."
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     *     )
     * )
     */
    public function getOffers(Request $request)
    {
        // Get Offer data from services.
        $offers = OfferService::getOffers();

        if (empty($offers) === false) {
            return ApiResponse::success($offers);
        } else {
            return ApiResponse::errorMessage('Offer Not Found.');
        }

    }//end getOffers()


    /**
     * Add offers
     *
     * @param \App\Http\PostOffersRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/admin/offers",
     *     tags={"Offers"},
     *     description="save offer in offer tables.",
     *     operationId="offers.post.admin.offers",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/name_in_form"),
     * @SWG\Parameter(ref="#/parameters/title_in_form"),
     * @SWG\Parameter(ref="#/parameters/status_in_form"),
     * @SWG\Parameter(ref="#/parameters/default_in_form"),
     * @SWG\Parameter(ref="#/parameters/description_in_form"),
     * @SWG\Parameter(ref="#/parameters/images_in_form"),
     * @SWG\Parameter(ref="#/parameters/destination_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Offers successfully saved."
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters."
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="File Not Found on s3 server."
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="Error while inserting file.",
     *     )
     * )
     */
    public function postOffers(PostOffersRequest $request)
    {
        $input_params = $request->input();

        $is_default        = 0;
        $offer_images_data = [];
        $count             = 0;
        $image_data        = '';

        $offers = [

            'name'        => $input_params['name'],
            'title'       => $input_params['title'],
            'description' => json_encode($input_params['description']),
            'status'      => $input_params['status'],
            'default'     => $input_params['default'],
        ];

        if (isset($input_params['destination']) === true) {
            $offers['destination'] = $input_params['destination'];
        }

        if (isset($input_params['images']) === true) {
             // Json array of object Params web_image, mobile_image, default , sort.
            $image_data = $input_params['images'];
        }

         // Check If Offer Name already exist.
         $offer_name_exist = OfferService::getOffers($offers['name']);
        if (empty($offer_name_exist) === false) {
            return ApiResponse::errorMessage('Name of offer '.$offers['name'].' already exist');
        }

        if (empty($offers['default']) === false) {
            $offer_default_exist = OfferService::getOffers('', 0, $offers['default']);
            if ($offer_default_exist > 0) {
                $offer_disable_default = OfferService::disableOfferDefault();
            }
        }

        // Insert Offers in offer_banner table.
        $offer_id = OfferService::insertOffer($offers);

        $image_array = json_decode($image_data, true);
        $array_len   = count($image_array);
        if (empty($image_array) === false) {
            foreach ($image_array as $array_index => $offer_image) {
                if ($this->checkImageStructure($offer_image) === true) {
                    // Check  mobile image existance on s3.
                    $exists_on_s3_mobile = AwsService::doesObjectExist(S3_BUCKET, S3_PROMOTIONAL_DIR_TMP.$offer_image['mobile_image']);

                     // Check  Web image existance on s3.
                    $exists_on_s3_web = AwsService::doesObjectExist(S3_BUCKET, S3_PROMOTIONAL_DIR_TMP.$offer_image['web_image']);

                    if (($exists_on_s3_mobile === true) && ($exists_on_s3_web === true)) {
                        // Copy mobile image.
                        AwsService::copyObjectFromS3ToS3Bucket(
                            S3_BUCKET,
                            S3_OFFER_DIR_MOBILE.$offer_image['mobile_image'],
                            S3_BUCKET.'/'.S3_PROMOTIONAL_DIR_TMP.$offer_image['mobile_image'],
                            'public-read'
                        );

                        // Copy web image.
                        AwsService::copyObjectFromS3ToS3Bucket(
                            S3_BUCKET,
                            S3_OFFER_DIR_WEB.$offer_image['web_image'],
                            S3_BUCKET.'/'.S3_PROMOTIONAL_DIR_TMP.$offer_image['web_image'],
                            'public-read'
                        );

                        // Delete web image from s3 temp directory.
                        AwsService::deleteObjectInS3Bucket(
                            S3_BUCKET,
                            S3_PROMOTIONAL_DIR_TMP.$offer_image['web_image']
                        );
                        // Delete mobile image from s3 temp directory.
                        AwsService::deleteObjectInS3Bucket(
                            S3_BUCKET,
                            S3_PROMOTIONAL_DIR_TMP.$offer_image['mobile_image']
                        );
                    } else {
                        return ApiResponse::notFoundError(EC_NOT_FOUND, 'File you are passing not found on s3 server');
                    }//end if

                    if ($offer_image['default'] === 1 && ($is_default !== 1)) {
                        $is_default = 1;
                    } else if (($count === $array_len - 1) && ($is_default !== 1)) {
                         $is_default = 1;
                    } else {
                        $is_default = 0;
                    }

                    $offer_images_data[] = [
                        'mobile_image' => $offer_image['mobile_image'],
                        'web_image'    => $offer_image['web_image'],
                        'default'      => $is_default,
                        'sort'         => $offer_image['sort'],
                    ];
                }//end if

                $count++;
            }//end foreach
        }//end if

        foreach ($offer_images_data as $image_data) {
            $image_data['offer_id'] = $offer_id;

            // Insert data in Offer images table.
            $image_data = OfferService::insertOfferImage($image_data);
        }

        return ApiResponse::successMessage('Offers successfully saved.');

    }//end postOffers()


    /**
     * Update Offers
     *
     * @param \App\Http\PutOffersRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/admin/offers",
     *     tags={"Offers"},
     *     description="Returns success message when Offer updated successfully.",
     *     operationId="offers.put.admin.offers",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/id_in_form"),
     * @SWG\Parameter(ref="#/parameters/name_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/title_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/status_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/default_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/description_in_form"),
     * @SWG\Parameter(ref="#/parameters/imagedata_in_form"),
     * @SWG\Parameter(ref="#/parameters/destination_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Offers changed successfully."
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters."
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="There was some error while updating offers. Please try again.",
     *     )
     * )
     */
    public function putOffers(PutOffersRequest $request)
    {
        $input_params = $request->input();
        $is_default   = 0;
        $image_id     = [];
        $count        = 0;
        $offers       = [];
        $id           = $input_params['id'];

        // Check If  id existance.
        $check_offer_id_exist = OfferService::getOffer($id);
        if (empty($check_offer_id_exist) === true) {
            return ApiResponse::errorMessage('Offer Id '.$id.' not exist');
        }

        if (isset($input_params['default']) === true) {
            $offers['default']   = $input_params['default'];
            $offer_default_exist = OfferService::getOffers('', 0, $offers['default']);
            if ($offer_default_exist > 0) {
                $offer_disable_default = OfferService::disableOfferDefault();
            }
        }

        // Check Offer Name existance.
        if (isset($input_params['name']) === true) {
            $check_offer_name_exist = OfferService::getOffers($input_params['name'], $id);
            if (empty($check_offer_name_exist) === false) {
                return ApiResponse::errorMessage('Name of offer '.$input_params['name'].' already exist');
            }
        }

        if (isset($input_params['imagedata']) === true) {
            $images      = $input_params['imagedata'];
            $image_array = json_decode($images, true);

            $array_len = count($image_array);
            if (empty($image_array) === false) {
                foreach ($image_array as $array_index => $image_data) {
                    if (array_key_exists('action', $image_data) === true) {
                        // Fetch id of all images where action is delete.
                        if (isset($image_data['action']) === true && ($image_data['action'] === 'delete') && array_key_exists('id', $image_data) === true) {
                            $image_id[] = $image_data['id'];
                        } else if (isset($image_data['action']) === true && ($image_data['action'] === 'insert') && ($this->checkImageStructure($image_data) === true)) {
                            // Check  mobile image existance on s3.
                            $exists_on_s3_mobile = AwsService::doesObjectExist(S3_BUCKET, S3_PROMOTIONAL_DIR_TMP.$image_data['mobile_image']);

                            // Check  web image existance on s3.
                            $exists_on_s3_web = AwsService::doesObjectExist(S3_BUCKET, S3_PROMOTIONAL_DIR_TMP.$image_data['web_image']);

                            if (($exists_on_s3_mobile === true) && ($exists_on_s3_web === true)) {
                                // Copy mobile image.
                                AwsService::copyObjectFromS3ToS3Bucket(
                                    S3_BUCKET,
                                    S3_OFFER_DIR_MOBILE.$image_data['mobile_image'],
                                    S3_BUCKET.'/'.S3_PROMOTIONAL_DIR_TMP.$image_data['mobile_image'],
                                    'public-read'
                                );

                                // Copy web image.
                                AwsService::copyObjectFromS3ToS3Bucket(
                                    S3_BUCKET,
                                    S3_OFFER_DIR_WEB.$image_data['web_image'],
                                    S3_BUCKET.'/'.S3_PROMOTIONAL_DIR_TMP.$image_data['web_image'],
                                    'public-read'
                                );

                                // Delete web image from s3 temp directory.
                                AwsService::deleteObjectInS3Bucket(
                                    S3_BUCKET,
                                    S3_PROMOTIONAL_DIR_TMP.$image_data['web_image']
                                );
                                // Delete mobile image from s3 temp directory.
                                AwsService::deleteObjectInS3Bucket(
                                    S3_BUCKET,
                                    S3_PROMOTIONAL_DIR_TMP.$image_data['mobile_image']
                                );

                                $check_default = OfferService::getDefaultImages($id);
                                if ($image_data['default'] === 1) {
                                    if (empty($check_default) === false) {
                                        $disable_previous_default = OfferService::disableDefault($id, ['default' => 0]);
                                    }

                                    $is_default = 1;
                                } else {
                                    $image_data['default'] = 0;
                                }

                                $image_data['offer_id'] = $id;
                                unset($image_data['action']);
                                $response = OfferService::insertOfferImage($image_data);
                            } else {
                                return ApiResponse::notFoundError(EC_NOT_FOUND, 'File you are Inserting not found on s3 server');
                            }//end if
                        } else if (isset($image_data['action']) === true && ($image_data['action'] === 'update') && array_key_exists('id', $image_data) === true) {
                            if (array_key_exists('mobile_image', $image_data) === true) {
                                // Check  mobile image existance on s3.
                                $exists_on_s3_mobile = AwsService::doesObjectExist(S3_BUCKET, S3_PROMOTIONAL_DIR_TMP.$image_data['mobile_image']);
                            }

                            if ($exists_on_s3_mobile === true) {
                                // Copy mobile image.
                                AwsService::copyObjectFromS3ToS3Bucket(
                                    S3_BUCKET,
                                    S3_OFFER_DIR_MOBILE.$image_data['mobile_image'],
                                    S3_BUCKET.'/'.S3_PROMOTIONAL_DIR_TMP.$image_data['mobile_image'],
                                    'public-read'
                                );

                                // Delete mobile image from s3 temp directory.
                                AwsService::deleteObjectInS3Bucket(
                                    S3_BUCKET,
                                    S3_PROMOTIONAL_DIR_TMP.$image_data['mobile_image']
                                );
                            }

                            if (array_key_exists('web_image', $image_data) === true) {
                                // Check  web image existance on s3.
                                $exists_on_s3_web = AwsService::doesObjectExist(S3_BUCKET, S3_PROMOTIONAL_DIR_TMP.$image_data['web_image']);
                            }

                            if ($exists_on_s3_web === true) {
                                // Copy mobile image.
                                AwsService::copyObjectFromS3ToS3Bucket(
                                    S3_BUCKET,
                                    S3_OFFER_DIR_MOBILE.$image_data['web_image'],
                                    S3_BUCKET.'/'.S3_PROMOTIONAL_DIR_TMP.$image_data['web_image'],
                                    'public-read'
                                );

                                // Delete mobile image from s3 temp directory.
                                AwsService::deleteObjectInS3Bucket(
                                    S3_BUCKET,
                                    S3_PROMOTIONAL_DIR_TMP.$image_data['web_image']
                                );
                            }

                            if (($exists_on_s3_mobile === true) && ($exists_on_s3_web === true)) {
                                // Check if default image for this offer id exist.
                                $check_default = OfferService::getDefaultImages($id, $image_data['id']);
                                if ($image_data['default'] === 1 && ($is_default === 0)) {
                                    if (empty($check_default) === false) {
                                        // Disable default all the images for this offer_id.
                                        $disable_previous_default = OfferService::disableDefault($id, ['default' => 0]);
                                    }
                                } else {
                                    $image_data['default'] = 0;
                                }

                                $image_data['offer_id'] = $id;
                                unset($image_data['action']);
                                $response = OfferService::updateOfferImage($image_data);
                            } else {
                                return ApiResponse::notFoundError(EC_NOT_FOUND, 'File you are Inserting not found on s3 server');
                            }//end if
                        }//end if

                        $count++;
                    }//end if
                }//end foreach

                // Delete All images whose action is delete.
                $response = OfferService::deleteMultipleOfferImage($image_id);
            }//end if
        }//end if

        if (isset($input_params['status']) === true) {
            $offers['status'] = $input_params['status'];
        }

        if (isset($input_params['name']) === true) {
            $offers['name'] = $input_params['name'];
        }

        if (isset($input_params['title']) === true) {
            $offers['title'] = $input_params['title'];
        }

        if (isset($input_params['description']) === true) {
            $offers['description'] = json_encode($input_params['description']);
        }

        if (isset($input_params['destination']) === true) {
            $offers['destination'] = $input_params['destination'];
        }

        $offers['id'] = $id;
        // Update Offers In Offer_banner table.
        $response = OfferService::updateOffer($offers);

        if ($response === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'Offers not Updated.');
        }

        return ApiResponse::successMessage('Offers successfully Updated.');

    }//end putOffers()


    /**
     * Delete Offer
     *
     * @param \App\Http\DeleteOffersRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Delete(
     *     path="/v1.6/admin/offers",
     *     tags={"Offers"},
     *     description="Returns success message when Offer Deleted successfully.",
     *     operationId="offers.admin.delete.offers",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/offer_id_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Offers changed successfully."
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters."
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="Offers not Deleted || OffersImages not Deleted. ",
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Offers Id Not Found. ",
     *     )
     * )
     */
    public function deleteOffer(DeleteOffersRequest $request)
    {
        $input_params      = $request->input();
        $offers_id         = $input_params['offer_id'];
        $check_exist_image = OfferService::getOffer($offers_id);
        if (empty($check_exist_image) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Offer Id not Found');
        }

        $response_image = OfferService::deleteImageWithOfferId($offers_id);
        if ($response_image <= 0) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'OffersImages not Deleted.');
        }

        $response_offer = OfferService::deleteOffer($offers_id);
        if ($response_offer === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'Offers not Deleted.');
        }

        return ApiResponse::successMessage('Offers successfully Deleted .');

    }//end deleteOffer()


    /**
     * Insert Home Banner in database
     *
     * @param \App\Http\PostHomeBannerRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/admin/homebanner",
     *     tags={"Homebanners"},
     *     description="Insert Home Banners.",
     *     operationId="homebanner.post.admin.homebanner",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/mobile_image_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/status_in_form"),
     * @SWG\Parameter(ref="#/parameters/destination_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns Successful message. "
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters."
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="File Not Found on s3 server."
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     *     ),
     * )
     */
    public function postHomeBanner(PostHomeBannerRequest $request)
    {
        $input_params = $request->input();

        $mobile_image = $input_params['mobile_image_name'];

        if (empty($mobile_image) === false) {
            // Check  mobile image existance on s3.
            $exists_on_s3_mobile = AwsService::doesObjectExist(S3_BUCKET, S3_PROMOTIONAL_DIR_TMP.$mobile_image);
            if (($exists_on_s3_mobile === true)) {
                // Copy mobile image.
                AwsService::copyObjectFromS3ToS3Bucket(
                    S3_BUCKET,
                    S3_HOMEBANNER_DIR_MOBILE.$mobile_image,
                    S3_BUCKET.'/'.S3_PROMOTIONAL_DIR_TMP.$mobile_image,
                    'public-read'
                );
                // Delete mobile image from s3 temp directory.
                AwsService::deleteObjectInS3Bucket(
                    S3_BUCKET,
                    S3_PROMOTIONAL_DIR_TMP.$mobile_image
                );
            } else {
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'File you are passing not found on s3 server');
            }//end if
        }

        $home_banner = [

            'image'       => $mobile_image,
            'status'      => $input_params['status'],
            'destination' => $input_params['destination'],
        ];
        $save_banner = OfferService::insertHomeBanner($home_banner);
        return ApiResponse::success(
            ['message' => 'Home banner succesfully saved']
        );

    }//end postHomeBanner()


    /**
     * Update Home Banner
     *
     * @param \App\Http\PutHomeBannerRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/admin/homebanner",
     *     tags={"Homebanners"},
     *     description="Returns success message when Banner updated successfully.",
     *     operationId="homebanner.put.admin.homebanner",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/id_in_form"),
     * @SWG\Parameter(ref="#/parameters/mobile_image_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/status_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/destination_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Banner successfully Updated."
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters."
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="There was some error while updating homebanner. Please try again.",
     *     )
     * )
     */
    public function putHomeBanner(PutHomeBannerRequest $request)
    {
        $input_params = $request->input();

        $banner       = [];
        $banner['id'] = $input_params['id'];

        // Check If  id existance.
        $check_banner_id_exist = OfferService::getHomeBanner($banner['id']);
        if (empty($check_banner_id_exist) === true) {
            return ApiResponse::errorMessage('Banner Id '.$banner['id'].' not exist');
        }

        if (isset($input_params['mobile_image_name']) === true) {
            $mobile_image = $input_params['mobile_image_name'];
        }

        if (empty($mobile_image) === false) {
            $banner['image'] = $mobile_image;
            // Check  mobile image existance on s3.
            $exists_on_s3_mobile = AwsService::doesObjectExist(S3_BUCKET, S3_PROMOTIONAL_DIR_TMP.$mobile_image);
            if (($exists_on_s3_mobile === true)) {
                // Copy mobile image.
                AwsService::copyObjectFromS3ToS3Bucket(
                    S3_BUCKET,
                    S3_HOMEBANNER_DIR_MOBILE.$mobile_image,
                    S3_BUCKET.'/'.S3_PROMOTIONAL_DIR_TMP.$mobile_image,
                    'public-read'
                );
                // Delete mobile image from s3 temp directory.
                AwsService::deleteObjectInS3Bucket(
                    S3_BUCKET,
                    S3_PROMOTIONAL_DIR_TMP.$mobile_image
                );
            } else {
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'File you are passing not found on s3 server');
            }//end if
        }//end if

        if (isset($input_params['status']) === true) {
            $banner['status'] = $input_params['status'];
        }

        if (empty($input_params['destination']) === false) {
            $banner['destination'] = $input_params['destination'];
        }

        // Update Offers In home_banners table.
        $response = OfferService::updateBanner($banner);

        if ($response === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'Bannner not Updated.');
        }

        return ApiResponse::successMessage('Banner successfully Updated.');

    }//end putHomeBanner()


    /**
     * Delete Home Banner
     *
     * @param \App\Http\DeleteHomeBannerRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Delete(
     *     path="/v1.6/admin/homebanner",
     *     tags={"Homebanners"},
     *     description="Returns success message when Banner Deleted successfully.",
     *     operationId="homebanners.admin.delete.homebanner",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/id_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Banner successfully Deleted"
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters."
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="Banner not Deleted ",
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Banner Id not Exist. ",
     *     )
     * )
     */
    public function deleteHomeBanner(DeleteHomeBannerRequest $request)
    {
        $input_params = $request->input();

        $banner_id      = $input_params['id'];
        $check_exist_id = OfferService::getHomeBanner($banner_id);
        if (empty($check_exist_id) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Banner Id not Exist');
        }

        $response_banner = OfferService::deleteBanner($banner_id);
        if ($response_banner === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'Banner not Deleted.');
        }

        return ApiResponse::successMessage('Banner successfully Deleted .');

    }//end deleteHomeBanner()


    /**
     * Get Banners listing
     *
     * @param \Illuminate\Http\Request $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/admin/homebanner",
     *     tags={"Homebanners"},
     *     description="get listing of home banners.",
     *     operationId="homebanners.get.admin.homebanner",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array containing Home Banners."
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     *     )
     * )
     */
    public function getHomeBanner(Request $request)
    {
        // Get Banner data from services.
        $banners = OfferService::getBanners();

        if (empty($banners) === false) {
            return ApiResponse::success($banners);
        } else {
            return ApiResponse::errorMessage('Home Banner Not Exist.');
        }

    }//end getHomeBanner()


     /**
      * Insert Promo Banner in database
      *
      * @param \App\Http\PostPromoBannerRequest $request Http request object.
      *
      * @return \Illuminate\Http\JsonResponse
      *
      * @SWG\Post(
      *     path="/v1.6/admin/promobanner",
      *     tags={"Promobanners"},
      *     description="Insert Promo Banners.",
      *     operationId="promobanner.post.admin.promobanner",
      *     produces={"application/json"},
      * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
      * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
      * @SWG\Parameter(ref="#/parameters/mobile_image_name_in_form"),
      * @SWG\Parameter(ref="#/parameters/web_image_name_in_form"),
      * @SWG\Parameter(ref="#/parameters/status_in_form"),
      * @SWG\Parameter(ref="#/parameters/name_in_form"),
      * @SWG\Response(
      *         response=200,
      *         description="Returns Successful message. "
      *     ),
      * @SWG\Response(
      *         response=400,
      *         description="Missing or invalid parameters."
      *     ),
      * @SWG\Response(
      *         response=404,
      *         description="File Not Found on s3 server."
      *     ),
      * @SWG\Response(
      *         response=401,
      *         description="Unauthorized action.",
      *     ),
      * )
      */
    public function postPromoBanner(PostPromoBannerRequest $request)
    {
        $input_params = $request->input();
        $name         = $input_params['name'];
        $status       = $input_params['status'];
        $mobile_image = '';
        $web_image    = '';

        if (isset($input_params['mobile_image_name']) === true) {
            $mobile_image = $input_params['mobile_image_name'];
        }

        if (isset($input_params['web_image_name']) === true) {
            $web_image = $input_params['web_image_name'];
        }

        // Check If Offer Name already exist.
         $promo_name_exist = OfferService::getPromoBanner(0, $name);
        if (empty($promo_name_exist) === false) {
               return ApiResponse::errorMessage('Name of promo banner '.$name.' already exist');
        }

        if (empty($mobile_image) === false) {
            // Check  mobile image existance on s3.
            $exists_on_s3_mobile = AwsService::doesObjectExist(S3_BUCKET, S3_PROMOTIONAL_DIR_TMP.$mobile_image);
            if (($exists_on_s3_mobile === true)) {
                // Copy mobile image.
                AwsService::copyObjectFromS3ToS3Bucket(
                    S3_BUCKET,
                    S3_PROMOBANNER_DIR_MOBILE.$mobile_image,
                    S3_BUCKET.'/'.S3_PROMOTIONAL_DIR_TMP.$mobile_image,
                    'public-read'
                );
                // Delete mobile image from s3 temp directory.
                AwsService::deleteObjectInS3Bucket(
                    S3_BUCKET,
                    S3_PROMOTIONAL_DIR_TMP.$mobile_image
                );
            } else {
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'File you are passing not found on s3 server');
            }//end if
        }

        if (empty($web_image) === false) {
            // Check  web image existance on s3.
            $exists_on_s3_web = AwsService::doesObjectExist(S3_BUCKET, S3_PROMOTIONAL_DIR_TMP.$web_image);
            if (($exists_on_s3_web === true)) {
                // Copy web image.
                AwsService::copyObjectFromS3ToS3Bucket(
                    S3_BUCKET,
                    S3_PROMOBANNER_DIR_WEB.$web_image,
                    S3_BUCKET.'/'.S3_PROMOTIONAL_DIR_TMP.$web_image,
                    'public-read'
                );
                // Delete web image from s3 temp directory.
                AwsService::deleteObjectInS3Bucket(
                    S3_BUCKET,
                    S3_PROMOTIONAL_DIR_TMP.$web_image
                );
            } else {
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'File you are passing not found on s3 server');
            }//end if
        }

        $promo_banner = [

            'web_image'    => $web_image,
            'mobile_image' => $mobile_image,
            'status'       => $status,
            'name'         => $name,
        ];
        $save_banner  = OfferService::insertPromoBanner($promo_banner);
        return ApiResponse::success(
            ['message' => 'Promo banner succesfully saved']
        );

    }//end postPromoBanner()


    /**
     * Update Promo Banner
     *
     * @param \App\Http\PutPromoBannerRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/admin/promobanner",
     *     tags={"Promobanners"},
     *     description="Returns success message when Banner updated successfully.",
     *     operationId="promobanner.put.admin.promobanner",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/id_in_form"),
     * @SWG\Parameter(ref="#/parameters/web_image_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/mobile_image_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/status_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/name_optional_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Banner successfully Updated."
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters."
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="There was some error while updating Promo Banner. Please try again.",
     *     )
     * )
     */
    public function putPromoBanner(PutPromoBannerRequest $request)
    {
        $input_params = $request->input();

        $banner       = [];
        $banner['id'] = $input_params['id'];
        $mobile_image = '';
        $web_image    = '';

        // Check If  id existance.
        $check_banner_id_exist = OfferService::getPromoBanner($banner['id']);
        if (empty($check_banner_id_exist) === true) {
            return ApiResponse::errorMessage('Banner Id '.$banner['id'].' not exist');
        }

        if (isset($input_params['name']) === true) {
            $banner['name'] = $input_params['name'];

            // Check Promo Name existance.
            $check_promo_name_exist = OfferService::getPromoBanner($banner['id'], $banner['name']);
            if (empty($check_promo_name_exist) === false) {
                return ApiResponse::errorMessage('Name of Promo '.$banner['name'].' already exist');
            }
        }

        if (isset($input_params['mobile_image_name']) === true) {
            $mobile_image = $input_params['mobile_image_name'];
        }

        if (isset($input_params['web_image_name']) === true) {
            $web_image = $input_params['web_image_name'];
        }

        if (empty($mobile_image) === false) {
            $banner['mobile_image'] = $mobile_image;
            // Check  mobile image existance on s3.
            $exists_on_s3_mobile = AwsService::doesObjectExist(S3_BUCKET, S3_PROMOTIONAL_DIR_TMP.$mobile_image);
            if (($exists_on_s3_mobile === true)) {
                // Copy mobile image.
                AwsService::copyObjectFromS3ToS3Bucket(
                    S3_BUCKET,
                    S3_PROMOBANNER_DIR_MOBILE.$mobile_image,
                    S3_BUCKET.'/'.S3_PROMOTIONAL_DIR_TMP.$mobile_image,
                    'public-read'
                );
                // Delete mobile image from s3 temp directory.
                AwsService::deleteObjectInS3Bucket(
                    S3_BUCKET,
                    S3_PROMOTIONAL_DIR_TMP.$mobile_image
                );
            } else {
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'File you are passing not found on s3 server');
            }//end if
        }//end if

        if (empty($web_image) === false) {
            $banner['web_image'] = $web_image;
            // Check  web image existance on s3.
            $exists_on_s3_web = AwsService::doesObjectExist(S3_BUCKET, S3_PROMOTIONAL_DIR_TMP.$web_image);
            if (($exists_on_s3_web === true)) {
                // Copy web image.
                AwsService::copyObjectFromS3ToS3Bucket(
                    S3_BUCKET,
                    S3_PROMOBANNER_DIR_WEB.$web_image,
                    S3_BUCKET.'/'.S3_PROMOTIONAL_DIR_TMP.$web_image,
                    'public-read'
                );
                // Delete web image from s3 temp directory.
                AwsService::deleteObjectInS3Bucket(
                    S3_BUCKET,
                    S3_PROMOTIONAL_DIR_TMP.$web_image
                );
            } else {
                    return ApiResponse::notFoundError(EC_NOT_FOUND, 'File you are passing not found on s3 server');
            }//end if
        }//end if

        if (isset($input_params['status']) === true) {
            $banner['status'] = $input_params['status'];
        }

        // Update Banners In promo_banners table.
        $response = OfferService::updatePromoBanner($banner);

        if ($response === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'Bannner not Updated.');
        }

        return ApiResponse::successMessage('Banner successfully Updated.');

    }//end putPromoBanner()


    /**
     * Delete Promo Banner
     *
     * @param \App\Http\DeletePromoBannerRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Delete(
     *     path="/v1.6/admin/promobanner",
     *     tags={"Promobanners"},
     *     description="Returns success message when Banner Deleted successfully.",
     *     operationId="promobanners.admin.delete.promobanner",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/id_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Banner successfully Deleted"
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters."
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="Banner not Deleted ",
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Banner Id not Exist. ",
     *     )
     * )
     */
    public function deletePromoBanner(DeletePromoBannerRequest $request)
    {
        $input_params = $request->input();

        $banner_id      = $input_params['id'];
        $check_exist_id = OfferService::getPromoBanner($banner_id);
        if (empty($check_exist_id) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Banner Id not Exist');
        }

        $response_banner = OfferService::deletePromoBanner($banner_id);
        if ($response_banner === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'Banner not Deleted.');
        }

        return ApiResponse::successMessage('Banner successfully Deleted .');

    }//end deletePromoBanner()


    /**
     * Get Banners listing
     *
     * @param \Illuminate\Http\Request $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/admin/promobanner",
     *     tags={"Promobanners"},
     *     description="get listing of promo banners.",
     *     operationId="promobanners.get.admin.promobanner",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array containing Promo Banners."
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     *     )
     * )
     */
    public function getPromoBanner(Request $request)
    {
        // Get Banner data from services.
        $banners = OfferService::getPromoBanners();

        if (empty($banners) === false) {
            return ApiResponse::success($banners);
        } else {
            return ApiResponse::errorMessage('Promo Banner Not Exist.');
        }

    }//end getPromoBanner()


    /**
     * CheckImageStructure
     *
     * @param array $offer_image Array Of offer image.
     *
     * @return boolean True/false
     */
    private function checkImageStructure(array $offer_image)
    {
        if (array_key_exists('mobile_image', $offer_image) === true && array_key_exists('web_image', $offer_image) === true && array_key_exists('default', $offer_image) === true && array_key_exists('sort', $offer_image) === true) {
            return true;
        }

        return false;

    }//end checkImageStructure()


    /**
     * PutObjectInS3Bucket
     *
     * @param string $image_path_new NEW IMAGE PATH.
     * @param string $image_path_old OLD IMAGE PATH.
     *
     * @return boolean True/false
     */
    private function putObjectInS3Bucket(string $image_path_new, string $image_path_old)
    {
        try {
            AwsService::putObjectInS3Bucket(
                S3_BUCKET,
                $image_path_new,
                $image_path_old,
                'public-read'
            );
        } catch (\ErrorException $e) {
            Helper::logError($e->getMessage());
            return false;
        }//end try

        return true;

    }//end putObjectInS3Bucket()


}//end class
