<?php

namespace zpksystems\phpzpk;

/**
 * Wrapper for ZPK VinOcr API
 * 
 * @author Morgan Olufsen [morgan@zpk.systems]
 * @author ZPK Systems
 *
 * @license MIT
 *
 */
class zpkVinOcr{

	private zpkApplication $application;
	private string $image_file;
	private string $image_url;
	private array $filters = [];
	private bool $include_considered_vins=false;

	function __construct( zpkApplication $application )
	{
		$this->application = $application;
		$this->image_file = false;
		$this->image_url = false;
	}

	private function getApplication():zpkApplication
	{
		return $this->application;
	}

	/** Sets the image to scan for possible vin numbers */
	public function setImageFile( string $filename ):void
	{
		if( !file_exists($filename) ){
			throw new zpkException(EX_FILE_NOT_FOUND,"File '$filename' not found.");
		}

		$this->image_file = $filename;
	}

	private function getImageFile(): String
	{
		return $this->image_file;
	}

	/**
	 * include considered vins
	 *
	 * The VinOcr API can consider múltiple VINs to be returned,
	 * calling this method makes the API return that VINS in
	 * a new response elemen 'considered vins'
	 *
	 * Each element on that array contains the vin number and
	 * the quality factor.
	 *
	 * */
	public function includeConsideredVins():void{
		$this->include_considered_vins = true;	
	}

	/** exclude a manufacturer
	 *
	 * penalizes all manufacturers matching $filter with -99 score
	 *
	 * @param string $filter a filter allowing * as wildcard
	 *
	 * */
	public function excludeManufacturer(string $filter){
		$this->addManufacturerFilter($filter,-99);
	}

	/** add a filter for a specific manufacturer
	 *
	 * adds a score alteration filter, if filter matches
	 * the vin manufacturer, score is applied on the final
	 * quality of the vin.
	 *
	 * used by api to decide between multiple posible vins
	 * 
	 * @param string $filter a filter allowing * as wildcard
	 * @param float $score a positive or negative float 
	 *
	 * */
	public function addManufacturerFilter(string $filter,float $score):void{
		$this->filters[] = [
			'zone'=>'*',
			'country'=>'*',
			'manufacturer'=>$filter,
			'brand'=>'*',
			'score'=>$score
		];
	}

	/** exclude a zone
	 *
	 * penalizes all zones matching $filter with -99 score
	 *
	 * @param string $filter a filter allowing * as wildcard
	 *
	 * */
	public function excludeZone($filter){
		$this->addZoneFilter($filter,-99);
	}

	/** add a filter for a specific zone
	 *
	 * adds a score alteration filter, if filter matches
	 * the vin zone, score is applied on the final
	 * quality of the vin.
	 *
	 * used by api to decide between multiple posible vins
	 * 
	 * @param string $filter a filter allowing * as wildcard
	 * @param float $score a positive or negative float 
	 *
	 * */
	public function addZoneFilter(string $filter,float $score):void{
		$this->filters[] = [
			'zone'=>$filter,
			'country'=>'*',
			'manufacturer'=>'*',
			'brand'=>'*',
			'score'=>$score
		];
	}

	/** exclude a country
	 *
	 * penalizes all countries matching $filter with -99 score
	 *
	 * @param string $filter a filter allowing * as wildcard
	 *
	 * */
	public function excludeCountry($country_filter){
		$this->addCountryFilter($country_filter,-99);
	}

	/** add a filter for a specific country
	 *
	 * adds a score alteration filter, if filter matches
	 * the vin country, score is applied on the final
	 * quality of the vin.
	 *
	 * used by api to decide between multiple posible vins
	 * 
	 * @param string $filter a filter allowing * as wildcard
	 * @param float $score a positive or negative float 
	 *
	 * */
	public function addCountryFilter(string $filter,float $score):void{
		$this->filters[] = [
			'zone'=>'*',
			'country'=>$filter,
			'manufacturer'=>'*',
			'brand'=>'*',
			'score'=>$score
		];
	}


	/** Exclude a brand
	 *
	 * Penalizes all brands matching $filter with -99 score
	 *
	 * @param string $filter A filter allowing * as wildcard
	 *
	 * */
	public function excludeBrand(string $filter):void{
		$this->addBrandFilter($filter,-99);
	}

	/** Add a filter for a specific brand
	 *
	 * Adds a score alteration filter, if filter matches
	 * one of the brands, score is applied on the final
	 * quality of the vins.
	 *
	 * Used by API to decide between multiple posible VINS
	 * 
	 * @param string $filter A filter allowing * as wildcard
	 * @param float $score A positive or negative float 
	 *
	 * */
	public function addBrandFilter(string $filter,float $score):void{
		$this->filters[] = [
			'zone'=>'*',
			'country'=>'*',
			'manufacturer'=>'*',
			'brand'=>$filter,
			'score'=>$score
		];
	}

	/** Call the api and return the data */
	public function scan():array
	{
		$request = new zpkRequest($this->getApplication());
		$request->setEndpoint('/api/vin-ocr/scan');

		// Check image specified
		if( !$this->image_file && !$this->image_url ){
			throw new zpkException(EX_INSUFFICIENT_PARAMETERS,"No image specified in image_url or image_file");
		}
		if( $this->image_file && $this->image_url ){
			throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,"Cannot use both parameters: image_file and image_url");
		}
		
		// Set image_file or image_url
		if( $this->image_file ){
			$request->attachFile('image_file',$this->getImageFile());
		}
		
		// Enable return of considered vins
		if( $this->include_considered_vins ){
			$request->setParameter('include_considered_vins',true);
		}

		// Add filters to the request
		if( count($this->filters)>0 ){
			$request->setParameter('score_filters',$this->filters);
		}

		// Run request
		$data = $request->run();
		return $data;

	}

	public function getBrands():array{
		$request = new zpkRequest($this->application);
		$request->setEndpoint('/api/vin-ocr/brands');
		return $request->run();
	}

	public function getCountries():array{
		$request = new zpkRequest($this->application);
		$request->setEndpoint('/api/vin-ocr/countries');
		return $request->run();
	}

	public function getManufacturers():array{
		$request = new zpkRequest($this->application);
		$request->setEndpoint('/api/vin-ocr/manufacturers');
		return $request->run();
	}

}
