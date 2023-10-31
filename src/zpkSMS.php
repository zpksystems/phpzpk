<?php

namespace zpksystems\phpzpk;

/**
 * ZPK SMS
 * Wrapper for ZPK SMS API
 * 
 * @author Morgan Olufsen [morgan@zpk.systems]
 * @author ZPK Systems
 *
 * @license MIT
 *
 */

class zpkSMS{

	public string $phone = '';
	public string $message = '';
	public string $from = '';
	public string $send_at = '';
	public string $reference = '';

	public function __construct( string $phone_number )
	{
		$this->phone = $phone_number;
	}

	public function setMessage( string $message ){
		$this->message = $message;
	}

	public function setFrom( string $from ){
		$this->from = $from;
	}

	public function setSendAt( string $send_at ){
		$this->send_at = $send_at;
	}

	public function setReference( string $reference ){
		$this->reference = $reference;
	}

	public function getReference():string{
		return $this->reference;
	}

	public function toArray():array{

		$a = [
			'phone'=>$this->phone,
			'from'=>$this->from,
			'message'=>$this->message,
		];

		if( $this->send_at ){
			$a['send_at'] = $this->send_at;
		}

		if( $this->reference ){
			$a['reference'] = $this->reference;
		}

		return $a;
	}


}

