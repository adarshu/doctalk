<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/common/includes/util_inc.php");
require_once("shared.php");

function preSetup()
{
    header('Content-Type: application/json');
}

$action = $_GET['a'];
$pid = $_GET['pid'];
$msg = $_GET['msg'];

preSetup();
if ($action == "msgs") {
    $msghtml = genUserMsgs($pid);
    $tophtml = genMsgsTop($pid);
    clearUnreadMessagesForUser($pid);
    echo json_encode(array("html" => $msghtml, "top" => "$tophtml"));
} else if ($action == "users") {
    echo json_encode(array("html" => genUserBlocks()));
} else if ($action == "postmsg") {
    postMsg($pid, $msg);
    $msghtml = genUserMsgs($pid);
    $tophtml = genMsgsTop($pid);
    echo json_encode(array("html" => $msghtml, "top" => "$tophtml"));
}

?>