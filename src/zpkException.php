<?php

namespace zpksystems\phpzpk;

define("EX_RESPONSE_ERRORS",500);
define("EX_FILE_NOT_FOUND",501);
define("EX_INCOMPATIBLE_PARAMETERS",502);
define("EX_INSUFFICIENT_PARAMETERS",503);
define("EX_INVALID_OPERATION",504);
define("EX_NETWORK_ERROR",505);
define("EX_INVALID_APPLICATION_ID",506);
define("EX_INVALID_API_KEY",507);
define("EX_DEACTIVATED_APPLICATION",508);
define("EX_DELETED_APPLICATION",509);
define("EX_UPLOAD_ERROR",510);

/**
 * ZPK Exception class
 *
 * @author Morgan Olufsen [morgan@zpk.systems]
 * @license MIT
 *
 */
class zpkException extends \Exception{

	private array $response_errors;

    public function __construct(int $code,string $message) {
		$this->response_errors = [];
        parent::__construct("$code, $message",$code);
    }

	public function setResponseErrors( array $errors ){
		$this->response_errors = $errors;
	}

	public function getResponseErrors(): array{
		return $this->response_errors;
	}

    public function __toString() {
		if( count($this->response_errors) > 0 ){
			return __CLASS__ . ": [CODE: {$this->code}]: MESSAGE: {$this->message} ResponseErrors: ".json_encode($this->response_errors)."\n";
		}else{
			return __CLASS__ . ": [CODE: {$this->code}]: MESSAGE: {$this->message}\n";
		}
    }
}

