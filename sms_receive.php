<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/common/includes/util_inc.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/shared.php");
require_once("shared.php");

//handle both twilio and nexmo
$phone = $_GET["msisdn"] ? $_GET["msisdn"] : $_REQUEST['From'];
$text = $_GET["text"] ? $_GET["text"] : $_REQUEST['Body'];
$test = $_GET["test"];
$phone = cleanPhone($phone);

ob_start();
print_r_pre($_REQUEST);
$myStr = ob_get_contents();
ob_end_clean();

file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/sms_callback.txt", $myStr . "\n");

//logic
if ($text) {
    processIncomingSMS($phone, $text);
//    $r = sendSMSTwilio($phone, "Jai Kisan\nReply 1 to check status of your farm\nReply 2 to add a new farm\nReply 3 for us to call you on phone instead");
} else if ($test) {
    echo $test;
}