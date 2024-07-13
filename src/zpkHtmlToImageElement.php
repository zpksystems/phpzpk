<?php

namespace zpksystems\phpzpk;

/**
 * ZPK Html To Image API
 * class that represents a element to be captured
 * 
 * @author Morgan Olufsen [morgan@zpk.systems]
 * @author ZPK Systems [https://zpk.systems]
 *
 * @license MIT
 *
 */


class zpkHtmlToImageElement implements \JsonSerializable{


	public string $selector;
	public bool $force_transparency;
	public bool $force_parent_transparency;
	public string $output_format;
	public int $output_quality;

	public function __construct( string $selector ){
		$this->selector = $selector;
	}

	public function setSelector( string $selector ){
		$this->selector = $selector;
	}

	public function setOutputFormat( string $format ){

		$supported_output_formats = ['jpeg','png','webp'];
		if( !in_array($format,$supported_output_formats) ){
			throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,
				"Unsupported output format '$format', supported: ".implode(',',$supported_output_formats));
		}

		$this->output_format = $format;
	}

	public function setOutputQuality( int $quality ){
		if( !is_int($quality) ){
			throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,
				"Invalid quality, integer required");
		}

		if( $quality < 10 || $quality>100 ){
			throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,
				"Invalid quality, integer between 10-100 required. Provided: $quality");
		}


		$this->output_quality = $quality;
	}

	public function enableForceTransparency(){
		$this->force_transparency = true;
	}

	public function disableForceTransparency(){
		$this->force_transparency = false;
	}

	public function enableForceParentTransparency(){
		$this->force_transparency = true;
	}

	public function disableForceParentTransparency(){
		$this->force_transparency = false;
	}

	public function jsonSerialize(): mixed{
		$data = [];
		$data['selector'] = $this->selector;
		if( $this->force_transparency ){
			$data['force_transparency'] = true;
		}
		if( $this->force_parent_transparency ){
			$data['force_parent_transparency'] = true;
		}
		if( $this->output_format ){
			$data['output_format'] = $this->output_format;
		}
		if( $this->output_quality ){
			$data['output_quality'] = $this->output_quality;
		}
		return $data;
	}

}
