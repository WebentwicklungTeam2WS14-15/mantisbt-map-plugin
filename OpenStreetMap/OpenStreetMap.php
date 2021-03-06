<?php
/* File created by Peter Mösenthin */
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
		$this->description = 'Dieses Plugin erlaubt das Einfügen von Geodaten in Einträgen mit Hilfe von OpenLayers.';
		$this->page = '';

		$this->version = '0.9.8';
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
			'EVENT_REPORT_BUG_FORM' => 'event_report_bug_form',
			'EVENT_REPORT_BUG' => 'event_report_bug',
			'EVENT_UPDATE_BUG_FORM' => 'event_update_bug_form',
			'EVENT_UPDATE_BUG' => 'event_update_bug'
		);
		return array_merge( parent::hooks(), $t_hooks );
	}


	//************************************************************************************************
	//	RESOURCES
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
	//	BUG VIEWS
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
	//	BUG DATA
	//************************************************************************************************

	/**
	* This event allows plugins to perform post-processing of the bug data structure after being
	* reported from the user and being saved to the database. At this point, the issue ID is actually
	* known, and is passed as a second parameter.
	*
	* Parameters: <Complex> Bug data structure (see core/bug_api.php)
	*
	*
	*/
	function event_report_bug( $p_event, $p_bug_data_structure){
		$bug_id = $p_bug_data_structure ->id;
		$address = $_POST['hiddenaddress'];
		$lat = $_POST['newlatitude'];
		$lng = $_POST['newlongitude'];
		$this->writeAddress( $bug_id, $address );
		$this->writeGeo( $bug_id, $lat, $lng );
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
		$address = $_POST['address'];
		$lat = $_POST['newlatitude'];
		$lng = $_POST['newlongitude'];

		if( isset ($_POST['address']) ) {
			$this->updateAddress( $p_bug_id, $address );
		}

		if(isset ($_POST['newlatitude']) && isset ($_POST['newlongitude']) ){
			$this->updateGeo( $p_bug_id, $lat, $lng );
		}

		return $p_bug_data_structure;
	}


	//************************************************************************************************
	//	MAP
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
					.'<div id="address_input_container">'
						.'<div id="address_input_left_div">'
						.'<input id="map_address_input" type="text" name="address" size="105" value="'.$address.'">'
						.'<input type="hidden" id="hidden_input_latitude" name="newlatitude" value=""/>'
						.'<input type="hidden" id="hidden_input_longitude" name="newlongitude" value=""/>'
						.'<input type="hidden" id="hidden_input_address" name="hiddenaddress" value="">'
						.'</div>'
						.'<div id="address_input_right_div">'
							.'<div id="location_bounds_alert"></div>'
						.'</div>'
					.'</div>'
						.'<div id="osmp_map"></div>'
						.'<script type="text/javascript">'
						.'osmp.loadMap();'
						.'osmp.clearAndSetMarker('.$coords['lng'].','.$coords['lat'].');'
						.'osmp.setPositionClickHandler();'
						.'osmp.setAutocompleteInputElements();'
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
					.'<div id="address_input_container">'
						.'<div id="address_input_left_div">'
							.'<input id="map_address_input" type="text" name="address" size="105" value="'.$address.'">'
							.'<input type="hidden" id="hidden_input_latitude" name="newlatitude" value=""/>'
							.'<input type="hidden" id="hidden_input_longitude" name="newlongitude" value=""/>'
							.'<input type="hidden" id="hidden_input_address" name="hiddenaddress" value="">'
						.'</div>'
						.'<div id="address_input_right_div">'
							.'<div id="location_bounds_alert"></div>'
						.'</div>'
						.'</div>'
						.'<div id="osmp_map"></div>'
						.'<script type="text/javascript">'
						.'osmp.loadMap();'
						.'osmp.setMapPosition('.$lng.','.$lat.','.$zoom.');'
						.'osmp.setPositionClickHandler();'
						.'osmp.setAutocompleteInputElements();'
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
		$query_read_address =  'SELECT value FROM '.$table.' WHERE bug_id = '.$p_bug_id.' AND field_id = 3';
		$result_read_address = db_query( $query_read_address );
		$row_read_address = db_fetch_array( $result_read_address );
		$address = $row_read_address['value'];
		//$address = implode("",$row_read_address);
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
		$row_read_coords = db_fetch_array( $result_read_coords );
		$geo_text = $row_read_coords['value'];
		$geo_text_array = explode(" ", $geo_text );
		$lat = str_replace(",","",$geo_text_array[0]);
		$lng = str_replace(",","",$geo_text_array[1]);
		return array(
			'lat' => $lat,
			'lng' => $lng
		);
	}

	/**
	* Update address in database
	*/
	function updateAddress( $p_bug_id, $p_address ){
		$table = 'mantis_custom_field_string_table';
		$query_update_address =  'UPDATE '.$table.' SET value="'.$p_address.'" WHERE bug_id = '.$p_bug_id.' AND field_id = 3';
		$result_update_address = db_query( $query_update_address );
	}

	/**
	* Update geodata in database
	*/
	function updateGeo( $p_bug_id, $lat, $lng ){
		$table = 'mantis_custom_field_string_table';
		$geo_text = $lat.', '.$lng;
		$query_update_coords =  'UPDATE '.$table.' SET value="'.$geo_text.'" WHERE bug_id = '.$p_bug_id.' AND field_id = 2';
		$result_update_coords = db_query( $query_update_coords );
	}





	/**
	* Insert new address into database
	*/
	function writeAddress ( $p_bug_id, $p_address ){
		$table = 'mantis_custom_field_string_table';
		$query_write_address =  'INSERT INTO '.$table
			.' (field_id, bug_id, value)'
			.' VALUES (3, '.$p_bug_id.', "'.$p_address.'")'
			.' ON DUPLICATE KEY UPDATE value = "'.$p_address.'"';
		$result_write_address = db_query( $query_write_address );
	}

	/**
	* Insert new geodata into database
	*/
	function writeGeo ( $p_bug_id, $lat, $lng ){
		$table = 'mantis_custom_field_string_table';
		$geo_text = $lat.', '.$lng;
		$query_write_coords =  'INSERT INTO '.$table
		.' (field_id, bug_id, value)'
		.' VALUES (2, '.$p_bug_id.', "'.$geo_text.'")'
		.' ON DUPLICATE KEY UPDATE value = "'.$geo_text.'"';
		$result_write_coords = db_query( $query_write_coords );
	}

} // Close class
?>
