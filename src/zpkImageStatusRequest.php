<?php

namespace zpksystems\phpzpk;

class zpkImageStatusRequest{

	private zpkApplication $application;

	public function __construct( zpkApplication $application )
	{
		$this->application = $application;
	}

	public function requestStatus(string $unique_request_id){

		$req = new zpkRequest($this->application);
		$req->setEndpoint('/api/images/check-status');

		if( empty($unique_request_id) ){
			throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,"Unique request id cannot be empty");
		}

		$req->setParameter('unique_request_id',$unique_request_id);
		$data = $req->run();

		$imagesList = new zpkImagesList();

		foreach( $data['images'] as $image_elem ){

			$image = new zpkGeneratedImage();
			$image->setNum($image_elem['num']);
			$image->setCost($image_elem['cost']);
			$image->setStatus($image_elem['generation_status']);
			$image->setWaitTime($image_elem['wait_time']);

			if( isset($image_elem['image_url']) ){
				$image->setDownloadUrl($image_elem['image_url']);
			}

			if( $image_elem['generation_status'] == 'error' ){
				$image->setErrorCode($image_elem['error_id']);
			}

			$image->setLastCheck(time());
			$imagesList->add($image);
		}

		return $imagesList;

	}


}
