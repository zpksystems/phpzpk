<?php
namespace zpksystems\phpzpk;

/**
 * Wrapper for ZPK SMS API, Send Request
 * 
 * @author Morgan Olufsen [morgan@zpk.systems]
 * @author ZPK Systems
 *
 * @license MIT
 *
 */

class zpkSmsSendRequest{

	private zpkApplication $application;
	private bool $concatenation = false;
	private string $encoding = '';
	private string $callback_url = '';
	private array $sms_list = [];
	private array $response = [];

	public function __construct( zpkApplication $application )
	{
		$this->application = $application;
	}

	private function setResponse(array $response){
		$this->response = $response;
	}

	public function getResponse():array{
		return $this->response;
	}

	public function getApplication():zpkApplication{
		return $this->application;
	}

	public function setConcatenation(bool $concat){
		$this->concatenation = $concat;
	}

	public function getConcatenation():bool{
		return $this->concatenation;
	}

	public function setEncoding(string $encoding){
		if( strtolower($encoding) == 'gsm7' ){
			$this->encoding = 'gsm7';
		}else if( strtolower($encoding) == 'ucs2' ){
			$this->encoding = 'ucs2';
		}else{
			throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,"Unsupported encoding. Valid encodings are: GSM7, UCS2");
		}
		$this->encoding = $encoding;
	}

	public function getEncoding():string{
		return $this->encoding;;
	}

	public function setCallbackURL(string $url){
		$this->callback_url = $url;
	}

	public function getCallbackURL():string{
		return $this->callback_url;
	}

	public function add( zpkSMS $sms ){
		$this->sms_list[] = $sms;
	}

	public function send(){

		$request = new zpkRequest($this->application);
		$request->setEndpoint('/api/sms/send');

		if( $this->callback_url ){
			$request->setParameter('callback_url',$this->callback_url);
		}

		if( $this->encoding ){
			$request->setParameter('encoding',$this->encoding);
		}

		if( $this->concatenation ){
			$request->setParameter('concatenate',true);
		}

		if( count($this->sms_list) == 0 ){
			throw new zpkException(EX_INSUFFICIENT_PARAMETERS,"A minimum of one sms is required on a sms sending request");
		}

		$messages = [];
		$used_references = [];
		foreach( $this->sms_list as $key=>$sms ){

			if( $sms->getReference() ){
				if( isset($used_references[$sms->getReference()]) ){
					throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,"Duplicated custom reference.".
						" On sms #$key");
				}
			}

			$used_references[$sms->getReference()] = true;
			$messages[] = $sms->toArray();
		}

		$request->setParameter('messages',$messages);

		$response = $request->run();
		$this->setResponse($response);
		return $this->getResponse();

	
	}

}


