<?php 

namespace zpksystems\phpzpk;

/**
 * zpkApplication
 *
 * Holds application_id & api_key for a specific
 * user application.
 * 
 * @author Morgan Olufsen [morgan@zpk.systems]
 * @license MIT
 *
 */

class zpkApplication
{
	private string $application_id;
	private string $api_key;

	public function __construct( string $application_id, $api_key ){
		$this->application_id = $application_id;
		$this->api_key = $api_key;
	}

	public function getApiKey(){
		return $this->api_key;
	}

	public function getApplicationId(){
		return $this->application_id;
	}

}


