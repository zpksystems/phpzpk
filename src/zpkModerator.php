<?php

namespace zpksystems\phpzpk;

/**
 * Wrapper for ZPK Moderation API
 * 
 * @author Morgan Olufsen [morgan@zpk.systems]
 * @author ZPK Systems
 *
 * @license MIT
 *
 */
class zpkModerator{

	private array $texts = [];
	private zpkApplication $application;

	public function __construct( zpkApplication $application )
	{
		$this->application = $application;
	}

	public function addText(array $data){

		if( !isset($data['text']) ){
			throw new zpkException(EX_INSUFFICIENT_PARAMETERS,"field text not specified");
		}

		foreach( array_keys($data) as $key ){
			if( !in_array($key,['text','message_id','source_id']) ){
				throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,"field '$key' is not valid, supported fields are: text, message_id and source_id");
			}
		}

		$this->texts[] = $data;

	}

	public function scan(){
		$request = new zpkRequest($this->application);
		$request->setEndpoint('/api/moderator/scan');
		$request->setParameter('messages',$this->texts);

		return $request->run();
	}

	public function getSourceStats( string $source_id ){
		$request = new zpkRequest($this->application);
		$request->setEndpoint('/api/moderator/source-info');
		$request->setParameter('source_id',$source_id);

		return $request->run();

	}



}


