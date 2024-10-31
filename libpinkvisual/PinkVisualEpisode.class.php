<?php

// $$LICENSE$$

class PinkVisualEpisode {
	
	private static $sReqKeys = array("id","name","date","desc","rating","niches","stdimgs","joinlinks","trailers");
	
	
	private $id;
	private $name;
	private $date;
	private $description;
	private $rating;
	private $niches;
	private $tall_image;
	private $std_images;
	private $std_join;
	private $mobile_join;
	private $trailers;
	
	public function __construct($data) {
		//var_dump($data);
		$this->id = $data['id'];
		$this->name = $data['name'];
		$this->date = $data['date'];
		$this->description = $data['desc'];
		$this->tall_image = $data['stdimgs'][1];
		$this->std_images = array();
		for($i = 2; $i <= count($data['stdimgs']); ++$i) {
			$this->std_images[] = $data['stdimgs'][$i];
		}
		$this->std_join = $data['joinlinks']['pc'];
		$this->mobile_join = $data['joinlinks']['mobile'];
		$this->trailers = $data['trailers'];
	}

	public static function parse($arr) {
		if(!is_array($arr)) {
			throw new PinkVisualException("PinkVisualEpisode: Unable to parse non-array data");
		}
		foreach(self::$sReqKeys as $key) {
			if(!isset($arr[$key])) {
				throw new PinkVisualException("PinkVisualEpisode: Missing required key $key");
			}
		}
		return new PinkVisualEpisode($arr);
	}
	
	///
	/// Accessors
	///
	
	public function getId() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getDate() {
		return $this->date;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function getTallImage() {
		return $this->tall_image;
	}
	
	public function getJoin() {
		return $this->std_join;
	}
	
	public function getTrailer($type="high") {
		// TODO: Error parsing on $type
		return $this->trailers[$type];
	}
	
	/**
	 * Returns an excerpt of the description trimmed to the specified
	 * length. The excerpt is generated "nicely", and attempts to find
	 * the closest space before the length so that the excerpt does
	 * not break a word.
	 * @param The length of the excerpt
	 * @param What to append to the excerpt (defaults to "...")
	 * @return The excerpt concatenated to the elipses.
	 */
	public function getExcerpt($len = 60,$elipses="...") {
		if(strlen($this->description) <= $len) {
			return $this->description;
		}
		$pos = 0;
		while( ($p = strpos($this->description," ",$pos + 1)) !== false) {
			if($p > $len) {
				break;
			}
			$pos = $p;
		}
		return substr($this->description,0,$pos).$elipses;
	}
	
	public function __toString() {
		return '"'.$this->getName().'": '.$this->getExcerpt();
	}
}