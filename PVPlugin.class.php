<?php

class PVPlugin {
	public static function init_admin_menu() {
		add_options_page(
			__("PinkVisual API Integration Options"),
			__("PVAPI Options"),'manage_options',
			'pvapi_options',
			array("PVPlugin","render_admin_menu")
		);
	}
	public static function render_admin_menu() {
		require_once("pvapi_admin_menu.php");
	}
	public static function add_media_button($context) {
		$img = WP_PLUGIN_URL.'/pvapi/pvapi_button.png';
		$button = " %s";
		$button .= '<a href="media-upload.php?type=pvapi&amp;TB_iframe=true" class="thickbox" title="Add PinkVisual Media">';
		$button .= "<img src='$img' /></a>";
		return sprintf($context, $button);
	}
	public static function tab_filter($tabs) {
		$newtab = array('pvapi' => __('PinkVisual API','insertpvapi'));
		return array_merge($tabs,$newtab);
	}
	public static function render_tab() {
		wp_enqueue_style("media");
		return wp_iframe(array("PVPlugin","tab_form"), $errors );
	}
	public static function tab_form() {
		/// What options to pass to this?
		media_upload_header();
		require_once("pvapi_tab.php");
	}
	
	///
	/// AJAX Callbacks
	///
	public static function list_sources() {
		$pv = self::APIInstance();
		$sources = $pv->getSource();
		$ret = array();
		$ret['sources'] = array();
		foreach($sources as $sr) {
			$r = array();
			$r['id'] = $sr->getId();
			$r['name'] = $sr->getName();
			$r['gay'] = $sr->isGay() == true;
			$ret['sources'][] = $r;
		}
		$ret['count'] = count($ret['sources']);
		echo json_encode($ret);
		die();
	}
	
	///
	/// Helpers
	///
	public static function APIInstance() {
		$pvfold = dirname(__FILE__)."/libpinkvisual/";
		require_once($pvfold."PinkVisualApi.class.php");
		require_once($pvfold."PinkVisualException.class.php");
		require_once($pvfold."PinkVisualSource.class.php");
		require_once($pvfold."PinkVisualEpisode.class.php");
		$args = array();
		$args['key'] = get_option("pvapi_key",null);
		if($args['key'] == null) {
			$args['key'] = "6oUFwb5a9dRDp8mE";
		}
		$args['exceptions'] = true;
		return PinkVisualApi::init($args);
	}
	public static function style() {
		wp_enqueue_style("pvapi",plugins_url("pvapi/public_style.css",dirname(__FILE__)),false,uniqid());
	}
	public static function shortcode($attrs, $content=null, $code="") {
		// 2. General processing
		$ret = "<div class=\"pink_visual_data\">";
		if(!isset($attrs['num'])) {
			$attrs['num'] = get_option("pvapi_length",10);
		} else {
			$attrs['num'] = intval($attrs['num']);
		}
		$pv = self::APIInstance();
		$file = self::template_filename($attrs);
		$episode = null;
		$episodes = null;
		$source = null;
		if(isset($attrs['episode'])) {
			$episode = $pv->getEpisode($attrs['episode']);
			if(is_array($episode)) {
				$episode = $episode[0];
			}
		}
		else if(isset($attrs['source'])) {
			$source = $pv->getSource($attrs['source']);
			$episodes = $source->getEpisodes($attrs['num']);
		} else {
			$episodes = $pv->getEpisode("all",$attrs['num']);
		}
		ob_start();
		require($file);
		$ret .= ob_get_clean();
		$ret .= "</div>";
		return $ret;
	}
	private static function template_filename($args) {
		// Generate possible names into a stack
		$ext = ".php";
		$search = array();
		$name = "pvapi";
		array_push($search,$name);
		if(isset($args['source'])) {
			array_push($search,$name."-source");
			$name .= "-".$args['source'];
			array_push($search,$name);
		}
		if(isset($args['episode'])) {
			array_push($search,$name."-episode");
			$stack = array();
			foreach($search as $s) {
				array_push($stack,$name."-".$args['episode']);
			}
			foreach($stack as $s) {
				array_push($search,$s);
			}
		}
		if(isset($args['num'])) {
			$stack = array();
			foreach($search as $s) {
				array_push($stack,$name."-".$args['num']);
			}
			foreach($stack as $s) {
				array_push($search,$s);
			}
		}
		// Search for file in the template
		$template_base = get_stylesheet_directory();
		$plugin_base = dirname(__FILE__)."/templates";
		while( ($file = array_pop($search)) !== null) {
			$f = $template_base."/".$file.$ext;
			//echo "Checking $f".PHP_EOL;
			if(file_exists($f)) {
				return $f;
			}
			$f = $plugin_base."/".$file.$ext;
			//echo "Checking $f".PHP_EOL;
			if(file_exists($f)) {
				return $f;
			}
		}
		return null;
	}
	public static function join($raw_link) {
		$camp = get_option("pvapi_camp",null);
		if($camp == null) {
			$camp = "22118";
		}
		$revid = get_option("pvapi_tb",null);
		if($revid == null) {
			$revid = "66553";
		}
		if(strpos($raw_link,"revid") !== false) {
			$parts = explode("&revid",$raw_link);
			$raw_link = $parts[0];
		}
		$raw_link .= "&revid=$revid";
		$raw_link .= "&campaign=".$camp;
		$raw_link .= "&client=pvapi_wp";
		return $raw_link;
	}
}