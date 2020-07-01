<?php
/**
 * ApiResponse containing methods related to Response model
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class ApiResponse
 */
class ApiResponse
{


    /**
     * Construct of ApiResponse
     *
     * @param array $params Parameters.
     *
     * @return $this
     */
    public function __construct(array $params)
    {
        foreach ($params as $key => $val) {
            $function = 'set'.ucwords(str_replace('_', '', $key));
            if (method_exists($this, $function) === true) {
                $this->$function($val);
            }
        }

    }//end __construct()


    /**
     * Convert Response Model Object to Array
     *
     * @return array Api Response in Array
     */
    public function toArray()
    {
         $vars = get_object_vars($this);
        array_walk(
            $vars,
            function (&$method, $key) {
                    $method = $this->{'get'.ucwords(str_replace('_', '', $key))}();
            }
        );
         return $vars;

    }//end toArray()


}//end class
