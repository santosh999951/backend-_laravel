<?php
/**
 * Country Code Mapping contain all functions country names and codes
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CountryCodeMapping
 */
class CountryCodeMapping extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'ccode_currency_mapping';


    /**
     * Helper function get countries name and code
     *
     * @return array Countries Array.
     */
    public static function getCountries()
    {
        // Get all countries.
        $countries = self::select('name', 'ccode')->get()->toArray();

        // Countries associative array.
        $countries_array = [];

        // Make associative array.
        foreach ($countries as $country) {
            $countries_array[$country['ccode']] = $country;
        }

        return $countries_array;

    }//end getCountries()


    /**
     * Helper function to get dial codes
     *
     * @return array Dial codes Array.
     */
    public static function getDialCodes()
    {
        $path = CDN_URL.'country-flags/1x/';
        // Get all countries with their dial codes in an associative fashion.
        $data = static::select(\DB::raw("CONCAT(CONCAT('$path', name), '.png') as path"), 'dial_code', 'name as country_name')->where('dial_code', '<>', '')->orderByRaw('CAST(dial_code AS unsigned)')->get()->toArray();

        return $data;

    }//end getDialCodes()


    /**
     * Helper function to get dial codes
     *
     * @return array Country Codes Array.
     */
    public static function getCountryCodes()
    {
        // Get all countries with their country codes in an associative fashion.
        $data = static::all('ccode as country_code', 'name as country_name')->toArray();

        return $data;

    }//end getCountryCodes()


    /**
     * Helper function to get Country Name
     *
     * @param string $country_code Country Code.
     *
     * @return string Country Name.
     */
    public static function getCountryName(string $country_code)
    {
        // Get all countries.
        $country_name = self::select('name')->where('ccode', $country_code)->first();

        $c_name = '';

        if (empty($country_name) === false) {
            $c_name = $country_name->name;
        }

        return ucfirst($c_name);

    }//end getCountryName()


    /**
     * Helper function to get country details.
     *
     * @return array Country Details Array.
     */
    public static function getCountryDetails()
    {
        $path = CDN_URL.'country-flags/1x/';
        // Get all countries with their dial codes in an associative fashion.
        $data = static::select('name', 'ccode', 'dial_code', \DB::raw("CONCAT(CONCAT('$path', name), '.png') as image"))->where('dial_code', '<>', '')->orderByRaw('CAST(dial_code AS unsigned)')->get()->toArray();

        return $data;

    }//end getCountryDetails()


}//end class
