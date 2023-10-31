<?php

namespace zpksystems\phpzpk;

define("EX_FILE_NOT_FOUND",1);
define("EX_INCOMPATIBLE_PARAMETERS",2);
define("EX_INSUFFICIENT_PARAMETERS",3);
define("EX_NETWORK_ERROR",4);

define("EX_INVALID_APPLICATION_ID",500);
define("EX_INVALID_API_KEY",501);
define("EX_DEACTIVATED_APPLICATION",502);
define("EX_DELETED_APPLICATION",503);
define("EX_UPLOAD_ERROR",504);

/**
 * ZPK Exception class
 *
 * @author Morgan Olufsen [morgan@zpk.systems]
 * @license MIT
 *
 */
class zpkException extends \Exception{

    public function __construct(string $code,string $message) {
        parent::__construct("$code, $message",500);
    }

    public function __toString() {
        return __CLASS__ . ": [CODE: {$this->code}]: MESSAGE: {$this->message}\n";
    }
}

