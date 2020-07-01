<?php
/**
 * CurrencyConversion Model contain all functions related to Currency Conversion
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

// phpcs:disable  
/**
 *
 * // phpcs:enable
 * Class CurrencyConversion
 */
class CurrencyConversion extends Model
{

    /**
     * Table Name
     *
     * @var string
     * // phpcs:disable
     * // phpcs:enable
     */
    protected $table = 'currency_conversion';


    /**
     * Helper function get currency details.
     *
     * @param string $currency_code Currency Code.
     *
     * @return object Currencies.
     */
    public static function getCurrencyDetails(string $currency_code=DEFAULT_CURRENCY)
    {
        return self::where('currency_code', $currency_code)->first();

    }//end getCurrencyDetails()


    /**
     * Helper function get all currency conversion details.
     *
     * @return object currency conversion Details.
     */
    public static function getAllCurrencyDetails()
    {
        if (defined('ALL_CURRENCY_CONVERSION') === false) {
            $currency_conversions            = self::all();
            $associative_currency_conversion = [];

            // Currency conversions.
            foreach ($currency_conversions as $one_currency_conversion) {
                $associative_currency_conversion[$one_currency_conversion->currency_code] = [
                    'currency_code' => $one_currency_conversion['currency_code'],
                    'exchange_rate' => $one_currency_conversion['exchange_rate'],
                ];
            }

            define('ALL_CURRENCY_CONVERSION', $associative_currency_conversion);
        }

        return ALL_CURRENCY_CONVERSION;

    }//end getAllCurrencyDetails()


}//end class
