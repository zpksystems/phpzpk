<?php

namespace zpksystems\phpzpk;

/**
 * ZPK Generated image
 * Image data holder
 * 
 * @author Morgan Olufsen [morgan@zpk.systems]
 * @author ZPK Systems
 *
 * @license MIT
 *
 */

class zpkGeneratedImage{

	private int $num = -1;
	private float $cost = 0;
	private string $status = 'pending';
	private string $download_url = '';
	private string $error_code = '';
	private int $wait_time = 0;
	private int $last_check = 0;

	public function setLastCheck( int $timestamp ){
		$this->last_check = $timestamp;
	}

	public function setNum( int $num ){
		$this->num = $num;
	}

	public function getNum():int{
		return $this->num;
	}

	public function setCost( float $cost ){
		$this->cost = $cost;
	}

	public function setWaitTime( int $secs ){
		$this->wait_time = $secs;
	}

	public function getWaitTime():int{
		return $this->wait_time;
	}

	public function setStatus( string $status ){
		$this->status = $status;
	}

	public function getStatus():string{
		return $this->status;
	}

	public function getETA():int{
		$end = $this->last_check+$this->getWaitTime();
		return $end-time();
	}

	public function setDownloadUrl(string $url){
		$this->download_url = $url;
	}

	public function getDownloadUrl(){
		return $this->download_url;
	}

	public function isGenerated():bool{
		return $this->getStatus() == 'generated';
	}


	public function setErrorCode(string $code){
		$this->error_code = $code;
	}

	public function getErrorCode():string{
		return $this->error_code;
	}

}

