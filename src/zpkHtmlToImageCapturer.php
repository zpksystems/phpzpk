<?php

namespace zpksystems\phpzpk;

/**
 * ZPK Html To Image
 * Wrapper for ZPK Html To Image API
 * 
 * @author Morgan Olufsen [morgan@zpk.systems]
 * @author ZPK Systems
 *
 * @license MIT
 *
 */

class zpkHtmlToImageCapturer{

	private array $capture_data;
	private zpkApplication $application;

	public function __construct( zpkApplication $application, string $url){
		$this->application = $application;
		$this->capture_data = [
			'url' => $url,
			'capture_list'=>[],
			'resolution_width'=>1920,
			'resolution_height'=>1040,
			'capture_list'=>[]
		];
	}

	public function getUrl():string{
		return $this->capture_data['url'];
	}

	public function setResolution( int $width, int $height ){
		$this->capture_data['resolution_width'] = $width;
		$this->capture_data['resolution_height'] = $height;
	}

	public function injectJavascript( string $js_code ){
		$this->capture_data['injected_js'] = $js_code;
	}

	public function setUrl( string $url ):string{
		return $this->capture_data['url'] = $url;
	}

	public function addElement( $selector ){
		$elem = new zpkHtmlToImageElement($selector);
		$this->capture_data['capture_list'][] = $elem;
		return $elem;
	}

	public function capture(){

		$request = new zpkRequest($this->application);
		$request->setEndpoint('/api/html-to-image/capture');
		$request->setParameter('url',$this->capture_data['url']);

		if( isset($this->capture_data['resolution_width']) ){
			$request->setParameter('resolution_width',$this->capture_data['resolution_width']);
			$request->setParameter('resolution_height',$this->capture_data['resolution_height']);
		}

		if( isset($this->capture_data['injected_js']) ){
			$request->setParameter('injected_js',$this->capture_data['injected_js']);
		}

		$request->setParameter('capture_list',$this->capture_data['capture_list']);

		return $request->run();

	}

}
