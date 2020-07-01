<?php
// phpcs:ignoreFile
/**
 * JSON Web Key implementation, based on this spec:
 * https://tools.ietf.org/html/draft-ietf-jose-json-web-key-41
 *
 * PHP version 5
 */

namespace App\Libraries;

use UnexpectedValueException;

/**
 * Class Jwk
 */
class Jwk
{
    /**
     * Parse a set of JWK keys.
     *
     * @param mixed $source JWK Keys.
     *
     * @return array an associative array represents the set of keys
     */
    public static function parseKeySet($source)
    {
        $keys = [];
        if (is_string($source)) {
            $source = json_decode($source, true);
        } else if (is_object($source)) {
            if (property_exists($source, 'keys'))
                $source = (array)$source;
            else
                $source = [$source];
        }

        if (is_array($source)) {
            if (isset($source['keys']))
                $source = $source['keys'];

            foreach ($source as $k => $v) {
                if (!is_string($k)) {
                    if (is_array($v) && isset($v['kid']))
                        $k = $v['kid'];
                    elseif (is_object($v) && property_exists($v, 'kid'))
                        $k = $v->{'kid'};
                }
                try {
                    $v = self::parsePemKey($v);
                    $keys[$k] = $v;
                } catch (UnexpectedValueException $e) {
                    //Do nothing
                }
            }
        }
        if (0 < count($keys)) {
            return $keys;
        }
        throw new UnexpectedValueException('Failed to parse JWK');
    }

    /**
     * Parse a JWK key.
     *
     * @param mixed $source JWK Key.
     *
     * @return string
     */
    public static function parsePemKey($source)
    {
        if (!is_array($source))
            $source = (array)$source;
        if (!empty($source) && isset($source['kty']) && isset($source['n']) && isset($source['e'])) {
            switch ($source['kty']) {
                case 'RSA':
                    if (array_key_exists('d', $source))
                        throw new UnexpectedValueException('Failed to parse JWK: RSA private key is not supported');

                    return self::createPemFromModulusAndExponent($source['n'], $source['e']);
                    break;
                default:
                    //Currently only RSA is supported
                    break;
            }
        }

        throw new UnexpectedValueException('Failed to parse JWK');
    }

    /**
     *
     * Create a public key represented in PEM format from RSA modulus and exponent information
     *
     * @access private
     * @param string $n the RSA modulus encoded in Base64
     * @param string $e the RSA exponent encoded in Base64
     * @return string the RSA public key represented in PEM format
     */
    public static function createPemFromModulusAndExponent($n, $e)
    {
        $modulus = self::urlsafeB64Decode($n);
        $publicExponent = self::urlsafeB64Decode($e);


        $components = array(
            'modulus' => pack('Ca*a*', 2, self::encodeLength(strlen($modulus)), $modulus),
            'publicExponent' => pack('Ca*a*', 2, self::encodeLength(strlen($publicExponent)), $publicExponent)
        );

        $RSAPublicKey = pack(
            'Ca*a*a*',
            48,
            self::encodeLength(strlen($components['modulus']) + strlen($components['publicExponent'])),
            $components['modulus'],
            $components['publicExponent']
        );


        // sequence(oid(1.2.840.113549.1.1.1), null)) = rsaEncryption.
        $rsaOID = pack('H*', '300d06092a864886f70d0101010500'); // hex version of MA0GCSqGSIb3DQEBAQUA
        $RSAPublicKey = chr(0) . $RSAPublicKey;
        $RSAPublicKey = chr(3) . self::encodeLength(strlen($RSAPublicKey)) . $RSAPublicKey;

        $RSAPublicKey = pack(
            'Ca*a*',
            48,
            self::encodeLength(strlen($rsaOID . $RSAPublicKey)),
            $rsaOID . $RSAPublicKey
        );

        $RSAPublicKey = "-----BEGIN PUBLIC KEY-----\r\n" .
            chunk_split(base64_encode($RSAPublicKey), 64) .
            '-----END PUBLIC KEY-----';

        return $RSAPublicKey;
    }

    /**
     * DER-encode the length
     *
     * DER supports lengths up to (2**8)**127, however, we'll only support lengths up to (2**8)**4.  See
     * {@link http://itu.int/ITU-T/studygroups/com17/languages/X.690-0207.pdf#p=13 X.690 paragraph 8.1.3} for more information.
     *
     * @access private
     * @param int $length
     * @return string
     */
    private static function encodeLength($length)
    {
        if ($length <= 0x7F) {
            return chr($length);
        }

        $temp = ltrim(pack('N', $length), chr(0));
        return pack('Ca*', 0x80 | strlen($temp), $temp);
    }

    /**
     * Decode a string with URL-safe Base64.
     *
     * @param string $input A Base64 encoded string
     *
     * @return string A decoded string
     */
    private static function urlsafeB64Decode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

}