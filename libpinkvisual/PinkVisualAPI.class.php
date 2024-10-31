<?php

// $$LICENSE$$

/**
 * The actual implementation of the PinkVisual API.
 * Handles requests to the PV API server as well as
 * pagination and errors.
 */
class PinkVisualAPI {
	
	///
	/// Constants
	///
	
	const VERSION = "1.0";
	
	// Sort
	/**
	 * Return data sorted by name.
	 */
	const SORT_NAME = "name";
	/**
	 * Return data sorted by date, newest first. Applicable only
	 * when requesting episodes.
	 */
	const SORT_DATE = "date";
	/**
	 * Return data sorted by rating. Applicable only when
	 * requesting episodes.
	 */
	const SORT_RATING = "rating";
	/**
	 * Return data sorted by relevance. Applicable only when searching.
	 */
	const SORT_RELEVANCE = "relevance";
	/**
	 * Return data with a random ordering.
	 */
	const SORT_RANDOM = "random";
	
	// Filters
	const FILTER_STRAIGHT = "straight";
	const FILTER_GAY = "gay";
	const FILTER_TRANNY = "tranny";
	const FILTER_MALE = "male";
	const FILTER_FEMALE = "female";
	
	// Format
	const FORMAT_JSON = "json";
	const FORMAT_XML = "xml";
	
	///
	/// API Requests
	///
	public function getSource($id = "all") {
		if(!$this->initialized()) {
			if(PinkVisualException::ret()) return PinkVisualException::value();
			else throw new PinkVisualException("PinkVisualAPI is uninitialized");
		}
		$url = self::apiUrl($this->mOptions,array($id == "all" ? "sources" : "source",$id));
		try {
			$res = self::rawRequest($url);
		} catch (Exception $ex) {
			if($ex->getCode() == PinkVisualException::NOT_FOUND) {
				if(PinkVisualException::ret()) return PinkVisualException::value();
				else throw $ex;
			}
			if(PinkVisualException::ret()) return PinkVisualException::value();
			else throw new PinkVisualException("Error fetching data: ".$ex->getMessagE(),$ex->getCode());
		}
		if($this->mOptions['unserialize']) {
			try {
				$res = $this->jsonParse($res,"sources",PinkVisualSource);
			} catch(Exception $ex) {
				if(PinkVisualException::ret()) return PinkVisualException::value();
				self::contentError($res);
			}
			if(count($res) == 0) {
				if(PinkVisualException::ret()) return PinkVisualException::value();
				else throw new PinkVisualException("No sources returned");
			}
			if(count($res) == 1) {
				return $res[0];
			}
			return $res;
		}
		return $res;
	}
	
	public function getEpisode($id = "all",$count=25,$offset=0) {
		if(!$this->initialized()) {
			if(PinkVisualException::ret()) return PinkVisualException::value();
			else throw new PinkVisualException("PinkVisualAPI is uninitialized");
		}
		$url = self::apiUrl($this->mOptions,array($id == "all" ? "episodes" : "episode",$id),array("start" => $offset, "limit" => $count));
		try {
			$res = self::rawRequest($url);
		} catch (Exception $ex) {
			if($ex->getCode() == PinkVisualException::NOT_FOUND) {
				if(PinkVisualException::ret()) return PinkVisualException::value();
				else throw $ex;
			}
			if(PinkVisualException::ret()) return PinkVisualException::value();
			else throw new PinkVisualException("Error fetching data: ".$ex->getMessagE(),$ex->getCode());
		}
		if($this->mOptions['unserialize']) {
			try {
				$res = $this->jsonParse($res,"episodes",PinkVisualEpisode);
			} catch(Exception $ex) {
				if(PinkVisualException::ret()) return PinkVisualException::value();
				self::contentError($res);
			}
			if(count($res) == 0) {
				if(PinkVisualException::ret()) return PinkVisualException::value();
				else throw new PinkVisualException("No episodes returned");
			}
			if(count($res) == 1) {
				return $res[0];
			}
			return $res;
		}
		return $res;
	}
	
	/**
	 * Fetches the episodes associated with a particular source ID
	 * @param $source The source ID to search for.
	 * @param $count The number of episodes to get
	 * @param $offset The record number to start retrieving at
	 * @return An array of {@link PinkVisualEpisode} objects.
	 */
	public function getEpisodesBySource($source,$count=25,$offset=0) {
		if(!$this->initialized()) {
			if(PinkVisualException::ret()) return PinkVisualException::value();
			else throw new PinkVisualException("PinkVisualAPI is uninitialized");
		}
		$url = self::apiUrl($this->mOptions,array("source",$source,"episodes"),array("start" => $offset, "limit" => $count));
			try {
			$res = self::rawRequest($url);
		} catch (Exception $ex) {
			if($ex->getCode() == PinkVisualException::NOT_FOUND) {
				if(PinkVisualException::ret()) return PinkVisualException::value();
				else throw $ex;
			}
			if(PinkVisualException::ret()) return PinkVisualException::value();
			else throw new PinkVisualException("Error fetching data: ".$ex->getMessagE(),$ex->getCode());
		}
		if($this->mOptions['unserialize']) {
			try {
				$res = $this->jsonParse($res,"episodes",PinkVisualEpisode);
			} catch(Exception $ex) {
				if(PinkVisualException::ret()) return PinkVisualException::value();
				self::contentError($res);
			}
			if(count($res) == 0) {
				if(PinkVisualException::ret()) return PinkVisualException::value();
				else throw new PinkVisualException("No sources returned");
			}
			if(count($res) == 1) {
				return $res[0];
			}
			return $res;
		}
		return $res;
	}
	
	/**
	 * Makes a raw API request and returns the raw results. IMPORTANT: This
	 * function does not respect the 'exceptions' parameter set in
	 * PinkVisualAPI::init, and *always* throws exceptions if errors are
	 * encountered.
	 * 
	 * If an exception is thrown, the code will correspond to 
	 * http://curl.haxx.se/libcurl/c/libcurl-errors.html
	 */
	public static function rawRequest($url) {
		//echo "Fetching $url".PHP_EOL;
		$curl = curl_init($url);
		if (defined('CURLOPT_PROTOCOLS')) {
			curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTP);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$version = curl_version();
		$vstring = "PinkVisualAPIClient/".self::VERSION;
		$vstring .= " (PHP ".phpversion()."; cURL/".$version['version']."; SSL ".$version['ssl_version'].")";
		curl_setopt($curl, CURLOPT_USERAGENT, $vstring);
		$response = curl_exec($curl);
		$code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
		//echo "Returned status of $code".PHP_EOL;
		if($response === false) {
			$err = curl_error($curl);
			$num = curl_errno($curl);
			curl_close($curl);
			throw new Exception("Curl Error: $err",$num);
		}
		curl_close($curl);
		if($code != 200) {
			throw new PinkVisualException("Error fetching data: $code",PinkVisualException::NOT_FOUND);
		}
		return $response;
	}
	
	/**
	 * Generates an API URL from options and segment hashes
	 * @param $options A populated options array
	 * @param $segments An array of URL segments
	 * @param $query A hash of keys and values for the query string
	 * @return A prepared URL which can be directly accessed to
	 * fetch the specified data.
	 */
	private static function apiUrl($options,$segments,$query=null) {
		$url = 'http://';
		$url .= $options['endpoint'];
		$url .= '/get/';
		$url .= $options['version'];
		$url .= '/';
		foreach($segments as $s) {
			$url .= $s;
			$url .= '/';
		}
		$url .= '?';
		$url .= 'key='.$options['key'];
		$url .= '&format='.$options['format'];
		if($query != null) {
			foreach($query as $key => $value) {
				$url .= "&$key=$value";
			}
		}
		// TODO: Additional query parameters
		return $url;
	}
	
	private static function contentError($data) {
		$data = json_decode($data);
		throw new PinkVisualException($data->error,PinkVisualException::CONTENT_ERROR);
	}
	
	///
	/// Defaults
	///
	private static $sDefaults = array(
		"format" => self::FORMAT_JSON,
		"endpoint" => "api.pinkvisual.com",
		"version" => "latest",
		"exceptions" => null,
		"unserialize" => true
	);
	
	///
	/// Members
	///
	private $mApiKey = null;
	private $mApiEndpoint = null;
	private $mOptions = null;
	
	///
	/// Initialization
	///
	/**
	 * Initializes an PinkVisualAPI object with teh specified options. Options are
	 * passed as an array of keys and values. A single option, <code>key</code> is
	 * required for every call to init. In addition, the following parameters are
	 * accepted:
	 * <ul>
	 * <li><code>format</code>: Whether to request API data in PinkVisualAPI::FORMAT_JSON 
	 * or PinkVisualAPI::FORMAT_XML format. In order to make use of the automatic 
	 * unserialization in the PinkVisualAPI, you must set this parameter to the default,
	 * PinkVisualAPI::FORMAT_JSON.</li>
	 * <li><code>endpoint</code>: The url used to make API calls against. Unless you have
	 * received an alternate URL from PinkVisaul, you shoudl leave this at the default
	 * setting of 'api.pinkvisual.com'</li>
	 * <li><code>version</code>: The PVAPI version code to send with API requests. Defaults
	 * to 'latest'.</li>
	 * <li><code>exceptions</code>: Wheter to throw exceptions, or return a value when
	 * errors occur. Setting this value to 'true' causes exceptions to be thrown. Setting
	 * to anything other than 'true' causes the value to be returned when errors occur.</li>
	 * <li><code>unserialize</code>: Boolean value to determine if the methods in PinkVisualAPI
	 * return unserialized objects or the raw API results. Defaults to 'true' (return objects).
	 * In the event that <code>format</code> is set to PinkVisualAPI::FORMAT_XML, then
	 * <code>unserialize</code> will be set to 'false'.</li>
	 * </ul>
	 */
	public static function init($options) {
		$pva = self::getInstance();
		$opts = array();
		foreach(self::$sDefaults as $key => $value) {
			if(!isset($options[$key])) {
				$opts[$key] = $value;
			} else {
				$opts[$key] = $options[$key];
			}
		}
		PinkVisualException::init($opts['exceptions']);
		if(!isset($options['key'])) {
			if(PinkVisualException::ret()) return PinkVisualException::value();
			else throw new PinkVisualException("No API Key Supplied");
		}
		$opts['key'] = $options['key'];
		if($opts['format'] == self::FORMAT_XML) {
			$opts['unserialize'] = false;
		}
		$pva->mOptions = $opts;
		return $pva;
	}
	/**
	 * Determines whetehr an instance of the PinkVisualAPI has been initialized or
	 * not. If it has *not* been initialized, it is required to call PinkVisualAPI::init
	 * before making any further calls on the API object.
	 */
	public function initialized() {
		return $this->mOptions !== null;
	}
	
	///
	/// Singleton Implementation
	///
	private static $instance;
	private function __construct() {}
	public static function getInstance() {
		if(!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
	
	///
	/// Parsers
	///
	private function jsonParse($data,$type,$elementClass) {
		$data = json_decode($data,true);
		if(!is_array($data)) {
			throw new PinkVisualException("JSONParser: Unable to parse non-array data");
		}
		if(!isset($data[$type])) {
			throw new PinkVisualException("JSONParser: Required key $type not found");
		}
		if(!is_array($data[$type])) {
			throw new PinkVisualException("JSONParser: Unable to parse non-array data in $type");
		}
		$res = array();
		foreach($data[$type] as $typ) {
			try {
				$res[] = call_user_func(array($elementClass,"parse"),$typ);
			} catch (Exception $ex) {
				throw new PinkVisualException("JSONParser: Error parsing data: ".$ex->getMessage(),$ex->getcode());
			}
		}
		// TODO: Verify number parsed equals "total"
		return $res;
	}
}