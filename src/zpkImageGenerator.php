<?php

namespace zpksystems\phpzpk;

/**
 * ZPK Image Generator
 * Wrapper for ZPK AI Image Generator
 * 
 * @author Morgan Olufsen [morgan@zpk.systems]
 * @author ZPK Systems
 *
 * @license MIT
 *
 */

class zpkImageGenerator{

	private zpkApplication $application;
	private string $prompt = '';
	private int $num_images = 1;

	private string $quality = '';
	private string $image_resolution = '';
	private string $style = '';
	private string $callback_url = '';
	private string $connection_type = 'fast';

	private int $last_request_time = 0;

	private bool $request_done = false;
	private string $unique_request_id = 'not_done';

	public zpkImagesList $imagesList;

	public function __construct( zpkApplication $application )
	{
		$this->application = $application;
		$this->imagesList = new zpkImagesList();
	}

	private function sendGenerationRequest(){

		$request = new zpkRequest($this->application);
		$request->setEndpoint('/api/images/generate');

		$request->setParameter('prompt',$this->prompt);
		$request->setParameter('num',$this->num_images);

		if( $this->quality ){
			$request->setParameter('quality',$this->quality);
		}

		if( $this->image_resolution ){
			$request->setParameter('resolution',$this->image_resolution);
		}

		if( $this->style ){
			$request->setParameter('style',$this->style);
		}

		if( $this->callback_url ){
			$request->setParameter('callback_url',$this->callback_url);
		}

		$request->setParameter('connection_type',$this->connection_type);
		$response = $request->run();
		$this->procResponse($response);

	}

	public function getUniqueRequestId(){
		return $this->unique_request_id;
	}

	private function procResponse( Array $response ){

		$this->unique_request_id = $response['unique_request_id'];

		foreach( $response['images'] as $image_elem ){
			$image = new zpkGeneratedImage();
			$image->setNum($image_elem['num']);
			$image->setCost($image_elem['cost']);
			$image->setStatus($image_elem['generation_status']);
			$image->setWaitTime($image_elem['wait_time']);
			$image->setLastCheck(time());
			$this->imagesList->add($image);
		}

		$this->last_request_time = time();
		$this->request_done = true;

	}

	public function getMaxWaitTime(){
		$waits = [];
		foreach( $this->imagesList as $image ){
			$waits[] = $image->getWaitTime();
		}
		return max($waits);
	}

	private function getTimePassedFromLastCheck():int{
		return time()-$this->last_request_time;
	}

	public function updateStatus(){

		$limit = $this->getMaxWaitTime();

		if( $limit > 40 ){
			$limit = floor($limit/2);
		}

		if( $this->getTimePassedFromLastCheck() > $limit ){
			$this->checkRequestStatusFromServer();
		}

	}

	private function allProcessed():bool{
		$count = 0;
		foreach( $this->imagesList as $image ){
			if( $image->getStatus() != 'generating' && $image->getStatus() != 'pending' ){
				$count++;
			}
		}
		return $count >= count($this->imagesList);
	}

	private function checkRequestStatusFromServer(){
		$statusRequest = new zpkImageStatusRequest( $this->application );
		$images = $statusRequest->requestStatus( $this->unique_request_id );
		$this->imagesList = $images;
		$this->last_request_time = time();
	}

	public function waitForGeneration(){
		if( $this->request_done == false ){
			throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,"Cannot wait for generation before a call to generate()");
		}

		while( $this->allProcessed() == false ){
			$this->updateStatus();
			sleep(1);
		}

	}

	public function generate(int $num_images){

		if( $num_images <1 || $num_images > 20 ){
			throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,"Cannot generate $num_images images, minimum is 1, max is 20");
		}

		$this->num_images = $num_images;
		$this->sendGenerationRequest();


	}

	public function setPrompt( string $prompt ){
		$this->prompt = $prompt;
	}

	public function setQuality( string $quality ){
		if( !in_array($quality,['standard','hq']) ){
			throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,"invalid image quality, accepted values are: standard, hq");
		}
		$this->quality = $quality;
	}

	public function setImageResolution( string $image_resolution ){
		if( !in_array($image_resolution,['1024x1024','1024x1792','1792x1024']) ){
			throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,"invalid image resolution, accepted resolutions are: 1024x1024, 1024x1792, 1792x1024");
		}
		$this->image_resolution = $image_resolution;
	}

	public function setStyle( string $style ){
		if( !in_array($style,['vivid','natural']) ){
			throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,"invalid image style, accepted styles are: vivid, natural");
		}
		$this->style = $style;
	}

	private function validateURL( string $url ){
		$valid = filter_var($url, FILTER_VALIDATE_URL);
		if( !$valid ){
			throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,"Invalid callback URL, not a valid URL: $url");
		}

		$url_components = parse_url( $url );
		if( !is_array($url_components) ){
			throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,"Invalid callback URL, cannot parse url for validation: $url");
		}
		if( !isset($url_components['scheme']) ){
			throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,"Invalid callback URL, has no scheme. Accepted schemes: https/http");
		}
		if( !in_array($url_components['scheme'],['http','https']) ){
			throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,"Invalid callback URL scheme '".$url_components['scheme']."'. Accepted schemes: https/http");
		}
	}

	public function setCallbackURL( string $url ){
		$this->validateURL( $url );
		$this->callback_url = $url;
	}

}
