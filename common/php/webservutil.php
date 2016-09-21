<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/common/vendor/php/RestClient.class.php");

//exec api
function getAPI($rel_url, $qparams, $cook = null)
{
    global $SERVER_URL;
    $url = $SERVER_URL . $rel_url;
    //get resp
    $gotxmlstr = RestClient::get($url, $qparams, $cook);
    //check resp code
    $resp_code = $gotxmlstr->getResponseCode();
    //if good
    if ($resp_code == 200 || $resp_code == 400 || $resp_code == 500) {
        $jsonResp = trim($gotxmlstr->getResponse());
        $parsedJson = json_decode($jsonResp);
        $parsedJsonStr = json_format($jsonResp);
    }

    return array($resp_code, $parsedJson, $parsedJsonStr);
}

function postAPI($rel_url, $body, $type = null, $cook = null)
{
    $url = $rel_url;
    //get resp
    $resp = RestClient::post($url, $body, $cook, null, null, $type);
    //check resp code
    $resp_code = $resp->getResponseCode();
    $resp_body = trim($resp->getResponse());
    //if good
    if ($resp_code == 200 || $resp_code == 400 || $resp_code == 500) {
        $cookies = $resp->getCookies();
    }

    return array($resp_code, $resp_body, $cookies);
}

function putAPI($rel_url, $body, $cook = null)
{
    global $SERVER_URL;
    $url = $SERVER_URL . $rel_url;
    //get resp
    $resp = RestClient::put($url, $body, $cook);
    //check resp code
    $resp_code = $resp->getResponseCode();
    //if good
    if ($resp_code == 200 || $resp_code == 400 || $resp_code == 500) {
        $jsonResp = trim($resp->getResponse());
        $parsedJson = json_decode($jsonResp);
        $parsedJsonStr = json_format($jsonResp);
        $cookies = $resp->getCookies();
    }

    return array($resp_code, $parsedJson, $parsedJsonStr, $cookies);
}

function deleteAPI($rel_url, $body, $cook = null)
{
    global $SERVER_URL;
    $url = $SERVER_URL . $rel_url;
    //get resp
    $resp = RestClient::delete($url, $body, $cook);
    //check resp code
    $resp_code = $resp->getResponseCode();
    //if good
    if ($resp_code == 200 || $resp_code == 400 || $resp_code == 500) {
        $jsonResp = trim($resp->getResponse());
        $parsedJson = json_decode($jsonResp);
        $parsedJsonStr = json_format($jsonResp);
        $cookies = $resp->getCookies();
    }

    return array($resp_code, $parsedJson, $parsedJsonStr, $cookies);
}

//exec api
function execApi($url, $qparams)
{
    //get resp
    $gotxmlstr = RestClient::get($url, $qparams);
    //check resp code
    $resp_code = $gotxmlstr->getResponseCode();
    //if good
    if ($resp_code == 200 || $resp_code == 400 || $resp_code == 500) {
        $jsonResp = trim($gotxmlstr->getResponse());
        $parsedJson = json_decode($jsonResp);
        $parsedJsonStr = json_format($jsonResp);
    }

    return array($resp_code, $parsedJson, $parsedJsonStr);
}

function indent($json)
{
    $result = '';
    $pos = 0;
    $strLen = strlen($json);
    $indentStr = '  ';
    $newLine = "\n";
    $prevChar = '';
    $outOfQuotes = true;

    for ($i = 0; $i <= $strLen; $i++) {

        // Grab the next character in the string.
        $char = substr($json, $i, 1);

        // Are we inside a quoted string?
        if ($char == '"' && $prevChar != '\\') {
            $outOfQuotes = !$outOfQuotes;

            // If this character is the end of an element,
            // output a new line and indent the next line.
        } else if (($char == '}' || $char == ']') && $outOfQuotes) {
            $result .= $newLine;
            $pos--;
            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }

        // Add the character to the result string.
        $result .= $char;

        // If the last character was the beginning of an element,
        // output a new line and indent the next line.
        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
            $result .= $newLine;
            if ($char == '{' || $char == '[') {
                $pos++;
            }

            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }

        $prevChar = $char;
    }

    return $result;
}

function json_format($input)
{
    $tab = 0;
    $out = "";
    $tabs = "";
    for ($i = 0; $i < strlen($input); $i++) {
        $c = $input[$i];
        $tabs = "";
        for ($t = 1; $t <= $tab; $t++) $tabs .= "\t";
        switch ($c) {
            case "{":
                $tab++;
                $tabs .= "\t";
                $out .= "{\n" . $tabs;
                break;
            case "}":
                $tab--;
                $tabs[strlen($tabs) - 1] = "";
                $out .= "\n" . $tabs . "}";
                break;
            case "[":
                $tab++;
                $tabs .= "\t";
                $out .= "[\n" . $tabs;
                break;
            case "]":
                $tab--;
                $tabs[strlen($tabs) - 1] = "";
                $out .= "\n" . $tabs . "]";
                break;
            case ",":
                $out .= ",\n" . $tabs;
                break;
            default:
                $out .= $c;
                break;
        }
    }
    $out = str_replace("\x00", "", $out);
    return $out;
}
