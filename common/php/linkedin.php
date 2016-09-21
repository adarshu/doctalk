<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/common/vendor/php/linkedin_3.2.0.class.php");

/**
 * Session existance check.
 */
function oauth_session_exists()
{
    return is_array($_SESSION) && array_key_exists('oauth', $_SESSION);
}

function linkedInInitiate($API_CONFIG, $successRedirPath)
{
    //track
    try {
        // check PHP version
        if (version_compare(PHP_VERSION, '5.0.0', '<')) {
            throw new LinkedInException('You must be running version 5.x or greater of PHP to use this library.');
        }

        // check for cURL
        if (extension_loaded('curl')) {
            $curl_version = curl_version();
            $curl_version = $curl_version['version'];
        } else {
            throw new LinkedInException('You must load the cURL extension to use this library.');
        }

        // start the session
        if ((session_status() == PHP_SESSION_NONE) && !session_start()) {
            throw new LinkedInException('This script requires session support, which appears to be disabled according to session_start().');
        }

        // check for the correct http protocol (i.e. is this script being served via http or https)
        if ($_SERVER['HTTPS'] == 'on') {
            $protocol = 'https';
        } else {
            $protocol = 'http';
        }

        /**
         * Handle user initiated LinkedIn connection, create the LinkedIn object.
         */
        // set the callback url
        $API_CONFIG['callbackUrl'] = $protocol . '://' . $_SERVER['SERVER_NAME'] . ((($_SERVER['SERVER_PORT'] != '80') || ($_SERVER['SERVER_PORT'] != '443')) ? ':' . $_SERVER['SERVER_PORT'] : '') . $_SERVER['PHP_SELF'] . '?' . LINKEDIN::_GET_TYPE . '=initiate&' . LINKEDIN::_GET_RESPONSE . '=1';
        $OBJ_linkedin = new LinkedIn($API_CONFIG);

        // check for response from LinkedIn
        $_GET[LINKEDIN::_GET_RESPONSE] = (isset($_GET[LINKEDIN::_GET_RESPONSE])) ? $_GET[LINKEDIN::_GET_RESPONSE] : '';
        if (!$_GET[LINKEDIN::_GET_RESPONSE]) {
            // LinkedIn hasn't sent us a response, the user is initiating the connection

            // send a request for a LinkedIn access token
            $response = $OBJ_linkedin->retrieveTokenRequest();
            if ($response['success'] === TRUE) {
                // store the request token
                $_SESSION['oauth']['linkedin']['request'] = $response['linkedin'];

                // redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
                header('Location: ' . LINKEDIN::_URL_AUTH . $response['linkedin']['oauth_token']);
            } else {
                // bad token request
                return "Error importing from LinkedIn. Please try again or contact support.";
//                    echo "Request token retrieval failed:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response, TRUE) . "</pre><br /><br />LINKEDIN OBJ:<br /><br /><pre>" . print_r($OBJ_linkedin, TRUE) . "</pre>";
            }
        } else {
            // LinkedIn has sent a response, user has granted permission, take the temp access token, the user's secret and the verifier to request the user's real secret key
            $response = $OBJ_linkedin->retrieveTokenAccess($_SESSION['oauth']['linkedin']['request']['oauth_token'], $_SESSION['oauth']['linkedin']['request']['oauth_token_secret'], $_GET['oauth_verifier']);
            if ($response['success'] === TRUE) {
                // the request went through without an error, gather user's 'access' tokens
                $_SESSION['oauth']['linkedin']['access'] = $response['linkedin'];
                // set the user as authorized for future quick reference
                $_SESSION['oauth']['linkedin']['authorized'] = TRUE;
                // redirect the user back to the demo page
                header('Location: ' . $successRedirPath);
            } else {
                // bad token access
                return "Error importing from LinkedIn. Please try again or contact support.";
//                    echo "Access token retrieval failed:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response, TRUE) . "</pre><br /><br />LINKEDIN OBJ:<br /><br /><pre>" . print_r($OBJ_linkedin, TRUE) . "</pre>";
            }
        }
    } catch (LinkedInException $e) {
        // exception raised by library call
        return $e->getMessage();
    }
}

function linkedInRevoke($API_CONFIG)
{
    try {
        // start the session
        if (!session_start()) {
            throw new LinkedInException('This script requires session support, which appears to be disabled according to session_start().');
        }

        /**
         * Handle authorization revocation.
         */
        // check the session
        if (!oauth_session_exists()) {
            throw new LinkedInException('This script requires session support, which doesn\'t appear to be working correctly.');
        }

        $OBJ_linkedin = new LinkedIn($API_CONFIG);
        $OBJ_linkedin->setTokenAccess($_SESSION['oauth']['linkedin']['access']);
        $response = $OBJ_linkedin->revoke();
        if ($response['success'] === TRUE) {
            // revocation successful, clear session
            session_unset();
            $_SESSION = array();
            if (session_destroy()) {
                // session destroyed
                header('Location: ' . $_SERVER['PHP_SELF']);
            } else {
                // session not destroyed
                echo "Error clearing user's session";
            }
        } else {
            // revocation failed
            echo "Error revoking user's token:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response, TRUE) . "</pre><br /><br />LINKEDIN OBJ:<br /><br /><pre>" . print_r($OBJ_linkedin, TRUE) . "</pre>";
        }
    } catch (LinkedInException $e) {
        // exception raised by library call
        return $e->getMessage();
    }
}

function isLinkedInAuthorized()
{
    try {
        // start the session
        if ((session_status() == PHP_SESSION_NONE) && !session_start()) {
            throw new LinkedInException('This script requires session support, which appears to be disabled according to session_start().');
        }

        $_SESSION['oauth']['linkedin']['authorized'] = (isset($_SESSION['oauth']['linkedin']['authorized'])) ? $_SESSION['oauth']['linkedin']['authorized'] : FALSE;
        return $_SESSION['oauth']['linkedin']['authorized'];
    } catch (LinkedInException $e) {
        // exception raised by library call
        return $e->getMessage();
    }
}

function getLinkedInObj($API_CONFIG)
{
    $OBJ_linkedin = new LinkedIn($API_CONFIG);
    $OBJ_linkedin->setTokenAccess($_SESSION['oauth']['linkedin']['access']);
    $OBJ_linkedin->setResponseFormat(LINKEDIN::_RESPONSE_JSON);
    return $OBJ_linkedin;
}
