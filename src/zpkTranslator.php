<?php

namespace zpksystems\phpzpk;

/**
 * Wrapper for ZPK Translate API
 * 
 * @author Morgan Olufsen [morgan@zpk.systems]
 * @author ZPK Systems
 *
 * @license MIT
 *
 */
class zpkTranslator{

	private array $to_translate = [];
	private zpkApplication $application;

	public function __construct( zpkApplication $application )
	{
		$this->application = $application;
	}

	public function addTranslation(string $text, array $target_languages, string $source_language=null){
		$this->to_translate[] = [
			'text'=>$text,
			'target_languages'=>$target_languages,
			'source_language'=>$source_language
		];
	}

	private function getApplication():zpkApplication
	{
		return $this->application;
	}

	/** Call the api and return the data */
	public function translate():array
	{

		$request = new zpkRequest($this->getApplication());
		$request->setEndpoint('/api/translate/text');

		// Add translations to the request
		if( count($this->to_translate) == 0 ){
			throw new zpkException(EX_INSUFFICIENT_PARAMETERS,"You need to add at least one text to translate.");
		}

		// Add texts
		$request->setParameter('texts',$this->to_translate);

		// Run request
		$data = $request->run();
		return $data;

	}

	public function getLanguages():array{
		$request = new zpkRequest($this->getApplication());
		$request->setEndpoint('/api/translate/supported-languages');
		return $request->run();
	}


}
