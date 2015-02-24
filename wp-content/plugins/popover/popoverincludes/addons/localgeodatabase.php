<?php
/*
Addon Name: Use Local Geo Database
Plugin URI:  http://premium.wpmudev.org/project/the-pop-over-plugin/
Description: Switches the geo checking from using an external API to using a local database
Author:      Barry (Incsub)
Author URI:  http://premium.wpmudev.org
Version:     1.0
*/

class popover_local_geo {

	var $db;

	function __construct() {

		global $wpdb;

		$this->db =& $wpdb;

		if( $this->check_tables_exist() ) {
			add_filter('popover_pre_incountry', array( &$this, 'incountry' ), 10, 3);
		}

	}

	function popover_local_geo() {
		$this->__construct();
	}

	function check_tables_exist() {

		if(!defined('POPOVER_GEOLOOKUPTABLE')) {
			define('POPOVER_GEOLOOKUPTABLE', 'countrylookupip');
		}

		if($this->db->get_var( $this->db->prepare( "SHOW TABLES LIKE %s", POPOVER_GEOLOOKUPTABLE ) ) == POPOVER_GEOLOOKUPTABLE) {
			return true;
		} else {
			return false;
		}

	}

	function incountry( $passed, $ip, $countrycode ) {

		$sql = $this->db->prepare( "SELECT * FROM " . POPOVER_GEOLOOKUPTABLE . " WHERE ipfrom <= INET_ATON(%s) AND ipto >= INET_ATON(%s)", $ip, $ip );

		$row = $this->db->get_row( $sql );

		if(!empty($row)) {
			if($row->ctry == 'ZZ') {
				$country = 'XX';
			} else {
				$country = $row->ctry;
			}
		} else {
			$country = 'XX';
		}

		if($country == 'XX') {
			if(PO_DEFAULT_COUNTRY !== false) {
				$country = PO_DEFAULT_COUNTRY;
			}
		}

		if($country == $countrycode) {
			return true;
		} else {
			return false;
		}

	}

}

$popover_local_geo = new popover_local_geo();