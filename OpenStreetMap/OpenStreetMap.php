<?php

/*
 * As of documentation this is not necessary
 * require_once( config_get( 'class_path' ) . 'MantisPlugin.class.php' );
 */

class OpenStreetMapPlugin extends MantisPlugin {

	/**
	*  A method that populates the plugin information and minimum requirements.
		*/
	function register() {

		// The localization does not work somehow.
		// This is just to see if it does at some point.
		//$this->name = plugin_lang_get( 'title' );

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
			'EVENT_LAYOUT_RESOURCES' => 'event_layout_resources',
			'EVENT_FILTER_FIELDS' => 'event_filter_fields',
			'EVENT_FILTER_COLUMNS' => 'event_filter_columns',
			'EVENT_REPORT_BUG_FORM' => 'event_report_bug_form',
			'EVENT_REPORT_BUG_DATA' => 'event_report_bug_data',
			'EVENT_UPDATE_BUG_FORM' => 'event_update_bug_form',
			'EVENT_UPDATE_BUG' => 'event_update_bug'
		);
		return array_merge( parent::hooks(), $t_hooks );
	}


	//************************************************************************************************
	//																		RESOURCES
	//************************************************************************************************

	/**
	* This event allows plugins to output HTML code from inside the <head> tag, for use with
	* CSS, Javascript, RSS, or any other similary resources. Note that this event is signaled after all
	* other CSS and Javascript resources are linked by MantisBT.
	*
	* Return: <String> HTML code to output.
	*
	*/
	function event_layout_resources(){
		$osmpjs = plugin_file( 'osmp.js' );
		$osmpcss = plugin_file( 'osmp_style.css' );
		$oljs = plugin_file( 'ol.js' );
		$olcss = plugin_file( 'ol.css' );
		$placesApiJs = 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&language=de';
		$placesApiCss = 'https://fonts.googleapis.com/css?family=Roboto:300,400,500';


		$t_html = '<script type="text/javascript" src="'.$oljs.'"></script>';
		$t_html .= '<link type="text/css" rel="stylesheet" href="'.$olcss.'">';
		$t_html .= '<link type="text/css" rel="stylesheet" href="'.$osmpcss.'">';
		$t_html .= '<link type="text/css" rel="stylesheet" href="'.$placesApiCss.'">';
		$t_html .= '<script type="text/javascript" src="'.$osmpjs.'"></script>';
		$t_html .= '<script type="text/javascript" src="'.$placesApiJs.'"></script>';
		return $t_html;
	}

	//************************************************************************************************
	//																		BUG VIEWS
	//************************************************************************************************

	/**
	* This event allows a plugin to either process information or display some data in the bug view page.
	* It is triggered after the bug notes have been displayed, but before the history log is shown.
	*
	* Any output here should be contained within its own <table> element.
	*
	* Parameters: <Integer> Bug ID
	*
	*/
	function event_view_bug_extra( $p_event, $p_bug_id ){
		$this->show_map_in_view( $p_bug_id );
	}

	/**
	* This event allows plugins to do processing or display form elements on the Update Issue
	* page. It is triggered immediately before the summary text field.
	*
	* Parameters: <Integer> Bug ID
	*
	*/
	function event_update_bug_form( $p_event, $p_bug_id ){
		$this->show_map_update_form( $p_bug_id );
	}

	/**
	* This event allows plugins to do processing or display form elements on the Report Issue
	* page. It is triggered immediately before the summary text field.
	* Any output here should be defining appropriate rows and columns for the surrounding
	* <table> elements.
	*
	* Parameters: <Integer> Project ID
	*
	*/
	function event_report_bug_form( $p_event, $p_project_id ){
		$this->show_map_report_form( $p_project_id );
	}

	//************************************************************************************************
	//																		BUG DATA
	//************************************************************************************************

	/**
	* This event allows plugins to perform pre-processing of the new bug data structure after
	* being reported from the user, but before the data is saved to the database. At this point, the
	* issue ID is not yet known, as the data has not yet been persisted.
	*
	* Parameters: <Complex> Bug data structure (see core/bug_api.php)
	*
	* Return: <Complex> Bug data structure
	*/
	function event_report_bug_data( $p_event, $p_bug_data_structure ){
		return $p_bug_data_structure;
	}

	/**
	* This event allows plugins to perform both pre- and post-processing of the updated bug
	* data structure after being modified by the user, but before being saved to the database.
	*
	* Parameters: <Complex> Bug data structure (see core/bug_api.php)
	* 						<Integer> Bug ID
	*
	* Return: <Complex> Bug data structure
	*/
	function event_update_bug( $p_event, $p_bug_data_structure, $p_bug_id ){
		return $p_bug_data_structure;
	}

	//************************************************************************************************
	//																		OTHER
	//************************************************************************************************



	/**
	* This event allows a plugin to register custom filter objects (based on the MantisFilter
	* class) that will allow the user to search for issues based on custom criteria or datasets. The
	* plugin must ensure that the filter class has been defined before returning the class name for
	* this event.
	*
	* Return: <Array> Array of class names for custom filters
	*
	*/
	function event_filter_fields(){
		//TODO check if is useful
	}

	/**
	* This event allows a plugin to register custom column objects (based on the MantisColumn
	* class) that will allow the user to view data for issues based on custom datasets. The plugin
	* must ensure that the column class has been defined before returning the class name for this
	* event.
	*
	* Return: <Array> Array of class names for custom columns
	*
	*/
	function event_filter_columns(){
		//TODO check if is useful
	}

	//************************************************************************************************
	//																		MAP
	//************************************************************************************************

	/**
	* Display a collapseable map in the bug view
	*/
	function show_map_in_view( $p_bug_id ){
		$t_lat = '51.65727969659906';
		$t_lng = '6.964556558664106';

		echo '<a name="mapview" id="mapview" /><br />'
				.'<div id="mapview_open">'
					.'<table class="width100" cellspacing="1">'
						.'<tr>'
							.'<td class="center" colspan="2">'
								.'<div id="map_address_display">'
									.'<div id="map_address_display_text">'
										.'<script type="text/javascript">'
											.'osmp.getAddress('.$t_lng.','.$t_lat.');'
										.'</script>'
									.'</div>'
								.'</div>'
								.'<div id="osmp_map"></div>'
								.'<script type="text/javascript">'
									.'osmp.loadMap();'
									.'osmp.setMapPosition('.$t_lng.','.$t_lat.',17);'
									.'osmp.showMarker('.$t_lng.','.$t_lat.');'
								.'</script>'
							.'</td>'
						.'</tr>'
					.'</table>'
				.'</div>';
	}

	/**
	* Display a map in the bug update form
	*/
	function show_map_update_form( $p_bug_id ){
		$t_lat = '51.65727969659906';
		$t_lng = '6.964556558664106';
		$t_zoom = '17';

		echo '<tr class="row-1">'
					.'<td class="category">'
						.'Ortsdaten setzen'
					.'</td>'
					.'<td colspan="5">'
						.'<input id="map_address_input" type="text" name="address" size="105" maxlength="128" value="">'
						.'<div id="osmp_map"></div>'
						.'<script type="text/javascript">'
						.'osmp.loadMap();'
						.'osmp.setMapPosition('.$t_lng.','.$t_lat.','.$t_zoom.');'
						.'osmp.setClickPositionHandler();'
						.'</script>'
					.'</td>'
				.'</tr>';
	}

	/**
	* Display a map in the bug report form
	*/
	function show_map_report_form( $p_project_id ){
		// Default position to center the map at the beginning
		$t_lat = '51.65997382185341';
		$t_lng = '6.970621633869327';
		$t_zoom = '13';

		echo '<tr class="row-1">'
					.'<td class="category">'
						.'Ortsdaten setzen'
					.'</td>'
					.'<td colspan="5">'
						.'<input id="map_address_input" onkeydown="osmp.catchEnter(this);" type="text" name="address" size="105" maxlength="128" value="">'
						.'<div id="osmp_map"></div>'
						.'<script type="text/javascript">'
						.'osmp.loadMap();'
						.'osmp.setMapPosition('.$t_lng.','.$t_lat.','.$t_zoom.');'
						.'osmp.setClickPositionHandler();'
						.'osmp.setGoogleAutocomplete();'
						.'</script>'
					.'</td>'
				.'</tr>';
	}

} // Close class
?>
