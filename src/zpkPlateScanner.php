<?php

namespace zpksystems\phpzpk;

/**
 * Wrapper for ZPK PlateScanner API
 * 
 * @author Morgan Olufsen [morgan@zpk.systems]
 * @author ZPK Systems
 *
 * @license MIT
 *
 */
class zpkPlateScanner{

	private zpkApplication $application;
	private array $region_hint_codes = [];
	private string $image_file;
	private bool $strict_regions = false;

	public function __construct( zpkApplication $application )
	{
		$this->application = $application;
	}

	public function setRegionHints( array $region_codes ):void{
		$this->region_hint_codes = $region_codes;
	}

	public function enableStrictRegions():void{
		$this->strict_regions = true;
	}

	public function disableStrictRegions():void{
		$this->strict_regions = false;
	}

	public function setImageFile( string $filename ):void{

		// Check exists
		if( !file_exists($filename) ){
			throw new zpkException(EX_FILE_NOT_FOUND,"Unexisting file: $filename");
		}

		// Check mime
		$mime = mime_content_type($filename);
		$valid_mimes = ['image/png','image/jpeg','image/jpg'];
		if( !in_array($mime,$valid_mimes) ){
			throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,"Unsupported mime type for $filename : $mime. Suported types are image/png, image/jpeg, image/jpg");
		}

		// Set image
		$this->image_file = $filename;

	}

	/** Call the api to scan and return the data */
	public function scan():array
	{
		$request = new zpkRequest($this->application);
		$request->setEndpoint('/api/plate-scanner/image');

		if( !isset($this->image_file) ){
			throw new zpkException(EX_INSUFFICIENT_PARAMETERS,"No image to scan has been set.");
		}

		if( count($this->region_hint_codes) > 0 ){
			$request->setParameter('regions',$this->region_hint_codes);
		}

		if( $this->strict_regions ){
			$request->setParameter('strict_regions',true);
		}

		$request->attachFile('image', $this->image_file);

		return $request->run();
	
	}

	/** Call the api to return all regions */
	public function getAllRegions():array
	{
		$request = new zpkRequest($this->application);
		$request->setEndpoint('/api/plate-scanner/regions');
		return $request->run();
	}

}
