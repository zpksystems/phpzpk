<?php

namespace zpksystems\phpzpk;

/**
 * Wrapper for ZPK SMS API, Pricing
 * 
 * @author Morgan Olufsen [morgan@zpk.systems]
 * @author ZPK Systems
 *
 * @license MIT
 *
 */

class zpkSmsPriceRequest{

	private zpkApplication $application;
	private string $country_code = '';
	private string $prefix = '';
	private array $response = [];

	public function __construct( zpkApplication $application )
	{
		$this->application = $application;
	}

	private function setResponse( array $response ){
		$this->response = $response;
	}

	public function getResponse():array{
		return $this->response;
	}

	public function getPrices(){
		$request = new zpkRequest($this->application);
		$request->setEndpoint('/api/sms/pricing');

		if( !empty($this->country_code) ){
			$request->setParameter('country_code',$this->country_code);
		}

		if( !empty($this->prefix) ){
			$request->setParameter('prefix',$this->prefix);
		}

		$response = $request->run();
		$this->setResponse($response);
		return $this->getResponse();


	}

	public function setCountryCode( string $country_code ){
		$this->country_code = $country_code;
	}

	public function setPrefix( string $prefix ){
		$this->prefix = $prefix;
	}


}

