<?php

namespace Zeroem\CurlBundle\Curl;

final class CurlOptions
{
    static private $option_value_types = array(
        CURLOPT_AUTOREFERER => "bool",
        CURLOPT_BINARYTRANSFER => "bool",
        CURLOPT_COOKIESESSION => "bool",
        CURLOPT_CERTINFO => "bool",
        CURLOPT_CRLF => "bool",
        CURLOPT_DNS_USE_GLOBAL_CACHE => "bool",
        CURLOPT_FAILONERROR => "bool",
        CURLOPT_FILETIME => "bool",
        CURLOPT_FOLLOWLOCATION => "bool",
        CURLOPT_FORBID_REUSE => "bool",
        CURLOPT_FRESH_CONNECT => "bool",
        CURLOPT_FTP_USE_EPRT => "bool",
        CURLOPT_FTP_USE_EPSV => "bool",
        CURLOPT_FTP_CREATE_MISSING_DIRS => "bool",
        CURLOPT_FTPAPPEND => "bool",

        // PHP 5.3.6 doesn't have this constant
        //CURLOPT_FTPASCII => "bool",
        CURLOPT_FTPLISTONLY => "bool",
        CURLOPT_HEADER => "bool",
        CURLINFO_HEADER_OUT = "bool",
        CURLOPT_HTTPGET => "bool",
        CURLOPT_HTTPPROXYTUNNEL => "bool",

        // PHP 5.3.6 doesn't have this constant
        //CURLOPT_MUTE => "bool",
        CURLOPT_NETRC => "bool",
        CURLOPT_NOBODY => "bool",
        CURLOPT_NOPROGRESS => "bool",
        CURLOPT_NOSIGNAL => "bool",
        CURLOPT_POST => "bool",
        CURLOPT_PUT => "bool",
        CURLOPT_RETURNTRANSFER => "bool",
        CURLOPT_SSL_VERIFYPEER => "bool",
        CURLOPT_TRANSFERTEXT => "bool",
        CURLOPT_UNRESTRICTED_AUTH => "bool",
        CURLOPT_UPLOAD => "bool",
        CURLOPT_VERBOSE => "bool",
        CURLOPT_BUFFERSIZE => "int",
        CURLOPT_CLOSEPOLICY => "int",
        CURLOPT_CONNECTTIMEOUT => "int",
        CURLOPT_CONNECTTIMEOUT_MS => "int",
        CURLOPT_DNS_CACHE_TIMEOUT => "int",
        CURLOPT_FTPSSLAUTH => "int",
        CURLOPT_HTTP_VERSION => "int",
        CURLOPT_HTTPAUTH => "int",
        CURLOPT_INFILESIZE => "int",
        CURLOPT_LOW_SPEED_LIMIT => "int",
        CURLOPT_LOW_SPEED_TIME => "int",
        CURLOPT_MAXCONNECTS => "int",
        CURLOPT_MAXREDIRS => "int",
        CURLOPT_PORT => "int",
        CURLOPT_PROTOCOLS => "int",
        CURLOPT_PROXYAUTH => "int",
        CURLOPT_PROXYPORT => "int",
        CURLOPT_PROXYTYPE => "int",
        CURLOPT_REDIR_PROTOCOLS => "int",
        CURLOPT_RESUME_FROM => "int",
        CURLOPT_SSL_VERIFYHOST => "int",
        CURLOPT_SSLVERSION => "int",
        CURLOPT_TIMECONDITION => "int",
        CURLOPT_TIMEOUT => "int",
        CURLOPT_TIMEOUT_MS => "int",
        CURLOPT_TIMEVALUE => "int",

        // PHP 5.3.6 doesn't have this constant
        //CURLOPT_MAX_RECV_SPEED_LARGE => "int",
        //CURLOPT_MAX_SEND_SPEED_LARGE => "int",
        CURLOPT_SSH_AUTH_TYPES => "int",
        CURLOPT_CAINFO => "string",
        CURLOPT_CAPATH => "string",
        CURLOPT_COOKIE => "string",
        CURLOPT_COOKIEFILE => "string",
        CURLOPT_COOKIEJAR => "string",
        CURLOPT_CUSTOMREQUEST => "string",
        CURLOPT_EGDSOCKET => "string",
        CURLOPT_ENCODING => "string",
        CURLOPT_FTPPORT => "string",
        CURLOPT_INTERFACE => "string",
        CURLOPT_KEYPASSWD => "string",
        CURLOPT_KRB4LEVEL => "string",
        CURLOPT_POSTFIELDS => array("string","array"),
        CURLOPT_PROXY => "string",
        CURLOPT_PROXYUSERPWD => "string",
        CURLOPT_RANDOM_FILE => "string",
        CURLOPT_RANGE => "string",
        CURLOPT_REFERER => "string",
        CURLOPT_SSH_HOST_PUBLIC_KEY_MD5 => "string",
        CURLOPT_SSH_PUBLIC_KEYFILE => "string",
        CURLOPT_SSH_PRIVATE_KEYFILE => "string",
        CURLOPT_SSL_CIPHER_LIST => "string",
        CURLOPT_SSLCERT => "string",
        CURLOPT_SSLCERTPASSWD => "string",
        CURLOPT_SSLCERTTYPE => "string",
        CURLOPT_SSLENGINE => "string",
        CURLOPT_SSLENGINE_DEFAULT => "string",
        CURLOPT_SSLKEY => "string",
        CURLOPT_SSLKEYPASSWD => "string",
        CURLOPT_SSLKEYTYPE => "string",
        CURLOPT_URL => "string",
        CURLOPT_USERAGENT => "string",
        CURLOPT_USERPWD => "string",
        CURLOPT_HTTP200ALIASES => "array",
        CURLOPT_HTTPHEADER => "array",
        CURLOPT_POSTQUOTE => "array",
        CURLOPT_QUOTE => "array",
        CURLOPT_FILE => "resource",
        CURLOPT_INFILE => "resource",
        CURLOPT_STDERR => "resource",
        CURLOPT_WRITEHEADER => "resource",
        CURLOPT_HEADERFUNCTION => "callable",

        // PHP 5.3.6 doesn't have this constant
        //CURLOPT_PASSWDFUNCTION => "callable",
        CURLOPT_PROGRESSFUNCTION => "callable",
        CURLOPT_READFUNCTION => "callable",
        CURLOPT_WRITEFUNCTION => "callable",
    );

    /**
     * Determine whether or not the value passed is a valid cURL option
     *
     * @param int $option An Integer Flag
     * @return boolean Whether or not the flag is a valid cURL option
     */
    static public function isValidOption($option) {
        return isset(static::$option_value_types[$option]);
    }

    /**
     * Check whether or not the value is a valid type for the given option
     *
     * @param int $option An integer flag
     * @param mixed $value the value to be set to the integer flag
     * @return boolean Whether or not the value is of the correct type
     *
     * @throws \InvalidArgumentException if the $option _is_not_ a valid cURL option
     */
    static public function checkOptionValue($option, $value, $throw = true) {
        if(static::isValidOption($option)) {
            $result = static::checkType($value, static::$option_value_types[$option]);

            if(!$result && $throw) {
                throw new \InvalidArgumentException("Invalid value for the given cURL option");
            }

            return $result;
        } else {
            throw new \InvalidArgumentException("Not a valid cURL option");
        }
    }

    static private function checkType($value, $type) {

        $result = false;

        if(is_array($type)) {
            foreach($type as $item) {
                $result |= $this->checkType($value, $item);
            }

        } else {
            $func = "is_{$type}";

            if(is_callable($func)) {
                $result = $func($value);
            } else {
                throw new \InvalidArgumentException("The type `{$type}' is not a valid type to check");
            }
        }

        return $result;
    }

}