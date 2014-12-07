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

		$this->version = '0.9.5';
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
	* Display a map in the bug view
	*/
	function show_map_in_view( $p_bug_id ){
		$coords = $this->readGeo( $p_bug_id );
		$address = $this->readAddress( $p_bug_id );

		echo '<a name="mapview" id="mapview" /><br />'
				.'<div id="mapview_open">'
					.'<table class="width100" cellspacing="1">'
						.'<tr>'
							.'<td class="center" colspan="2">'
								.'<div id="map_address_display">'
									.'<div id="map_address_display_text">'
									.$address
									.'</div>'
								.'</div>'
								.'<div id="osmp_map"></div>'
								.'<script type="text/javascript">'
									.'osmp.loadMap();'
									.'osmp.setMapPosition('.$coords['lng'].','.$coords['lat'].',17);'
									.'osmp.clearAndSetMarker('.$coords['lng'].','.$coords['lat'].');'
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
		$coords = $this->readGeo( $p_bug_id );
		$address = $this->readAddress( $p_bug_id );

		echo '<tr class="row-1">'
					.'<td class="category">'
						.'Ortsdaten setzen'
					.'</td>'
					.'<td colspan="5">'
						.'<input id="map_address_input" type="text" name="address" size="105" maxlength="128" value="'.$address.'">'
						.'<div id="osmp_map"></div>'
						.'<script type="text/javascript">'
						.'osmp.loadMap();'
						.'osmp.clearAndSetMarker('.$coords['lng'].','.$coords['lat'].');'
						.'osmp.setPositionClickHandler();'
						.'osmp.setGoogleAutocomplete();'
						.'</script>'
					.'</td>'
				.'</tr>';
	}

	/**
	* Display a map in the bug report form
	*/
	function show_map_report_form( $p_project_id ){
		// Default position to center the map at the beginning
		// Dorsten (Germany)
		$lat = '51.65997382185341';
		$lng = '6.970621633869327';
		$zoom = '13';

		echo '<tr class="row-1">'
					.'<td class="category">'
						.'Ortsdaten setzen'
					.'</td>'
					.'<td colspan="5">'
						.'<input id="map_address_input" onkeydown="osmp.catchEnter(this);" type="text" name="address" size="105" maxlength="128" value="">'
						.'<div id="osmp_map"></div>'
						.'<script type="text/javascript">'
						.'osmp.loadMap();'
						.'osmp.setMapPosition('.$lng.','.$lat.','.$zoom.');'
						.'osmp.setPositionClickHandler();'
						.'osmp.setGoogleAutocomplete();'
						.'</script>'
					.'</td>'
				.'</tr>';
	}

	//************************************************************************************************
	//																		DATABASE OPERATION
	//************************************************************************************************


	/**
	* Read address from database
	* Return: Address
	*/
	function readAddress( $p_bug_id ){
		$table = 'mantis_custom_field_string_table';
		$query_read_address =  'SELECT value FROM '.$table.' WHERE bug_id = '.$p_bug_id.' AND field_id = 6';
		$result_read_address = db_query( $query_read_address );
		$row_read_address = db_fetch_array( $result_read_address );
		//$address = $row_read_address['value'];
		$address = implode("",$row_read_address);
		return $address;
	}

	/**
	* Read geodata from database
	* Return: <Array> Array of coordinates
	*/
	function readGeo( $p_bug_id ){
		$table = 'mantis_custom_field_string_table';
		$query_read_coords =  'SELECT value FROM '.$table.' WHERE bug_id = '.$p_bug_id.' AND field_id = 2';
		$result_read_coords = db_query( $query_read_coords );
Latitude: 51.66883576734051, Longitude: 6.956922317382792
		$row_read_coords = db_fetch_array( $result_read_coords );
		$geo_text = implode( "",$row_read_coords );
		$geo_text_array = explode(" ", $geo_text );
		$lat = $geo_text_array[1];
		$lng = $geo_text_array[3];
		return array(
			'lat' => $lat,
			'lng' => $lng
		);
	}

	/**
	* Update address in database
	*/
	function updateAddress( $p_bug_id, $p_address ){
		$query_update_address =  'UPDATE mantis_bug_text_table SET description='.$p_address.' WHERE id = '.$p_bug_id;
		$result_update_address = db_query( $query_update_address );
	}

	/**
	* Update geodata in database
	*/
	function updateGeo( $p_bug_id, $lat, $lng ){
		$query_update_coords =  'UPDATE mantis_bug_text_table SET description='.$lat.','.$lng.' WHERE id = '.$p_bug_id;
		$result_update_coords = db_query( $query_update_coords );
	}

	/**
	* Insert new address into database
	*/
	function writeAddress ( $p_project_id, $p_address ){
		$query_write_address =  'INSERT INTO mantis_bug_text_table (description) VALUES ('.$p_address.')';
		$result_write_address = db_query( $query_write_address );
	}

	/**
	* Insert new geodata into database
	*/
	function writeGeo ( $p_project_id, $lat, $lng ){
		$query_write_coords =  'INSERT INTO mantis_bug_text_table (description) VALUES ('.$lat.','.$lng.')';
		$result_write_coords = db_query( $query_write_coords );
	}

} // Close class
?>
