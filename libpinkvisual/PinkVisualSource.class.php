<?php

// $$LICENSE$$

/**
 * Represents a PinkVisual "Source" (a site or channel).
 */
class PinkVisualSource {

	private static $sReqKeys = array("id","name","gay");
	
	private $id;
	private $name;
	private $isGay;
	
	private $episodes;
	
	public function __construct($id,$name,$isGay=false) {
		$this->id = $id;
		$this->name = $name;
		$this->isGay = $isGay;
	}
	
	public static function parse($arr) {
		if(!is_array($arr)) {
			throw new PinkVisualException("PinkVisualSource: Unable to parse non-array data");
		}
		foreach(self::$sReqKeys as $key) {
			if(!isset($arr[$key])) {
				throw new PinkVisualException("PinkVisualSource: Missing required key $key");
			}
		}
		return new PinkVisualSource($arr['id'],$arr['name'],$arr['gay']);
	}
	
	///
	/// Dynamic Accessors
	///
	
	/**
	 * Fetches the array of {@link PinkVisualEpisode}s for this
	 * source.
	 */
	public function getEpisodes($count=25,$offset=0) {
		// TODO: Caching. This is complicated by the pagination, which makes it hard to track the acutal episodes retrieved.
		try {
			$eps = PinkVisualApi::getInstance()->getEpisodesBySource($this->getId(),$count,$offset);
			return $eps;
		} catch (Exception $ex) {
			if(PinkVisualException::ret()) return PinkVisualException::value();
			else throw new PinkVisualException("Error fetching episodes for ".$this->getId().": ".$ex->getMessagE(),$ex->getCode());
		}
	}
	
	///
	/// Accessors
	///
	
	/**
	 * Gets a human readable name for this source.
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Gets the API id of this source.
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Is this source tagged as 'gay'?
	 */
	public function isGay() {
		return $this->gay;
	}
	
	public function __toString() {
		return $this->getName() == null ? "NULL" : $this->getName();
	}
}