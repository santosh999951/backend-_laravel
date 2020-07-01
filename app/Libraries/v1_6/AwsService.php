<?php
/**
 * Aws Service containing methods related to sending sending sms and call via twilio
 */

namespace App\Libraries\v1_6;

use App;
use Log;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;
use Aws\Polly\PollyClient;
use Aws\Sqs\SqsClient;
use Aws\S3\S3Client;
use \Carbon\Carbon;
use App\Libraries\Helper;

/**
 * Class AwsService
 */
class AwsService
{

    /**
     * Variable definition.
     *
     * @var $_s3
     */
    // phpcs:ignore
    private static $_s3 = null;

    /**
     * Variable definition.
     *
     * @var $_s3_region
     */
    // phpcs:ignore
    private static $_s3_region = null;

    /**
     * Variable definition.
     *
     * @var $_sqs
     */
    // phpcs:ignore
    private static $_sqs = null;

    /**
     * Variable definition.
     *
     * @var $_sqs_region
     */
    // phpcs:ignore
    private static $_sqs_region = null;

    /**
     * Variable definition.
     *
     * @var $_polly
     */
    // phpcs:ignore
    private static $_polly = null;

    /**
     * Variable definition.
     *
     * @var $_polly_region
     */
    // phpcs:ignore
    private static $_polly_region = null;


    /**
     * Make a s3 connection
     *
     * @param string $region AWS region.
     *
     * @return object |null return s3 client object
     */
    public static function getS3Client(string $region)
    {
        if (empty(self::$_s3) === true || self::$_s3_region !== $region) {
            try {
                self::$_s3        = App::make('aws')->createClient('s3', ['region' => $region]);
                self::$_s3_region = $region;
            } catch (AwsException $e) {
                 Helper::logError('<<<<---- Error creating S3 Client ---->>>>', ['error' => $e->getMessage()]);
                return null;
            }
        }

        return self::$_s3;

    }//end getS3Client()


    /**
     * Make a sqs connection
     *
     * @param string $region AWS region.
     *
     * @return object | null return sqs client object
     */
    public static function getSqsClient(string $region)
    {
        if (empty(self::$_sqs) === true || self::$_sqs_region !== $region) {
            try {
                self::$_sqs        = App::make('aws')->createClient('sqs', ['region' => $region]);
                self::$_sqs_region = $region;
            } catch (AwsException $e) {
                 Helper::logError('<<<<---- Error creating Sqs Client ---->>>>', ['error' => $e->getMessage()]);
                return null;
            }
        }

        return self::$_sqs;

    }//end getSqsClient()


    /**
     * Make a polly connection
     *
     * @param string $region AWS region.
     *
     * @return object return polly client object
     */
    public static function getPollyClient(string $region)
    {
        if (empty(self::$_polly) === true || self::$_polly_region !== $region) {
            try {
                self::$_polly        = App::make('aws')->createClient('polly', ['region' => $region]);
                self::$_polly_region = $region;
            } catch (AwsException $e) {
                 Helper::logError('<<<<---- Error creating Polly Client ---->>>>', ['error' => $e->getMessage()]);
                return (object) [];
            }
        }

        return self::$_polly;

    }//end getPollyClient()


    /**
     * Get aws credentials
     *
     * @return object return aws credentials object
     */
    // phpcs:ignore
    private static function _getCredentials()
    {
        $credentials = new Credentials(env('AWS_ACCESS_KEY_ID'), env('AWS_SECRET_ACCESS_KEY'));
        return $credentials;

    }//end _getCredentials()


    /**
     * Put object in s3 bucket
     *
     * @param string $bucket      Bucket name.
     * @param string $key         File indetifier key (filename).
     * @param string $source_file Source file full name (with path).
     * @param string $acl         Public access.
     * @param string $region      Aws bucket region.
     * @param string $cache       Image cache time.
     *
     * @return null
     */
    public static function putObjectInS3Bucket(string $bucket, string $key, string $source_file, string $acl, string $region=DEFAULT_S3_REGION, string $cache=S3_IMAGES_CACHE_TIME)
    {
        $s3 = self::getS3Client($region);

        $s3->putObject(
            [
                'Bucket'       => $bucket,
                'Key'          => $key,
                'SourceFile'   => $source_file,
                'ACL'          => $acl,
                'CacheControl' => 'max-age='.$cache,
            ]
        );

        return null;

    }//end putObjectInS3Bucket()


    /**
     * Copy object in s3 bucket
     *
     * @param string $dest_bucket Destination Bucket name.
     * @param string $dest_key    Destination File indetifier key (filename).
     * @param string $source_file Source file full name (with path and bucket).
     * @param string $acl         Public access.
     * @param string $region      Aws bucket region.
     * @param string $cache       Image cache time.
     *
     * @return boolean
     *
     * @example supppose we want to move image from promotional_images/offer_landing_page_image_temp/ to  promotional_images/offer_landing_page_images/
     dest_bucket will be guesthoser, $dest_key will be  promotional_images/offer_landing_page_images/ and $source_file will be guesthouser/promotional_images/offer_landing_page_image_orignal/
     */
    public static function copyObjectFromS3ToS3Bucket(string $dest_bucket, string $dest_key, string $source_file, string $acl, string $region=DEFAULT_S3_REGION, string $cache=S3_IMAGES_CACHE_TIME)
    {
        try {
            $s3 = self::getS3Client($region);
            $s3->copyObject(
                [
                    'Bucket'       => $dest_bucket,
                    'Key'          => $dest_key,
                    'CopySource'   => $source_file,
                    'ACL'          => 'public-read',
                    'CacheControl' => $cache,
                ]
            );
        } catch (AwsException $e) {
                Helper::logError('<<<<---- Error creating Sqs Client ---->>>>', ['error' => $e->getMessage()]);
                return false;
        }

        return true;

    }//end copyObjectFromS3ToS3Bucket()


    /**
     * Delete object in s3 bucket
     *
     * @param string $bucket Bucket name.
     * @param string $key    File indetifier key (filename).
     * @param string $region Aws bucket region.
     *
     * @return boolean
     */
    public static function deleteObjectInS3Bucket(string $bucket, string $key, string $region=DEFAULT_S3_REGION)
    {
        try {
            $s3 = self::getS3Client($region);
            $s3->deleteObject(
                [
                    'Bucket' => $bucket,
                    'Key'    => $key,
                ]
            );
        } catch (AwsException $e) {
            Helper::logError('<<<<---- Error creating Sqs Client ---->>>>', ['error' => $e->getMessage()]);
            return false;
        }

        return true;

    }//end deleteObjectInS3Bucket()


    /**
     * Get object in s3 bucket
     *
     * @param string $bucket Bucket name.
     * @param string $key    File indetifier key (filename).
     * @param string $region Aws bucket region.
     *
     * @return boolean
     */
    public static function doesObjectExist(string $bucket, string $key, string $region=DEFAULT_S3_REGION)
    {
        try {
            $s3     = self::getS3Client($region);
            $result = $s3->doesObjectExist($bucket, $key);
            return $result;
        } catch (AwsException $e) {
            Helper::logError('<<<<---- Error creating Sqs Client ---->>>>', ['error' => $e->getMessage()]);
            return false;
        }

    }//end doesObjectExist()


    /**
     * Generate audio file from text array using polly and sore that audio in s3 bucket
     *
     * @param array  $speech      Speech array to convert in audio file.
     * @param string $bucket_name Bucket name.
     * @param string $region      Aws region.
     *
     * @return array containing status and file_name
     */
    public static function generatePollyAudioAndSaveInS3Bucket(array $speech, string $bucket_name=S3_POLLY_OTP_AUDIO_DIR, string $region=S3_OTP_AUDIO_REGION)
    {
        $generate_audio_file = self::generateAudioFileFromSSMLSpeechArray($speech);

        if ($generate_audio_file['status'] === 0) {
            return $generate_audio_file;
        }

        self::putObjectInS3Bucket(S3_BUCKET, $bucket_name.'/'.$generate_audio_file['message'], POLLY_OTP_AUDIO_DIR.$generate_audio_file['message'], 'public-read', DEFAULT_S3_REGION);
        // Delete local file.
        unlink(POLLY_OTP_AUDIO_DIR.$generate_audio_file['message']);
        $generate_audio_file['message'] = CDN_URL.$bucket_name.'/'.$generate_audio_file['message'];

        return $generate_audio_file;

    }//end generatePollyAudioAndSaveInS3Bucket()


    /**
     * Generate audio file from ssml code using polly
     *
     * @param array  $speech Speech containing ssml code.
     * @param string $region Aws region.
     *
     * @return array $result Array containing message/file name and status.
     */
    public static function generateAudioFileFromSSMLSpeechArray(array $speech, string $region=DEFAULT_POLLY_REGION)
    {
        // Get service handle.
        $polly = self::getPollyClient($region);

        try {
            // Generate speech audio.
            $response = $polly->synthesizeSpeech($speech);

            $file_name = Carbon::now()->timestamp.'.mp3';
            // Save response file.
            \Storage::disk('sms_call')->put($file_name, $response['AudioStream']);

            $result['message'] = $file_name;
            $result['status']  = 1;
        } catch (AwsException $e) {
            $result['message'] = $e->getMessage();
            $result['status']  = 0;
        }

        return $result;

    }//end generateAudioFileFromSSMLSpeechArray()


    /**
     * PutObjectInS3Bucket
     *
     * @param string $image_path_new NEW IMAGE PATH.
     * @param string $image_path_old OLD IMAGE PATH.
     *
     * @return boolean True/false
     */
    public static function putImageInS3Bucket(string $image_path_new, string $image_path_old)
    {
        try {
            self::putObjectInS3Bucket(
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

    }//end putImageInS3Bucket()


}//end class
