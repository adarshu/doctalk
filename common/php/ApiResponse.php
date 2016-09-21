<?php

class ApiResponse
{
    private $success = true;
    private $result = NULL;
    private $error = NULL;
    private $errors = array();
    private $okmsg = NULL;

    function ok()
    {
        return $this->success;
    }

    function failed()
    {
        return !$this->success;
    }

    function result()
    {
        return $this->result;
    }

    function setResult($result)
    {
        $this->result = $result;
    }

    function error()
    {
        return $this->error;
    }

    function setError($error)
    {
        $this->error = $error;
        $this->success = false;
    }

    function errors()
    {
        return $this->errors;
    }

    function hasError($name)
    {
        return array_key_exists($name, $this->errors);
    }

    function getMessage($name)
    {
        return $this->errors[$name];
    }

    function setOkMessage($msg)
    {
        $this->okmsg = $msg;
    }

    function getOkMessage()
    {
        return $this->okmsg;
    }

    //i
    function addError($name, $message = NULL)
    {
        $this->errors[$name] = $message;
        $this->setError($message);
    }

    function setFailed()
    {
        $this->success = false;
        $this->error = "An error has occurred. Please try again later.";

//        $callers=debug_backtrace();
//        echo $callers[1]['function'];
    }

    function toArray()
    {
        return array("success" => $this->success, "result" => $this->result, "error" => $this->error, "okmsg" => $this->okmsg);
    }

}
