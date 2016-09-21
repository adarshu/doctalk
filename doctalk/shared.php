<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/common/includes/util_inc.php");

function getUsers()
{
    $users = DB::query("SELECT * FROM airmeet.doctalk_users WHERE pid!='0' ORDER BY uuid ASC");
    return $users;
}

function getUser($pid)
{
    $user = DB::queryFirstRow("SELECT * FROM airmeet.doctalk_users WHERE pid=%s", $pid);
    return $user;
}


function getMessagesForUser($pid)
{
    $msgs = DB::query("SELECT * FROM airmeet.doctalk_messages WHERE src_pid=%s OR dest_pid=%s ORDER BY last_modified ASC", $pid, $pid);
    return $msgs;
}

function clearUnreadMessagesForUser($pid)
{
    DB::query("UPDATE airmeet.doctalk_messages SET `read`='yes' WHERE src_pid=%s OR dest_pid=%s", $pid, $pid);
}

function genUserBlock($user)
{
    $countUnread = DB::queryFirstField("SELECT count(*) FROM airmeet.doctalk_messages WHERE (src_pid=%s OR dest_pid=%s) AND `read`='no' ORDER BY last_modified ASC", $user["pid"], $user["pid"]);
    $newShow = ($countUnread != 0) ? "$countUnread new" : "";
    $nameShow = ($countUnread != 0) ? "<b>{$user["name"]}</b>" : "{$user["name"]}";
//    $active = ($countUnread != 0) ? "active" : "";
    $ret = "<div class='lv-item media user-node ' data-pid='{$user["pid"]}'>
                            <div class='lv-avatar pull-left'><img src='{$user["pic"]}' alt=''></div>
                            <div class='media-body'>
                                <div class='lv-title'>$nameShow<span class='pull-right num-new'>$newShow</div>
                                <div class='lv-small'><b>{$user["desc"]}</b></div>
                            </div>
                        </div>";
    return $ret;
}

function genUserBlocks()
{
    $ret = "";
    $users = getUsers();
    foreach ($users as $user) {
        $msgs = getMessagesForUser($user["pid"]);
        if ($msgs)
            $ret .= genUserBlock($user);
    }
    return $ret;
}

function genMsg($msg, $pid)
{
    $user = getUser($pid);
    $mleftright = ($pid == $msg["src_pid"]) ? "" : "right";
    $pleftright = ($pid == $msg["src_pid"]) ? "pull-left" : "pull-right";
    $picUse = ($pid == $msg["src_pid"]) ? $user["pic"] : "/doctalk/pics/anuhya.jpg";
    $addClass = ($msg["priority"] == "yes") ? "dangerous" : "";

    if ($msg["picmsg"] != null) {
        $showMsg = "<img src='{$msg["picmsg"]}' alt='' style='max-width: 200px'>";
    } else {
        $showMsg = $msg["message"];
    }

    $ret = "<div class='lv-item media $mleftright'>
                            <div class='lv-avatar $pleftright'><img src='$picUse' alt=''></div>
                            <div class='media-body'>
                                <div class='ms-item $addClass'>$showMsg</div>
                                <small class='ms-date'><span class='glyphicon glyphicon-time'></span>&nbsp; {$msg["last_modified"]}</small>
                            </div>
                        </div>";
    return $ret;
}

function genMsgsTop($pid)
{
    $user = getUser($pid);
    $ret = "<div class='top-avatar lv-avatar pull-left' data-pid='{$pid}'><img src='{$user["pic"]}' alt=''></div>
                            <span class='c-black'>{$user["name"]}</span>";
    return $ret;
}

function genUserMsgs($pid)
{
    $ret = "";
    $msgs = getMessagesForUser($pid);
    foreach ($msgs as $msg) {
        $ret .= genMsg($msg, $pid);
    }
    return $ret;
}

function postMsg($pid, $msg)
{
    DB::insert('airmeet.doctalk_messages', array(
        'src_pid' => '0',
        'dest_pid' => $pid,
        'message' => $msg,
        'read' => 'yes'
    ));
    //send over sms to patient
    $user = getUser($pid);
    $r = sendSMSTwilio($user["phone"], $msg);
}

function getUserWithPhone($phone)
{
    return DB::queryFirstRow("SELECT * FROM airmeet.doctalk_users WHERE phone=%s", $phone);
}

function processIncomingSMS($phone, $text)
{
    $res = file_get_contents("http://hack16.herokuapp.com/watson_response?text=" . urlencode($text));
    $jres = json_decode($res, true);

    $priority = ($jres["result"] == "status_1") ? "yes" : "no";

    //add messsage
    $userWithPhone = getUserWithPhone($phone);
    DB::insert('airmeet.doctalk_messages', array(
        'src_pid' => $userWithPhone["pid"],
        'dest_pid' => '0',
        'message' => $text,
        'aimessage' => $res,
        'read' => 'no',
        'priority' => $priority
    ));

    //tell Dr UI to refresh
    $userWithPhone = getUserWithPhone($phone);
    sendPubnub(array("event" => "incoming", "user" => $userWithPhone["pid"]), "doctalk-ch");

    //show danger
    if ($priority == "yes") {
        //send to real dr phone
        $dr = getUser(0);
        $r = sendSMSTwilio($dr["phone"], "Priority msg from " . $userWithPhone["name"] . ": " . $text);
    } // regular case
    else if ($jres["result"] == "status_2") {
    } //bot case
    else {
        $botmsg = "Watson automated message: " . $jres["result"];
        DB::insert('airmeet.doctalk_messages', array(
            'src_pid' => '0',
            'dest_pid' => $userWithPhone["pid"],
            'message' => $botmsg,
            'aimessage' => '',
            'read' => 'no'
        ));
        //send patient the bot msg (prepend)
        $r = sendSMSTwilio($phone, $botmsg);
    }
}


?>