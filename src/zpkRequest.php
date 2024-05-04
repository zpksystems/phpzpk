<?php

namespace zpksystems\phpzpk;

/** String with API host and schema */
define("ZPK_HOST_URI",'https://zpk.systems');

/**
 * Represents a request to a ZPK Systems API
 * 
 * @author Morgan Olufsen [morgan@zpk.systems]
 * @license MIT
 *
 */
class zpkRequest{

	private zpkApplication $application;
	private array $parameters = [];
	private array $files = [];
	private string $endpoint = ZPK_HOST_URI;

	/**
	 * API Request Constructor
	 *
	 * initializes a request to ZPK API
	 * credenciales especificadas en $application
	 *
	 * @param zpkApplication $application The zpkApplication to which the request will be sent.
	 *
	 * */
	function __construct(zpkApplication $application){
		$this->application = $application;
	}

	/**
	 * Returns the currently used zpkApplication on this request
	 */
	public function getApplication():zpkApplication{
		return $this->application;
	}


	/**
	 * Sets the target endpoint url
	 */
	public function setEndpoint(string $endpoint_url){
		$this->endpoint = ZPK_HOST_URI.$endpoint_url;
	}

	/**
	 * return current endpoint
	 * */
	public function getEndpoint():string{
		return $this->endpoint;
	}

	/**
	 * Set a parameter to be sent on this request
	 * */
	public function setParameter( string $name, mixed $value ){
		$this->parameters[$name] = $value;
	}

	/**
	 * Return an array with all parameters that has been set
	 * */
	public function getParameters(): Array
	{
		return $this->parameters;
	}

	/**
	 * Return an array of all files that has been set
	 * */
	public function getFiles(): Array
	{
		return $this->files;
	}

	/** 
	 * Attach a filte to the request
	 * */
	public function attachFile( string $parameter_name, string $filename )
	{
		if( !file_exists($filename) ){
			throw new zpkException(EX_FILE_NOT_FOUND,"File '$filename' not found.");
		}
		$this->files[$parameter_name] = $filename;
	}

	/** Flattens an arra
	 *
	 * Recursively scans all items on @param $array and creates a
	 * ready to use by curl array
	 *
	 **/
	private function flattenArray(array $array) {

		$iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array));
		$keys = array();
		foreach($iterator as $key => $value) {
			// Build long key name based on parent keys
			for ($i = $iterator-> getDepth() - 1; $i >= 0; $i--) {
				$key = $iterator-> getSubIterator($i)-> key().
					'.'.$key;
			}
			$keys[$key]=$value;
		}

		$ret = [];
		foreach( $keys as $key=>$value ){
			$parts = explode('.',$key);
			$first=array_shift($parts);
			$nkey = $first;
			foreach( $parts as $part ){
				$nkey.="[".$part."]";
			}
			$ret[$nkey] = $value;
		} 

		return $ret;
	}


	/** Build a Multipart Request to use with CURL
	 *
	 * Prepares an array of fields and files to be sent together
	 * in a curl request
	 *
	 *  */
	private function buildMultiPartRequest($ch, $boundary, $fields, $files) {
		$delimiter = '-------------' . $boundary;
		$data = '';

		$fields = $this->flattenArray($fields);

		foreach ($fields as $name => $content) {
			$data .= "--" . $delimiter . "\r\n"
				. 'Content-Disposition: form-data; name="' . $name . "\"\r\n\r\n"
				. $content . "\r\n";
		}
		foreach ($files as $name => $content) {
			$data .= "--" . $delimiter . "\r\n"
				. 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $name . '"' . "\r\n\r\n"
				. $content . "\r\n";
		}

		$data .= "--" . $delimiter . "--\r\n";

		curl_setopt_array($ch, [
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => [
				'Content-Type: multipart/form-data; boundary=' . $delimiter,
				'Content-Length: ' . strlen($data)
			],
			CURLOPT_POSTFIELDS => $data
		]);

		return $ch;
	}

	/**
	 * Returns an array of files with their content
	 * */
	private function getFilesWithContent(){
		$ret = [];
		foreach( $this->getFiles() as $name=>$filename ){
			$ret[$name] = file_get_contents($filename);
		}
		return $ret;
	}

	/**
	 * Send the request to ZPK and proccess response
	 * */
	public function run():array{

		// Set authorization parameters
		$this->setParameter('application_id',$this->getApplication()->getApplicationId());
		$this->setParameter('api_key',$this->getApplication()->getApiKey());

		// CURL INIT
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$this->getEndpoint());

		$ch = $this->buildMultiPartRequest($ch,uniqid(),
			$this->getParameters(),
			$this->getFilesWithContent()
		);

		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);

		// Run curl request and get data and info
		$data = curl_exec($ch);
		$info = curl_getinfo($ch);

		// Check for errors
		if( $info['http_code'] != 200 ){
			throw new zpkException(EX_NETWORK_ERROR,"Invalid response code from server: {$info['http_code']}, CURL error message: ".curl_error($ch));
		}

		// Expect only JSON from server
		if( $info['content_type'] != 'application/json' ){
			throw new zpkException(EX_NETWORK_ERROR,"Invalid contentType from server: {$info['content_type']}, expecting 'application/json'");
		}

		// Try to decode the json
		$json = json_decode($data,true);

		// Unable to decode json
		if( !is_array($json) ){
			throw new zpkException(EX_NETWORK_ERROR,"Invalid JSON format from server, JSON cannot be decoded");
		}

		// Check for server errors
		if( isset($json['errors']) && is_array($json['errors']) ){
			$ex = new zpkException(EX_RESPONSE_ERRORS,$json['errors'][0]['message']);
			$ex->setResponseErrors( $json['errors'] );
			throw $ex;
		}

		return $json;

	}

}
