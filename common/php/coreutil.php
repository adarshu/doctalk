<?php

function getMilliTime()
{
    return round(microtime(true) * 1000);
}

function mapFuncOnArgs($functocall)
{
    $numargs = func_num_args();
    if ($numargs == 2) {
        $origargs = func_get_arg(1);
        $numorigargs = count($origargs);
        if ($numorigargs == 1) {
            $first = $origargs[0];
            if (is_array($first) || is_object($first)) {
                $ret = array();
                foreach ($first as $key => $val) {
                    $ret[$key] = call_user_func($functocall, $val);
                }
                if (is_object($first)) {
                    return (object)$ret;
                } else if (is_array($first)) {
                    return $ret;
                }
            } else {
                return call_user_func($functocall, $origargs[0]);
            }
        } else if ($numorigargs > 1) {
            return array_map($functocall, $origargs);
        }
    }
    return false;
}

function printargs()
{
    $ret = "";
    $numargs = func_num_args();
    for ($i = 0; $i < $numargs; $i++) {
        $arg = func_get_arg($i);
        $ret .= $arg . "<br>";
    }
    return $ret;
}

function echoBr($s)
{
    echo $s . "<br>";
}

function getRandomStringSimple($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function getRandomString($length = 16)
{
    $bytes = openssl_random_pseudo_bytes($length, $cstrong);
    $hex = bin2hex($bytes);
    return $hex;
}

//TODO later
function getRandomInt($length = 16)
{
    $bytes = openssl_random_pseudo_bytes($length, $cstrong);
    $dec = strval(bindec($bytes));
    return substr($dec, 0, $length);
}

function getUUID()
{
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),

        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

function strleft($s1, $s2)
{
    return substr($s1, 0, strpos($s1, $s2));
}

function strcontains($haystack, $needle)
{
    return strpos($haystack, $needle);
}

// Converts $title to Title Case, and returns the result.
function strtotitle($title)
{
    // Our array of 'small words' which shouldn't be capitalised if
    // they aren't the first word. Add your own words to taste.
    $smallwordsarray = array('of', 'a', 'the', 'and', 'an', 'or', 'nor', 'but', 'is', 'if', 'then', 'else', 'when', 'at', 'from', 'by', 'on', 'off', 'for', 'in', 'out', 'over', 'to', 'into', 'with');
    // Split the string into separate words
    $words = explode(' ', $title);
    foreach ($words as $key => $word) {
        // If this word is the first, or it's not one of our small words, capitalise it
        // with ucwords().
        if ($key == 0 or !in_array($word, $smallwordsarray))
            $words[$key] = ucwords($word);
    }
    // Join the words back into a string
    $newtitle = implode(' ', $words);
    return $newtitle;
}


function truncateString($str, $len)
{
    $slen = strlen($str);
    if ($slen <= $len)
        return $str;
    else
        return substr($str, 0, $len) . "...";
}

function ellipsis($text, $max = 100, $append = '&hellip;')
{
    if (strlen($text) <= $max) return $text;
    $out = substr($text, 0, $max);
    if (strpos($text, ' ') === FALSE) return $out . $append;
    return preg_replace('/\w+$/', '', $out) . $append;
}

function startsWith($haystack, $needle)
{
    return !strncmp($haystack, $needle, strlen($needle));
}

function endsWith($haystack, $needle)
{
    return strrpos($haystack, $needle) === strlen($haystack) - strlen($needle);
}


// trims off x chars from the front of a string
// or the matching string in $off is trimmed off
function trimOffFront($off, $str)
{
    if (is_numeric($off))
        return substr($str, $off);
    else
        return substr($str, strlen($off));
}

// trims off x chars from the end of a string
// or the matching string in $off is trimmed off
function trimOffEnd($off, $str)
{
    if (is_numeric($off))
        return substr($str, 0, strlen($str) - $off);
    else
        return substr($str, 0, strlen($str) - strlen($off));
}

function my_encrypt($plaintext)
{
    $server_encrypt_key = "Monkey Boy is so cool w00t!";
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

    $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $server_encrypt_key, $plaintext, MCRYPT_MODE_ECB, $iv);
    return $crypttext;
}

function my_decrypt($cyphertext)
{
    $server_encrypt_key = "Monkey Boy is so cool w00t!";
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

    $plaintext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $server_encrypt_key, $cyphertext, MCRYPT_MODE_ECB, $iv);
    $plaintext = rtrim($plaintext, "\0");
    return $plaintext;
}

function my_encrypt64($plaintext)
{
    return bin2hex(my_encrypt($plaintext));
}

function my_decrypt64($cyphertext)
{
    return my_decrypt(hex2bin_mine($cyphertext));
}

function hex2bin_mine2($hex_string)
{
    $binary_string = pack("H*", $hex_string);
    return $binary_string;
}

function hex2bin_mine($hexstr)
{
    $n = strlen($hexstr);
    $sbin = "";
    $i = 0;
    while ($i < $n) {
        $a = substr($hexstr, $i, 2);
        $c = pack("H*", $a);
        if ($i == 0) {
            $sbin = $c;
        } else {
            $sbin .= $c;
        }
        $i += 2;
    }
    return $sbin;
}

function base64_url_encode($input)
{
    return strtr(base64_encode($input), '+/=', '-_,');
}

function base64_url_decode($input)
{
    return base64_decode(strtr($input, '-_,', '+/='));
}

function base64_url_encode2($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64_url_decode2($data)
{
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

function base36_encode($base10)
{
    return base_convert($base10, 10, 36);
}

function base36_decode($base36)
{
    return base_convert($base36, 36, 10);
}


function base62_encode($hex)
{
    $chars = 'abcdefghijklmnopqrstuwvxyzABCDEFGHIJKLMNOPQRSTUWVXYZ0123456789';
    $in = bchexdec($hex);

    $result = '';

    while ($in > 0) {
        $i = bcmod($in, 62);
        $in = bcdiv($in, 62);

        $result = $chars[$i] . $result;
    }

    return $result;
}

function bchexdec($hex)
{
    $dec = 0;
    $len = strlen($hex);
    for ($i = 1; $i <= $len; $i++)
        $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));

    return $dec;
}

function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $email);;
}


function htmlsafehelper($value)
{
    return htmlspecialchars($value, ENT_QUOTES);
}

function htmlsafe()
{
    return mapFuncOnArgs("htmlsafehelper", func_get_args());
}

function getMimeType($filename)
{
    preg_match("|\.([a-z0-9]{2,4})$|i", $filename, $fileSuffix);

    switch (strtolower($fileSuffix[1])) {
        case "js" :
            return "application/x-javascript";

        case "json" :
            return "application/json";

        case "jpg" :
        case "jpeg" :
        case "jpe" :
            return "image/jpg";

        case "png" :
        case "gif" :
        case "bmp" :
        case "tiff" :
            return "image/" . strtolower($fileSuffix[1]);

        case "css" :
            return "text/css";

        case "xml" :
            return "application/xml";

        case "doc" :
        case "docx" :
            return "application/msword";

        case "xls" :
        case "xlt" :
        case "xlm" :
        case "xld" :
        case "xla" :
        case "xlc" :
        case "xlw" :
        case "xll" :
            return "application/vnd.ms-excel";

        case "ppt" :
        case "pps" :
            return "application/vnd.ms-powerpoint";

        case "rtf" :
            return "application/rtf";

        case "pdf" :
            return "application/pdf";

        case "html" :
        case "htm" :
        case "php" :
            return "text/html";

        case "txt" :
            return "text/plain";

        case "mpeg" :
        case "mpg" :
        case "mpe" :
            return "video/mpeg";

        case "mp3" :
            return "audio/mpeg3";

        case "mp4" :
            return "video/mp4";

        case "wav" :
            return "audio/wav";

        case "aiff" :
        case "aif" :
            return "audio/aiff";

        case "avi" :
            return "video/x-msvideo";
        case "divx" :
            return "video/divx";
        case "mkv" :
            return "video/x-matroska";

        case "wmv" :
            return "application/octet-stream";

        case "mov" :
            return "video/quicktime";

        case "zip" :
            return "application/zip";

        case "tar" :
            return "application/x-tar";

        case "swf" :
            return "application/x-shockwave-flash";

        default :
            if (function_exists("mime_content_type")) {
                $fileSuffix = mime_content_type($filename);
            }

            //return "unknown/" . trim($fileSuffix[0], ".");
            return "application/octet-stream";
    }
}

