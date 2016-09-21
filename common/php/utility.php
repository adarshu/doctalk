<?php
require_once("coreutil.php");

//do by default if this file included
date_default_timezone_set('America/Los_Angeles');
//error_reporting(E_STRICT);

/******** help functions **********/

function is_assoc($a)
{
    foreach (array_keys($a) as $key)
        if (!is_int($key)) return TRUE;
    return FALSE;
}

function preventDirectIncludeAccess()
{
    if (count(get_included_files()) == 1) exit("Direct access not permitted.");
}

function includePageString($page)
{
    ob_start();
    include($page);
    return ob_get_clean();
}

function ob_clean_all()
{
    $ob_active = ob_get_length() !== false;
    while ($ob_active) {
        ob_end_clean();
        $ob_active = ob_get_length() !== false;
    }

    return true;
}

function echoJsStart()
{
    echo "<script type=\"text/javascript\">";
}

function echoJsEnd()
{
    echo "</script>";
}

function echoJsVar($name, $val, $literal = false)
{
    if ($literal) {
        echo "var $name = $val;";
    } else {
        echo "var $name = \"$val\";";
    }
}

function echoPre($txt)
{
    echo "<pre>" . $txt . "</pre>";
}

function print_r_pre($r)
{
    echo "<pre>";
    print_r($r);
    echo "</pre>";
}

function get_r_pre($r)
{
    ob_start();
    echo "<pre>";
    print_r($r);
    echo "</pre>";
    return ob_get_clean();
}

function isAgentAndroid()
{
    $agent = $_SERVER['HTTP_USER_AGENT'];
    return strpos($agent, "Android");
}

function isAgentIphone()
{
    $agent = $_SERVER['HTTP_USER_AGENT'];
    return strpos($agent, "iPhone");
}

function isAgentTouchpad()
{
    $agent = $_SERVER['HTTP_USER_AGENT'];
    return strpos($agent, "TouchPad");
}

/******** file functions **********/

function getFileSize($file)
{
    $size = filesize($file);
    if ($size < 0)
        if (!(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'))
            $size = trim(`stat -c%s $file`);
        else {
            $fsobj = new COM("Scripting.FileSystemObject");
            $f = $fsobj->GetFile($file);
            $size = $file->Size;
        }
    return $size;
}

//get image data uri
function getImageDataUri($imgpath)
{
    $type = pathinfo($imgpath, PATHINFO_EXTENSION);
    $data = file_get_contents($imgpath);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    return $base64;
}

// ensure $dir ends with a slash
function delTree($dir)
{
    $files = glob($dir . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (substr($file, -1) == '/')
            delTree($file);
        else
            unlink($file);
    }
}

//remove all files in dir
function emptyDirectory($dir)
{
    foreach (new DirectoryIterator($dir) as $fileInfo)
        if (!$fileInfo->isDot()) {
            unlink($dir . $fileInfo->getFilename());
        }
}


//get mime type of file
function getMimeTypeOrig($f)
{
    return mime_content_type($f);
}

function readfile_chunked($filename, $retbytes = true)
{
    $chunksize = 1 * (8 * 1024); // how many bytes per chunk
    $buffer = '';
    $cnt = 0;
    // $handle = fopen($filename, 'rb');
    $handle = fopen($filename, 'rb');
    if ($handle === false) {
        return false;
    }
    while (!feof($handle)) {
        $buffer = fread($handle, $chunksize);
        echo $buffer;
        if ($retbytes) {
            $cnt += strlen($buffer);
        }
    }
    $status = fclose($handle);
    if ($retbytes && $status) {
        return $cnt; // return num. bytes delivered like readfile() does.
    }
    return $status;
}

function downloadFile($fileToDown, $fileName)
{
    if (is_file($fileToDown)) {
        $flength = filesize($fileToDown);

        header("Content-Length: $flength");
        header("Content-Type: application/octet-stream;");
        header("Content-Disposition: attachment; filename=\"" . $fileName . "\";");
        header("Pragma: public");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        ob_clean();
        flush();

        readfile_chunked($fileToDown);
    } else {
        echo "Not a file!";
    }
}

function forceDownloadStr($data, $name, $mimetype = '', $filesize = false)
{
    // File size not set?
    if ($filesize == false OR !is_numeric($filesize)) {
        $filesize = strlen($data);
    }

    // Mimetype not set?
    if (empty($mimetype)) {
        $mimetype = 'application/octet-stream';
    }

    // Make sure there's not anything else left
    ob_clean_all();

    // Start sending headers
    header("Pragma: public"); // required
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false); // required for certain browsers
    header("Content-Transfer-Encoding: binary");
    header("Content-Type: " . $mimetype);
    header("Content-Length: " . $filesize);
    header("Content-Disposition: attachment; filename=\"" . $name . "\";");

    // Send data
    echo $data;
    die();
}


function getFilesInDir($dir, $stripext, $listtype = "all", $onlyext = "", $fullpath = false)
{
    $count = 0; //count of files
    $ret = array();
    if ($handle = opendir($dir)) {
        $dir_path = dirname($_SERVER['PHP_SELF']);
        while (false !== ($file = readdir($handle))) {
            $full = $dir . $file;
            if ($file != "." && $file != "..") {
                if ($listtype == "all" || ($listtype == "files" && is_file($full)) || ($listtype == "dirs" && is_dir($full))) {
                    if ((is_string($onlyext) && $onlyext == "") || in_array(strtolower(getFileExtension($file)), $onlyext)) {
                        $file_path = $dir_path . "/" . $file;
                        if ($stripext) $file = stripFileExtension($file);
                        $count++;

                        if ($fullpath)
                            $ret[] = $full;
                        else
                            $ret[] = $file;
                        //echo $full . " IS FILE";
                    } else {
                        //echo $full . " IS NOT";
                    }
                }
            }
        }
        closedir($handle);
    }
    return $ret;
}

function getFileExtension($filename)
{
    $found = strrpos($filename, '.');
    if ($found)
        return substr($filename, $found, strlen($filename));
    else
        return "";
}

function getDrive($filename)
{
    $found = strpos($filename, ':');
    if ($found)
        return substr($filename, 0, $found);
    else
        return "";
}

function getFilename($filename)
{
    $found = strrpos($filename, '/');
    if ($found)
        return substr($filename, $found + 1, strlen($filename));
    else
        return $filename;
}

function stripDrive($filename)
{
    $found = strpos($filename, '/');
    if ($found)
        return substr($filename, $found + 1, strlen($filename));
    else
        return $filename;
}

function stripFileExtension($filename)
{
    $found = strrpos($filename, '.');
    if ($found)
        return substr($filename, 0, $found);
    else
        return $filename;
}

//size must be in bytes
function formatFileSize($size, $round = 0)
{
    //Size must be bytes!
    $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $total = count($sizes);
    for ($i = 0; $size > 1024 && $i < $total; $i++) $size /= 1024;
    return round($size, $round) . " " . $sizes[$i];
}

/******** web functions **********/

function getRedirectPage()
{
    $loc = $_GET["l"];

//    $printArray = explode("?", $loc);
//    $fpart = $printArray[0];

    //check to make sure is real file
//    if (is_file($_SERVER['DOCUMENT_ROOT'] . "/" . $fpart)) {
//        return $loc;
//    } else {
//        return false;
//    }

    //no need to check if file since not doing file inclusion anyway
    return $loc;
}

function getRequestUrl($getqueryparams = true)
{
    return getRequestUrlBase() . getRequestPage($getqueryparams);
}

function getRequestPage($getqueryparams = true)
{
    return $getqueryparams ? $_SERVER['REQUEST_URI'] : parse_url($_SERVER['REQUEST_URI'])["path"];
}

function addGetParamToUrl($url, $varName, $value)
{
    // is there already an ?
    if (strpos($url, "?")) {
        $url .= "&" . $varName . "=" . $value;
    } else {
        $url .= "?" . $varName . "=" . $value;
    }
    return $url;
}

function addGetParamsToUrl($url, $params)
{
    if (!empty($params) && !strstr(parse_url($url)["query"], $params)) {
        // is there already an ?
        if (strpos($url, "?")) {
            $url .= "&" . $params;
        } else {
            $url .= "?" . $params;
        }
    }
    return $url;
}

// Get full request url base
function getRequestUrlBase()
{
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/") . $s;
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER["SERVER_PORT"]);
    return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port;
}

function getCurPagePathNoParams()
{
    return basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
}

// Get server addr
function getServerAddr()
{
    return $_SERVER["SERVER_ADDR"];
}

// Get server host name
function getServerName()
{
    return $_SERVER["SERVER_NAME"];
}

