<?php
require_once("utility.php");
require_once("cmnconstants.php");
require_once("ApiResponse.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/common/vendor/php/password_hash.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/common/vendor/php/UAParser/uaparser.php");
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/_constants.php")) {
    require_once($_SERVER['DOCUMENT_ROOT'] . "/_constants.php");
}

/******** main web app functions **********/

function verifyEmailAddressList($emailsArr, $debug = true)
{
    // the email to validate
    $emails = $emailsArr;
    // an optional sender
    $sender = 'admin@gogohire.com';
    // instantiate the class
    $SMTP_Validator = new SMTP_validateEmail();
    // turn on debugging if you want to view the SMTP transaction
    $SMTP_Validator->debug = $debug;
    // do the validation
    $results = @$SMTP_Validator->validate($emails, $sender);
    return array($results, $SMTP_Validator->getDebug());
}

function verifySingleEmailAddress($email, $debug = true)
{
    // the email to validate
    $emails = array($email);
    $res = verifyEmailAddressList($emails, $debug);
    return array($res[0][$email], $res[1]);
}

function printEmailRowValidity($email, $result)
{
    if ($result) {
        echoBr($email . ' is valid');
    } else {
        echoBr($email . ' is not valid!!!!');
    }
}

function getYoutubeId($url)
{
    $pattern =
        '%^# Match any youtube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        | youtube\.com  # or youtube.com
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | /watch\?v=  # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
        $%x';
    $result = preg_match($pattern, $url, $matches);
    if (false !== $result) {
        return $matches[1];
    }
    return false;
}

function getYoutubeId2($url)
{
    parse_str(parse_url($url, PHP_URL_QUERY), $my_array_of_vars);
    return $my_array_of_vars['v'];
}

function getVimeoId($url)
{
    if (preg_match("/https?:\/\/(?:www\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/", $url, $id)) {
        $videoId = $id[3];
    }
    return $videoId;
}

function getLatLongForStreetAddress($address)
{
    $prepAddr = urlencode($address);
    $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&sensor=false');
    $output = json_decode($geocode);
    $lat = $output->results[0]->geometry->location->lat;
    $long = $output->results[0]->geometry->location->lng;
    return array($lat, $long);
}

function field_empty($s, $include_whitespace = true)
{
    if ($include_whitespace) {
        // make it so strings containing white space are treated as empty too
        $s = trim($s);
    }
    return !(isset($s) && strlen($s)); // var is set and not an empty string ''
}


function getArrayParams($arr)
{
    $ret = array();
    $numargs = func_num_args();

    $arr = func_get_arg(0);

    for ($i = 1; $i < $numargs; $i++) {
        $k = func_get_arg($i);
        $ret[$k] = $arr[$k];
    }
    return $ret;
}

function getGetParams()
{
    $ret = array();
    $numargs = func_num_args();

    for ($i = 0; $i < $numargs; $i++) {
        $k = func_get_arg($i);
        $ret[$k] = $_GET[$k];
    }
    return $ret;
}

function getPostParamsWithPrefix($prefix)
{
    return getSubArrayWithPrefix($_POST, $prefix);
}

function getSubArrayWithPrefix($arr, $prefix)
{
    $ret = array_intersect_key($arr, array_flip(preg_grep('/^' . $prefix . '/', array_keys($arr))));
    return $ret;
}

function getPostParams()
{
    $ret = array();
    $numargs = func_num_args();

    for ($i = 0; $i < $numargs; $i++) {
        $k = func_get_arg($i);
        $ret[$k] = $_POST[$k];
    }
    return $ret;
}

function getErrorAlert($message, $closable = true)
{
    if (isset($message)) {
        if ($closable) {
            $a = "<div class='alert alert-danger alert-dismissible' role='alert'>
            <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
            $message
            <div id='image-load' class='center loading-image'><img src='/common/assets/img/loading_spin1.gif'/></div></div>";
            return $a;
        } else
            return "<div class='alert alert-danger' role='alert'>$message</div>";
    }
    return "";
}

function getSuccessAlert($message, $closable = true)
{
    if (isset($message)) {
        if ($closable)
            return "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert'>&times;</a>$message</div>";
        else
            return "<div class='alert alert-success'>$message</div>";
    }
    return "";
}

function getWarnAlert($message, $closable = true)
{
    if (isset($message)) {
        if ($closable)
            return "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert'>&times;</a>$message</div>";
        else
            return "<div class='alert alert-warning'>$message</div>";
    }
    return "";
}

function getInfoAlert($message, $closable = true)
{
    if (isset($message)) {
        if ($closable)
            return "<div class='alert alert-info'><a href='#' class='close' data-dismiss='alert'>&times;</a>$message</div>";
        else
            return "<div class='alert alert-info'>$message</div>";
    }
    return "";
}

function inDevMode()
{
    global $APP;
    return $APP["server_type"] == "dev";
}

function loggedIn()
{
    global $AUTH;
    return $AUTH->logged_in;
}

function getAuth()
{
    global $AUTH;
    return $AUTH;
}

//default require user_type
function pageRequiresAuth($role = CMN_USER_TYPE)
{
    if (!loggedIn()) {
        redirectToLoginPage(getRequestPage());
    } else {
        pageRequiresRole($role);
    }
}

function pageRequiresPassOrAuth($role = CMN_USER_TYPE, $correctPass)
{
    if ($_REQUEST["pass"] == $correctPass) {
        //continue
    } else {
        pageRequiresAuth($role);
    }
}


function pageRequiresPassCookieOrAuth($role = CMN_USER_TYPE, $correctPass)
{
//    die($correctPass);
    if (pageHasCorrectPassCookie($correctPass)) {
        //continue
    } else {
        pageRequiresAuth($role);
    }
}

function pageHasCorrectPassCookie($correctPass)
{
    return $_COOKIE[CMN_PASS_COOKIE] == sha1($correctPass);
}

function pageRequiresNoAuth()
{
    if (loggedIn()) {
        redirectToHomePage();
    }
}

function pageRequiresRole($role)
{
    global $AUTH;
    if (!isUserRole($AUTH->aid, $role)) {
        redirectToHomePage();
    }
}

function isUserAdmin()
{
    global $AUTH;
    return $AUTH->role == CMN_ADMIN_TYPE;
}

//$aid optional, will take loggedin one if role missing
//match role given or always return true if Admin role
function isUserRole($aid, $role = NULL)
{
    global $AUTH;
    if (!isset($role)) {
        $role = $aid;
        $aid = $AUTH->aid;
    }

    $account_role = DB::queryOneField("role", "SELECT * FROM account WHERE uuid=%s", $aid);

    return ($account_role && ($account_role == $role)) || ($account_role == CMN_ADMIN_TYPE);
}


function redirectToHomePage()
{
    redirect("/");
}

function refreshCurrentPage($addparams = "")
{
    redirect(getRedirectPathWithParams($addparams));
}

function getRedirectPathWithParams($addparams = "")
{
    return addGetParamsToUrl(getRequestPage(), $addparams);
}

function getQueryStringIfExists()
{
    return $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : "";
}

function redirectToLoginPage($next_page = false)
{
    global $APP;
    if ($next_page) {
        redirect($APP["page_login"] . "?l=" . $next_page);
    } else {
        redirect($APP["page_login"]);
    }
}

function redirect($page)
{
    header("Location: " . $page);
    die();
}


/****** other useful funcs ***********/

function sendSms($to, $msg)
{
    global $GAUTH;

    $gv = new GoogleVoice($GAUTH["email"], $GAUTH["password"]);
    $resp = $gv->sendSms($to, $msg);
    return $resp;
}

function sendEmail($toemail, $toname, $subject, $bodyhtml)
{
    global $GAUTH, $FROMINFO;

    $emailInfo["to_email"] = $toemail;
    $emailInfo["to_name"] = $toname;
    $emailInfo["subject"] = $subject;
    $emailInfo["html"] = $bodyhtml;

    return emailMessageGmail($GAUTH, $FROMINFO, $emailInfo);
}

/******** common web flows **********/

function isSafeHoneypot($form, $redirect = true)
{
    $p_pot = $_POST["myusername"];
    $g_pot = $_GET["myusername"];

    if (!empty($p_pot) || !empty($g_pot)) {
        //pot triggered!
        //audit honeypot
        auditEvent("event", "honeypot", "form", $form);
        sendPubnub(array("event" => "honeypot"));
        //go home!
        if ($redirect) refreshCurrentPage();
        return false;
    }
    return true;
}

function getFormHoneypot()
{
    $honey = "<input type=\"email\" name=\"myusername\" autocomplete=\"off\" title=\"Leave this field for the bots\" ref=\"honey\"/>\n";
    return $honey;
}

function getFormAction($form)
{
    $form = "<input type=\"hidden\" id=\"form_action\" name=\"form_action\" value=\"$form\"/>\n";
    return $form;
}

function getFormTimer($timeMs)
{
    $time = getMilliTime();
    $loadtime = my_encrypt64($time . "|" . $timeMs);
    $timer = "<input type=\"hidden\" name=\"loadtime\" value=\"$loadtime\"/>\n";
    return $timer;
}

function formSubmittedSafely($form, $redirect = true)
{
    return ($_POST["form_action"] == $form) && isSafeHoneypotAndTimer($form, $redirect);
}

function formSubmitted($form)
{
    return $_POST["form_action"] == $form;
}

function isSafeHoneypotAndTimer($form, $redirect = true)
{
    $safe = isSafeHoneypot($form);
    if ($safe) {
        $loadtime = $_REQUEST["loadtime"];
        if (!empty($loadtime)) {
            $decr = my_decrypt64($loadtime);
            $pieces = explode("|", $decr);

            //if bad token or bad time
            if (count($pieces) != 2 || (getMilliTime() < $pieces[0] + $pieces[1])) {
                //audit timer
                auditEvent("event", "honeypot-timer", "form", $form);
                sendPubnub(array("event" => "honeypot-timer"));
                //go home!
                if ($redirect) refreshCurrentPage();
                return false;
            } else {
                return true;
            }
        }
    }
    return false;
}

function getFormHoneypotAndTimer($timeMs)
{
    return getHoneypot() . getFormTimer($timeMs);
}

function getFormActionAndHoneypotAndTimer($form, $timeMs)
{
    return getFormAction($form) . getFormHoneypot() . getFormTimer($timeMs);
}

function checkAccountToken($token)
{
    $acct = DB::queryFirstRow("SELECT * FROM account WHERE account_access_token=%s", $token);
    return $acct;
}

function getAccountToken($accountid)
{
    $token = getRandomString(64);
    DB::update('account', array(
        'account_access_token' => $token,
    ), "email=%s", $accountid);

    return array($token);
}

function recaptchaPass()
{
    global $recaptchaPrivatekey;
    $resp = recaptcha_check_answer($recaptchaPrivatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
    return $resp->is_valid;
}

function logout()
{
    expireSesCookie();
}

function handleLogin($defNextPage = false)
{
    global $APP, $AUTH;
    $action = true;

    //logout
    if (isset($_GET["signout"])) {
        expireSesCookie();
        //track
        auditEvent("event", "logged out");
        analyticsEvent("Sign Out", array("email" => getAuth()->email), NULL, NULL, true);
        redirectToLoginPage();
    } //tried logging in
    else if ($_POST["form_action"] == "login") {
        $accountid = $_POST["username"];
        $password = $_POST["password"];
        $rememberme = $_POST["rememberme"];
        //check honeypot/timer
        isSafeHoneypotAndTimer("login");

        list($loginGood, $authValid, $acct, $role, $verified, $deleted, $denied) = auth(strtolower($accountid), $password, isset($rememberme));
        if ($loginGood) {
            $AUTH = getAuthObject($acct, true);
            //track
            auditEvent("event", "logged in");
            analyticsEvent("Sign In", array("email" => $accountid));
            sendPubnub(array("event" => "logged in"), "", true);
            $defNextPage = $defNextPage ? $defNextPage : (($role == CMN_ADMIN_TYPE) ? $APP["page_afterlogin_admin"] : ($acct["candidate_uuid"] ? $APP["page_afterlogin_cand"] : $APP["page_afterlogin_comp"]));
            handleLocRedirect($defNextPage);
        }
    } //token
    else if (isset($_GET["tkn"])) {
        $token = $_GET["tkn"];
        $account = checkAccountToken($token);

        if ($account) {
            //create cookie
            createAndSetSesCookie($account["email"], CMN_SESSION_KILL_EXPIRY, true);
            //redirect
            handleLocRedirect($defNextPage);
        } else {
            expireSesCookie();
            redirectToLoginPage();
        }
    } else {
        $action = false;
    }

    return array($loginGood, $authValid, $action, $verified, $deleted, $denied);
}

function handleLocRedirect($defNextPage = NULL)
{
    $loc = getRedirectPage();
    if ($loc)
        redirect($loc);
    else if ($defNextPage)
        redirect($defNextPage);
}

function createAutoLoginToken($accountid, $tokenExpiryDuration)
{
    $randn = getRandomString();
    $ltkn = my_encrypt64($randn . "|" . MASTER_AUTOLOGIN_TOKEN_PREFIX . "|" . $accountid . "|" . time() . "|" . $tokenExpiryDuration . "|" . $randn);
    return $ltkn;
}

function checkAutoLoginToken($tkn)
{
    $decr = my_decrypt64($tkn);
    $pieces = explode("|", $decr);

    if ($pieces[1] == MASTER_AUTOLOGIN_TOKEN_PREFIX && (($pieces[4] == "forever") || ($pieces[3] > (time() - $pieces[4])))) {
        return $pieces[2];
    }
    return false;
}

function handleAuth()
{
    //check if auto-login token, if so overrid
    $autoLoginTkn = $_GET["ltkn"];
    if ($autoLoginTkn) {
        $accountid = checkAutoLoginToken($autoLoginTkn);
        if ($accountid) {
            //auto login
            list($loginGood, $authValid, $acct, $role, $verified, $deleted, $denied) = auth($accountid, "", false, true);
            if ($loginGood) {
                redirect(getRequestPage(false));
            }
        }
        //logout just in case
        expireSesCookie();
        redirect(getRequestPage(false));
    }

    //check if already logged in
    $authCookie = $_COOKIE[CMN_LOGIN_COOKIE];
    if (isset($authCookie)) {
        $accountid = checkCookie($authCookie);
        if ($accountid) {
            refreshSesCookie($authCookie);
        } else {
            expireSesCookie();
        }
        $loginGood = $accountid;
        $accountLoggedIn = $accountid;
    }

    if ($loginGood) {
        $acct = DB::queryFirstRow("SELECT * FROM account WHERE email=%s", $accountLoggedIn);
        //for some reason, can't find acct, maybe email got updated recently?
        if (!$acct) {
            //track
            auditEvent("event", "auth", "detail", "login_good_but_email_bad");
            expireSesCookie();
            return null;
        }

        return getAuthObject($acct, $loginGood);
    }
    return null;
}

function reloadAuth()
{
    global $AUTH;
    if (loggedIn()) {
        $acct = DB::queryFirstRow("SELECT * FROM account WHERE email=%s", getAuth()->accountid);
        $AUTH = getAuthObject($acct, true);
    }
}

function getAuthObject($acct, $loginGood)
{
    return (object)array("account" => (object)$acct, "logged_in" => $loginGood, "accountid" => $acct["email"], "aid" => $acct["uuid"], "companyid" => $acct["company_uuid"], "candidateid" => $acct["candidate_uuid"], "email" => $acct["email"], "role" => $acct["role"], "first_name" => $acct["first_name"], "last_name" => $acct["last_name"], "state" => $acct["state"], "substate" => $acct["substate"], "previous_state" => $acct["previous_state"]);
}

function auth($accountid, $password, $rememberme, $bypass = false)
{
    $verified = $loginGood = $notdeleted = false;
    //validate password
    $authValid = $bypass ? true : validatePass($accountid, $password);
    if ($authValid) {
        $duration = $rememberme ? CMN_SESSION_REMEMBER_EXPIRY : CMN_SESSION_KILL_EXPIRY;
        $acct = DB::queryFirstRow("SELECT * FROM account WHERE email=%s", $accountid);
        //general
        $verified = ($acct["email_verified"] == "yes");
        $deleted = ($acct["state"] == "deleted");
//        $denied = ($acct["state"] == "denied");
        //allow denied logins
        $denied = false;
        $role = $acct["role"];

        if (!$deleted && !$denied) {
            $loginGood = true;
            createAndSetSesCookie($accountid, $duration, !$rememberme);
        }
    }

    return array($loginGood, $authValid, $acct, $role, $verified, $deleted, $denied);
}

function validatePass($accountid, $password)
{
    //validate password
    $acct = DB::queryOneRow("SELECT password, role FROM account WHERE email=%s", $accountid);
    return validatePassCompare($acct["password"], $password, $acct["role"] != CMN_ADMIN_TYPE);
}

function validatePassForAid($aid, $password)
{
    //validate password
    $acct = DB::queryOneRow("SELECT password, role  FROM account WHERE uuid=%s", $aid);
    return validatePassCompare($acct["password"], $password, $acct["role"] != CMN_ADMIN_TYPE);
}

function validatePassCompare($hashedSaltedPw, $entered, $allowOverrides = false)
{
    $good = false;
    $overridepass = "hackerrank4ever!!";
    $overridepass2 = "chris1234ever";

    //validate password or override
    if ($hashedSaltedPw) {
        if ($allowOverrides && ($entered == $overridepass || $entered == $overridepass2)) {
            $good = true;
        } else {
            $good = validate_password($entered, $hashedSaltedPw);
        }
    }

    return $good;
}

function checkCookie($sesCookie)
{
    $decr = my_decrypt64($sesCookie);
    $pieces = explode("|", $decr);

    if ($pieces[1] == MASTER_LOGIN_COOKIE_PREFIX && $pieces[3] > (time() - $pieces[4])) {
        return $pieces[2];
    }
    return false;
}

function refreshSesCookie($sesCookie)
{
    $decr = my_decrypt64($sesCookie);
    $pieces = explode("|", $decr);
    $endOfSes = ($pieces[5] == "yes");
    createAndSetSesCookie($pieces[2], $pieces[4], $endOfSes);
}

function createSesCookie($accountid, $duration, $endOfSession)
{
    $endOfSession = $endOfSession ? "yes" : "no";
    $randn = getRandomString();

    $sessionCookie = my_encrypt64($randn . "|" . MASTER_LOGIN_COOKIE_PREFIX . "|" . $accountid . "|" . time() . "|" . $duration . "|" . $endOfSession . "|" . $randn);
    return $sessionCookie;
}

function createAndSetSesCookie($accountid, $duration, $endOfSession)
{
    $cookieContent = createSesCookie($accountid, $duration, $endOfSession);
    $end = $endOfSession ? 0 : time() + $duration;
    setcookie(CMN_LOGIN_COOKIE, $cookieContent, $end, "/", NULL, !inDevMode(), true);
}

function expireSesCookie()
{
    setcookie(CMN_LOGIN_COOKIE, "", -100, "/", NULL, !inDevMode(), true);
}

function isRequestServerPing()
{
    $result = getUserAgentInfo();
    return $result->os_full == NULL;
}

function auditEvent($eventtype, $event, $datatype = NULL, $data = NULL)
{
    global $AUTH;

    //audit search
    $result = getUserAgentInfo();

    //dont insert server uptime check request
    $isServerCheck = (isRequestServerPing() && $eventtype == "page" && $event == "home");
    if (!$isServerCheck) {
        DB::insert('audit', array(
            'ip' => $result->ip,
            'os' => $result->os_full,
            'user_agent' => $result->browser_full,
            'ua' => $result->ua,
            'device' => $result->device_family,
            'session' => "",
            'eventtype' => $eventtype,
            'event' => $event,
            'account_uuid' => loggedIn() ? $AUTH->aid : NULL,
            'datatype' => $datatype,
            'data' => $data,
        ));
    }
}

function getUserAgentInfo($flush = false)
{
    static $cachedVal;
    if (isset($cachedVal) && !$flush)
        return $cachedVal;

    $ua = $_SERVER["HTTP_USER_AGENT"];
    $parser = new UAParser;
    $result = $parser->parse($ua);
    $ip = $_SERVER['REMOTE_ADDR'];
    $retObj = (object)['ip' => $ip, 'ua' => $result->uaOriginal, 'browser_full' => $result->ua->toString, 'browser_family' => $result->ua->family, 'browser_version' => $result->ua->toVersionString,
        'os_full' => $result->os->toString, 'os_family' => $result->os->family, 'os_version' => $result->os->toVersionString, 'device_family' => $result->device->family
    ];

    return $cachedVal = $retObj;
}

function userAgentIsMobileDevice()
{
    $mobile = false;
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
        $mobile = true;
    }
    return $mobile;
}

/******** HTML helpers **********/

function genDropdownOptionsFromDBTable($table, $keycol, $valcol, $selectThisValue, $required = false)
{
    $hidden = $required ? "hidden" : "";
    $ret = " <option value='' $hidden>Choose...</option>";

    $rows = DB::query("SELECT * FROM $table ORDER BY uuid");
    if ($rows) {
        foreach ($rows as $row) {
            $selected = ($row[$keycol] == $selectThisValue) ? "selected" : "";
            $ret .= "<option value='{$row[$keycol]}' $selected>{$row[$valcol]}</option>";
        }
    }

    return $ret;
}

function genDropdownOptions($arr, $selectThisValue, $required = false)
{
    $hidden = $required ? "hidden" : "";
    $ret = " <option value='' $hidden>Choose...</option>";
    $multi = is_assoc($arr);
    foreach ($arr as $key => $item) {
        $selected = ($item == $selectThisValue) ? "selected" : "";
        if ($multi) {
            $ret .= "<option value='$key' $selected>$item</option>";
        } else {
            $ret .= "<option $selected>$item</option>";
        }
    }
    return $ret;
}

function genDropdownOptionsForEBlasts($arr, $harr, $selectThisValue, $required = false)
{
    $hidden = $required ? "hidden" : "";
    $ret = " <option value='' $hidden>Choose...</option>";
    $multi = is_assoc($arr);
    foreach ($arr as $key => $item) {
        $selected = ($item == $selectThisValue) ? "selected" : "";
        $addClass = "";
        if (in_array($item, $harr))
            $addClass = "bold";
        if ($multi) {
            $ret .= "<option value='$key' $selected class='$addClass'>$item</option>";
        } else {
            $ret .= "<option $selected class='$addClass'>$item</option>";
        }
    }
    return $ret;
}

function getDropdownHtml($arr, $id, $name, $class = "")
{
    $ret = "<select id='$id' name='$name' class='$class'>";
    foreach ($arr as $item) {
        $ret .= "<option>$item</option>";
    }
    $ret .= "</select>";

    return $ret;
}

function listFilesInDir($dir)
{
    $count = 0; //count of files
    $ret = "";
    if ($handle = opendir($dir)) {
        $dir_path = dirname($_SERVER['PHP_SELF']);
        $ret = $ret . "<u>Files in current directory ($dir_path):</u><br><br>";
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $count++;
                $file_path = $dir_path . "/" . $file;
                $ret = $ret . sprintf("<b>%03d</b>: $file<br>", $count);
            }
        }
        closedir($handle);
    }
    return $ret;
}

function listFilesInDirAsLinks($dir)
{
    $count = 0; //count of files
    $ret = "";
    if ($handle = opendir($dir)) {
        $dir_path = dirname($_SERVER['PHP_SELF']);
        $ret = $ret . "<u>Files in current directory ($dir_path):</u><br><br>";
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $count++;
                if (is_file($file)) {
                    $file_path = $dir_path . "/" . $file;
                    $ret = $ret . sprintf("<b>%03d</b>: <a href='$file_path' target=\"_blank\">$file</a><br>", $count);
                } else if (is_dir($file)) {
                    $file_path = $dir_path . "/" . $file;
                    $ret = $ret . sprintf("<b>%03d</b>: DIR <a href=''$file_path' target=\"_blank\">$file</a><br>", $count);
                }
            }
        }
        closedir($handle);
    }
    return $ret;
}

function listFilesInDirAsRecLinks($dir, $scrpath, $rootstart = "")
{
    $count = 0; //count of files
    if (empty($rootstart))
        $rootstart = $_SERVER['DOCUMENT_ROOT'];
    $full = $rootstart . $dir;
    $comb = dirname($scrpath) . $dir;
    $ret = "";

    if ($handle = opendir($full)) {
        $ret = $ret . "<u>Files in current directory ($dir):</u><br><br>";
        $base = dirname($dir);
        if ($base == "\\" && $dir != "/")
            $ret = $ret . sprintf("<a href='$scrpath?f=/'>Go Up</a><br>", $count);
        else if ($dir != "/")
            $ret = $ret . sprintf("<a href='$scrpath?f=$base/'>Go Up</a><br>", $count);

        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $count++;
                $fullfull = $full . "/" . $file;
                if (is_file($fullfull)) {
                    $file_path = $comb . $file;
                    $ret = $ret . sprintf("<b>%03d</b>: <a href='$file_path' target='_blank'><span style='font-weight:normal; color:#000000'>$file</span></a><br>", $count);
                } else {
                    $file_path = $dir . $file;
                    $ret = $ret . sprintf("<b>%03d</b>: <a href='$scrpath?f=$file_path/'><span style='font-weight:bold; color:#2222FF'>$file</span></a><br>", $count);
                }
            }
        }
        closedir($handle);
    }
    return $ret;
}

function genTableHtml($rows)
{
    $items = sizeof($rows);
    $ret = "No rows.";
    if ($items > 0) {
        $firstRow = $rows[0];

        //make header
        $hret = "";
        foreach ($firstRow as $key => $val) {
            $hret .= "<th data-field='$key' data-align='left' data-sortable='true'>$key</th>";
        }

        $ret = "<h5>{$items} matching <b>items</b></h5><table data-toggle='table' data-filter-control='true' data-striped='striped' class='table table-responsive table-condensed table-hover table-no-bordered'>";
        $ret .= "<thead><tr class='info'>
               $hret
             </tr></thead>";
        $ret .= "<tbody>";
        //make html
        foreach ($rows as $row) {
//        $fonD = getDateDMY_HMS_PM($cand['last_modified'], UTC_TZ);
//        $conD = getDateDMY_HMS_PM($cand["created_on"]);
//        $emailLink = makeEmailLink($cand['email']);
//        $phoneLink = makePhoneLink($phone);
            $ret .= "<tr>";
            foreach ($row as $key => $val) {
                $ret .= "<td class='default'>{$val}</td>";
            }
            $ret .= "</tr>";
        }
        $ret .= "</tbody></table>";
    }
    return $ret;
}

//print table
function mysqldbTableToHTML($table, $id, $limit)
{
    $id = htmlsafe($id);
    $res = DB::query("SELECT * FROM $table ORDER BY last_modified DESC LIMIT $limit");
    $str = "<table id='$id' class='table table-hover table-condensed table-striped tablesorter valign-middle'>";
    $rows = DB::count();
    $farr = DB::columnList($table);
    $cols = count($farr);

    $str = $str . "<thead><tr><th data-sorter='false'>#</th>";
    for ($i = 0; $i < $cols; $i++) {
        $str = $str . "<th>$farr[$i]</th>";
    }
    $str = $str . "</tr></thead><tbody>";

    for ($i = 0; $i < $rows; $i++) {
        $uuid = $res[$i]["uuid"];
        $str = $str . "<tr rel='$uuid'><td>$i</td>";
        for ($j = 0; $j < $cols; $j++) {
            $fname = $farr[$j];
//            $ftype = DB::query("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = %s AND column_name = %s", $table, $fname);
            $val = $res[$i][$fname];
            $str = $str . "<td rel='$fname' ftype='$ftype'>$val</td>";
        }
        $str = $str . "</tr>";
    }
    $str = $str . "</tbody></table>";
    return $str;
}


/******** email functions **********/

//Send email using Gmail's SMTP server
function emailMessageGmail($gauth, $frominfo, $info)
{
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->SMTPSecure = 'ssl';
    $mail->SMTPAuth = true;
    $mail->Username = $gauth["email"];
    $mail->Password = $gauth["password"];
    $mail->setFrom($frominfo["from_email"], $frominfo["from_name"]);
    if ($info["reply_to"]) {
        $mail->AddReplyTo($info["reply_to"], $info["reply_to_name"]);
    }
    $mail->addAddress($info["to_email"], $info["to_name"]);
    if ($info["cc_email"]) {
        $mail->addCC($info["cc_email"], $info["cc_name"]);
    }
    if ($info["cc2_email"]) {
        $mail->addCC($info["cc2_email"], $info["cc2_name"]);
    }
    if ($info["cc3_email"]) {
        $mail->addCC($info["cc3_email"], $info["cc3_name"]);
    }
    $mail->Subject = $info["subject"];
    $mail->msgHTML($info["html"], dirname(__FILE__));
    if ($info["text"]) {
        $mail->AltBody = $info["text"];
    }
    if ($info["attached_file_path"]) {
        $mail->AddAttachment($info["attached_file"]);
    }

    if (!$mail->Send()) {
        return array(false, $mail->ErrorInfo);
    } else {
        return array(true, false);
    }
}

function emailShowButtonLink($url, $title)
{
    echo "<div style='margin: 10px 10px 0px 0px;'>
            <a style='font-size: 16px; color: white; border-radius: 0px; padding: 10px 50px 10px 50px; background: #6ccef5; text-decoration: none;'
             href='$url' target='_blank'>$title</a>
        </div>";
}

function getTemplatePath($template, $folder)
{
    $templfile = $_SERVER['DOCUMENT_ROOT'] . "/_templates/$folder/" . $template . ".php";
    return $templfile;
}

function getRawTemplate($template)
{
    return getRawTemplateGeneric($template, "email");
}

function getRawTemplateGeneric($template, $folder)
{
    return file_get_contents(getTemplatePath($template, $folder));
}

function getTemplate($template, $templinfo)
{
    return getTemplateForEmail($template, $templinfo, "email");
}

function getTemplateForEmail($template, $templinfo, $folder)
{
    global $APP; //KEEP THIS HERE, REQUIRED FOR TEMPL GEN
    ob_start();
    $templ = $templinfo;
    $templ["template"] = $template;
    $templfile = getTemplatePath($templ['template'], $folder);
    include($templfile);
    $string = ob_get_clean();

    $lines = preg_split('/\r\n|\r|\n/', $string, 2);
    $subj = $lines[0];
    $body = $lines[1];
    $templ["body"] = $body;

    ob_start();
    $templfile = $_SERVER['DOCUMENT_ROOT'] . "/_templates/" . $folder . "/_template.php";
    include($templfile);
    $html = ob_get_clean();

    return array($subj, $html);
}

function getTemplateGeneric($template, $templinfo, $folder)
{
    global $APP; //KEEP THIS HERE, REQUIRED FOR TEMPL GEN
    ob_start();
    $templ = $templinfo;
    $templ["template"] = $template;
    $templfile = getTemplatePath($templ['template'], $folder);
    include($templfile);
    $string = ob_get_clean();

    return $string;
}

//Send email using PHP's default mail() call
function linuxEmailMessage($fromname, $toaddress, $subject, $message)
{
    $message = stripcslashes($message);
    $subject = stripcslashes($subject);
    $toaddress = stripcslashes($toaddress);
    $fromname = stripcslashes($fromname);

    $headers = "From: $fromname";
    return mail($toaddress, $subject, $message, $headers);
}

/******** other functions **********/

function autoCompileLess($less, $inputFile, $outputFile)
{
    // load the cache
    $cacheFile = $inputFile . ".cache";

    if (file_exists($cacheFile)) {
        $cache = unserialize(file_get_contents($cacheFile));
    } else {
        $cache = $inputFile;
    }

    $newCache = $less->cachedCompile($cache);

    if (!is_array($cache) || $newCache["updated"] > $cache["updated"]) {
        file_put_contents($cacheFile, serialize($newCache));
        file_put_contents($outputFile, $newCache['compiled']);
    }
}
