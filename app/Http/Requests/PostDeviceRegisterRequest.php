<?php
/**
 * Device Register Request model containing Request Model Validation related Method.
 */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory;

/**
 * Class PostDeviceRegisterRequest
 */
class PostDeviceRegisterRequest extends BaseFormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ['device_unique_id' => 'required|string|max:255'];

    }//end rules()


    /**
     *  Filters to be applied to the input.
     *
     * @return array
     */
    public function filters()
    {
        // Set empty string when parameter not provided.
        return [
            'device_notification_token' => 'trim|default:empty',
            'app_version'               => 'trim|default:empty',
            'device_model'              => 'trim|default:empty',
            'device_make'               => 'trim|default:empty',
            'brand'                     => 'trim|default:empty',
            'os_version'                => 'trim|default:empty',
            'resolution'                => 'trim|default:empty',
            'country'                   => 'trim|default:empty',
            'screen_width'              => 'trim|default:empty',
            'screen_height'             => 'trim|default:empty',
            'ram'                       => 'trim|default:empty',
            'dpi'                       => 'trim|default:empty',
            'app_version_code'          => 'trim|default:empty',
            'fcm_token'                 => 'trim|default:empty',
        ];

    }//end filters()


}//end class
