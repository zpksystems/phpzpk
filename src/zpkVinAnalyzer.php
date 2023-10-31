<?php

namespace zpksystems\phpzpk;

/**
 * Wrapper for ZPK VinAnalyzer API
 * 
 * @author Morgan Olufsen [morgan@zpk.systems]
 * @author ZPK Systems
 *
 * @license MIT
 *
 */
class zpkVinAnalyzer{

	private array $filters = [];
	private array $vins = [];
	private zpkApplication $application;

	public function __construct(zpkApplication $application){
		$this->application = $application;
	}

	/** Adds a vin to be analyzed */
	public function addVin(string $vin_code):void{
		$this->vins[] = $vin_code;
	}


	/** Adds multiple VINS to be analyzed */
	public function addVins( array $vin_codes ):void{
		foreach( $vin_codes as $vin_code ){
			$this->addVin($vin_code);
		}
	}



	/** 
	 * Add a filter
	 *
	 * penalizes all manufacturers matching $filter with -99 score
	 *
	 * @param array $filters An associative array defining the filter,
	 * this array must contain at least the 'score' key and one
	 * of the filter keys: zone,zountry,manufacturer,brand
	 *
	 * score is a float
	 * zone,country,manufacturer and brand are strings allowing '*'
	 * as wildcard.
	 *
	 * */
	public function addFilter(array $filter){

		$valid_keys = ['zone','country','manufacturer','brand','score'];
		$specified_fields=0;

		$filter_data = [
			'zone'=>'*',
			'country'=>'*',
			'manufacturer'=>'*',
			'brand'=>'*',
			'score'=>0
		];

		if( !isset($filter['score']) ){
			throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,
				"Array filter doesn't contain a required 'score' key.");
		}

		foreach( $filter as $key=>$val ){
			if( !in_array($key,$valid_keys) ){
				throw new zpkException(EX_INCOMPATIBLE_PARAMETERS,
					"$key is not valid in a filter array. Valid keys are: zone,country,manufacturer,brand,score");
			}
			if( $key != 'score' ){
				$specified_fields+=1;
			}
			$filter_data[$key] = $val;
		}

		if( $specified_fields == 0 ){
			throw new zpkException(EX_INSUFFICIENT_PARAMETERS,"0 fields specified on filter array. A minimum of one field is required");
		}

		$this->filters[] = $filter_data;
	
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
	public function excludeBrand($brand_filter){
		$this->addBrandFilter($brand_filter,-99);
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
	public function analyze():array{

		$request = new zpkRequest($this->application);
		$request->setEndpoint('/api/vin-analyzer/analyze');

		// Check at least one vin to send
		if( count($this->vins) == 0 ){
			throw new zpkException(EX_INSUFFICIENT_PARAMETERS,"VINs array is empty. You need to set at least one VIN");
		}

		$request->setParameter('vins',$this->vins);

		// Add filters to the request
		if( count($this->filters)>0 ){
			$request->setParameter('score_filters',$this->filters);
		}

		$data = $request->run();
		return $data;
	}

	public function getBrands():array{
		$request = new zpkRequest($this->application);
		$request->setEndpoint('/api/vin-analyzer/brands');
		return $request->run();
	}

	public function getCountries():array{
		$request = new zpkRequest($this->application);
		$request->setEndpoint('/api/vin-analyzer/countries');
		return $request->run();
	}

	public function getManufacturers():array{
		$request = new zpkRequest($this->application);
		$request->setEndpoint('/api/vin-analyzer/manufacturers');
		return $request->run();
	}

}
