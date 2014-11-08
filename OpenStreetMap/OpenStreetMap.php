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
		$t_html = '<script type="text/javascript" src="http://openlayers.org/en/v3.0.0/build/ol.js"></script>';
		$t_html .= '<link type="text/css" rel="stylesheet" href="http://openlayers.org/en/v3.0.0/css/ol.css">';
		$t_html .= '<script type="text/javascript" src="/mantisbt/plugins/OpenStreetMap/js/osmp.js"></script>';
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
	function event_view_bug_extra( $p_bug_id ){
		$this->show_map_in_view();
	}

	/**
	* This event allows plugins to do processing or display form elements on the Update Issue
	* page. It is triggered immediately before the summary text field.
	*
	* Parameters: <Integer> Bug ID
	*
	*/
	function event_update_bug_form( $p_bug_id ){
		$this->show_map_in_form();
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
	function event_report_bug_form( $p_project_id ){
		$this->show_map_in_form();
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
	function event_report_bug_data( $bug_data_structure ){
		//TODO implement
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
	function event_update_bug( $bug_data_structure, $bug_id ){
		//TODO implement
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
	* Display a basic map
	*
	*/
	function show_map_in_view(){
		echo '<a name="mapview" id="mapview" /><br />';
		echo '<div id="mapview_open">';
			echo '<table class="width100" cellspacing="1">';
				echo '<tr>';
					echo '<td class="form-title" colspan="2">';
						echo '<a href="" onclick="ToggleDiv( \'mapview\' ); return false;">';
							echo '<img border="0" src="images/minus.png" alt="-" />';
						echo '</a>';
						echo '&#160;		Ortsdaten';
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td class="center" colspan="2">';
						echo '<div id="map" class="map" style="height:300px;"></div>';
						echo '<script type="text/javascript">';
							echo 'osmp.showMap();';
						echo '</script>';
					echo '</td>';
				echo '</tr>';
			echo '</table>';
		echo '</div>';

		echo '<div id="mapview_closed" class="hidden">';
			echo '<table class="width100" cellspacing="1">';
				echo '<tr>';
					echo '<td class="form-title" colspan="2">';
						echo '<a href="" onclick="ToggleDiv( \'mapview\' ); return false;">';
							echo '<img border="0" src="images/plus.png" alt="+" />';
						echo '</a>';
						echo '&#160;		Ortsdaten';
					echo '</td>';
				echo '</tr>';
			echo '</table>';
		echo '</div>';
	}


	function show_map_in_form(){
		echo '<tr class="row-1">';
		echo '<td class="category">';
		echo 'Ortsdaten setzen';
		echo '</td><td colspan="5">';
		echo '<div id="map" class="map" style="height:300px;"></div>';
		echo '<script type="text/javascript">';
		echo 'osmp.showMap();';
		echo '</script>';
		echo '</td></tr>';
	}

} // Close class
?>
