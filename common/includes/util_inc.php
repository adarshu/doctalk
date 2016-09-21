<?php

if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/_constants.php")) {
    require_once($_SERVER['DOCUMENT_ROOT'] . "/_constants.php");
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/common/php/webapplib.php");

if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/db_inc.php")) {
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/db_inc.php");
}

if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/track/serverstatus.php") && !$UNAVAILABLE_PAGE) {
    require_once($_SERVER['DOCUMENT_ROOT'] . "/track/serverstatus.php");
}


