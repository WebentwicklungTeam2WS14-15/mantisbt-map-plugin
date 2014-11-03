<?php

/**
 *  requires MantisPlugin.class.php
 */
require_once( config_get( 'class_path' ) . 'MantisPlugin.class.php' );

class OpenStreetMapPlugin extends MantisPlugin {

	/**
	 *  A method that populates the plugin information and minimum requirements.
	 */
	function register() {
		$this->name = 'OpenStreetMap Plugin';
		$this->description = 'Dieses Plugin erlaubt das Einfügen von Geodaten in Einträgen mit Hilfe von Leaflet und OpenStreetMap.';
		$this->page = '';

		$this->version = '0.0.1';
		$this->requires = array(
			'MantisCore' => '1.2.0',
		);

		$this->author = 'Peter Mösenthin';
		$this->contact = 'peter.moesenthin@gmail.com';
		$this->url = 'http://www.enterthecode.de';
	}

	/**
	 * Install plugin function.
	 */
	function install() {
		return true;
	}


	function hooks() {
		$t_hooks = array(
			'EVENT_VIEW_BUG_EXTRA'  => 'event_view_bug_extra',
			'EVENT_LAYOUT_RESOURCES' => 'event_layout_resources'
		);
		return array_merge(parent::hooks(), $t_hooks);
	}

	/**
	 * This event allows a plugin to either process information or display some data in the bug view page.
	 * It is triggered after the bug notes have been displayed, but before the history log is shown.
	 *
	 * Any output here should be contained within its own <table> element.
	 *
	 *
	 *
	 */
	function event_view_bug_extra($p_bug_id){
		echo '<h3>OpenStreetMap plugin test</h3>';
		echo '<div id="map" class="map" style="height:300px;"></div>';
		echo '<script src="plugins/OpenStreetMap/js/showmap.js" type="text/javascript"></script>';
	}

	function event_layout_resources(){
		$t_html = '<link rel="stylesheet" href="http://openlayers.org/en/v3.0.0/css/ol.css" type="text/css">';
		$t_html .= '<script src="http://openlayers.org/en/v3.0.0/build/ol.js" type="text/javascript"></script>';
		return $t_html;
	}

}
?>
