<?php
require_once("coreutil.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/common/vendor/php/meekrodb.2.3.class.php");

/******** core db functions **********/

function getCurTimeForDB()
{
    return date("Y-m-d H:i:s");
}

function getTimeForDB($time)
{
    return date("Y-m-d H:i:s", $time);
}

//logon user
function mysqldb_user_login($user, $password)
{
    if ($dbh = mysql_connect("127.0.0.1", $user, $password)) {
    } else {
        die("Error logging into database.<br>");
        error_log("MYSQL :: User $user not logged in to MYSQL system because: " . mysql_error() . " < br>");
    }
}

//connect to DB
function mysqldb_connect_to_db($db)
{
    if (mysql_select_db($db)) {
    } else {
        die("Error connecting to database .<br > ");
        error_log("MYSQL :: Failed to connect to MYSQl DB $db .<br > ");
    }
}

//work DB
function mysqldb_connect_full()
{
    global $_MYSQL_USERNAME, $_MYSQL_PASSWORD, $_MYSQL_DATABASE;
    mysqldb_user_login($_MYSQL_USERNAME, $_MYSQL_PASSWORD);
    mysqldb_connect_to_db($_MYSQL_DATABASE);
}

//close DB
function mysqldb_close()
{
    mysql_close();
}

function sqlsafehelper($value)
{
    return mysqldb_safeinput($value);
}

function sqlsafe()
{
    return mapFuncOnArgs("sqlsafehelper", func_get_args());
}

//Add slashes to incoming data
function mysqldb_safeinput($value)
{
    $magic_quotes_active = get_magic_quotes_gpc();
    $new_enough_php = function_exists("mysql_real_escape_string");
    // i.e PHP >= v4.3.0
    if ($new_enough_php) {
        //undo any magic quote effects so mysql_real_escape_string can do the work
        if ($magic_quotes_active) {
            $value = stripslashes($value);
        }
        $value = mysql_real_escape_string($value);
    } else { // before PHP v4.3.0
        // if magic quotes aren't already on this add slashes manually
        if (!$magic_quotes_active) {
            $value = addslashes($value);
        } //if magic quotes are avtive, then the slashes already exist
    }
    return $value;
}

//query
function mysqldb_query($query)
{
    return mysql_query($query);
}

//num rows
function mysqldb_num_rows($res)
{
    return mysql_num_rows($res);
}

//query result
function mysqldb_result($res, $row, $field)
{
    return mysql_result($res, $row, $field);
}

//query result with row
function mysqldb_query_result($query, $row, $field)
{
    $res = mysqldb_query($query);
    if ($res == NULL || mysqldb_num_rows($res) <= $row)
        return NULL;
    else
        return mysqldb_result($res, $row, $field);
}

//query full
function mysqldb_query_and_close($query)
{
    mysqldb_connect_db_full();
    $res = mysqldb_query($query);
    mysqldb_close();
    return $res;
}

function mysqldb_getcount($table, $key = NULL, $keyval = NULL)
{
    $table = sqlsafe($table);
    if (isset($key) && isset($keyval)) {
        $res = mysqldb_query("SELECT count(*) FROM $table WHERE `$key` = '$keyval';");
    } else {
        $res = mysqldb_query("SELECT count(*) FROM $table;");
    }
    return mysql_result($res, 0, 0);
}

function mysqldb_getprop($table, $key, $keyval, $prop, $default = NULL)
{
    list($table, $key, $keyval, $prop) = sqlsafe($table, $key, $keyval, $prop);
    $ret = mysqldb_query_result("SELECT $prop FROM $table WHERE `$key` = '$keyval'", 0, $prop);
    if (!$ret && isset($default)) {
        $ret = $default;
    }
    return $ret;
}

function mysqldb_existsrow($table, $key, $keyval)
{
    list($table, $key, $keyval) = sqlsafe($table, $key, $keyval);
    $ret = mysqldb_query("SELECT 1 FROM $table WHERE `$key` = '$keyval' LIMIT 1");
    return mysql_num_rows($ret) == 1;
}

function mysqldb_getrow($table, $key, $keyval)
{
    list($table, $key, $keyval) = sqlsafe($table, $key, $keyval);
    $ret = mysqldb_query("SELECT * FROM $table WHERE `$key` = '$keyval'");
    if ($ret) {
        $ret = mysql_fetch_array($ret, MYSQL_BOTH);
    }
    return $ret;
}

function mysqldb_deleterow($table, $key, $keyval)
{
    list($table, $key, $keyval) = sqlsafe($table, $key, $keyval);
    $ret = mysqldb_query("DELETE FROM $table WHERE `$key` = '$keyval'");
    return $ret;
}

function mysqldb_setprop($table, $key, $keyval, $prop, $propval, $proptype = "string")
{
    list($table, $key, $keyval, $prop, $propval) = sqlsafe($table, $key, $keyval, $prop, $propval);
    if ($proptype == "string") {
        $proppart = "'$propval'";
    } else {
        $proppart = "$propval";
    }

    $ret = mysqldb_query("UPDATE $table SET `$prop` = $proppart WHERE `$key` = '$keyval'");
    return $ret;
}

//get fields names from table
function mysqldb_table_fields($table)
{
    $table = sqlsafe($table);
    $res = mysqldb_query("SHOW COLUMNS FROM $table");
    $arr = array();
    if (mysqldb_num_rows($res) > 0) {
        while ($row = mysql_fetch_assoc($res)) {
            $arr[] = $row['Field'];
        }
    }
    return $arr;
}

function mysqldb_get_tables($schema)
{
    $schema = sqlsafe($schema);
    $res = mysqldb_query("SELECT table_name FROM INFORMATION_SCHEMA . TABLES  WHERE table_schema = '$schema' ORDER BY table_name");
    $arr = array();
    if (mysqldb_num_rows($res) > 0) {
        while ($row = mysql_fetch_assoc($res)) {
            $arr[] = $row['table_name'];
        }
    }
    return $arr;
}

function mysqldb_get_pk($table)
{
    $table = sqlsafe($table);
    $res = mysqldb_query("SHOW KEYS from $table where key_name = 'PRIMARY';");
    return mysqldb_result($res, 0, "column_name");
}

function dbGetPK($table)
{
    return DB::queryOneField("Column_name", "SHOW KEYS from $table where key_name = 'PRIMARY';");
}

function dbGetRowForUUID($table, $uuid)
{
    return DB::queryFirstRow("SELECT * FROM %l WHERE uuid=%s", $table, $uuid);
}


/******** extra functions **********/

//print res
function mysqldb_results_to_string($res)
{
    $str = " < table border = \"1\" style=\"width:800px;\">";
    $rows = mysqldb_num_rows($res);
    $cols = mysql_num_fields($res);
    for ($i = 0; $i < $rows; $i++) {
        $str = $str . "<tr><td>ROW $i:</td>";
        for ($j = 0; $j < $cols; $j++) {
            $fname = mysql_field_name($res, $j);
            $val = mysqldb_result($res, $i, $fname);
            $str = $str . "<td>$fname = [$val]</td>";
        }
        $str = $str . "</tr>";
    }
    $str = $str . "</table>";
    return $str;
}


