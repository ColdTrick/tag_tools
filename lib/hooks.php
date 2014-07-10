<?php
/**
 * All plugin hook handlers are bundled here
 */

function tag_tools_route_tags_hook($hook, $type, $return_value, $params) {
	
	if(empty($return_value) || !is_array($return_value)) {
		return $return_value;
	}
	
	$page = elgg_extract("segments", $return_value);
	
	switch ($page[0]) {
		case "autocomplete":
			$return_value = false;
			
			include(dirname(dirname(__FILE__)) . "/procedures/autocomplete.php");
			break;
	}
	
	return $return_value;
}