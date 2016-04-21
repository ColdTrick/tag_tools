<?php

namespace ColdTrick\TagTools;

class Router {
	
	/**
	 * Listen to the 'tags' page handler
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param array  $return_value the current return value
	 * @param array  $params       supplied params
	 *
	 * @return void|false
	 */
	public static function tags($hook, $type, $return_value, $params) {
		
		if (empty($return_value) || !is_array($return_value)) {
			return;
		}
		
		$page = elgg_extract('segments', $return_value);
		
		switch ($page[0]) {
			case 'autocomplete':
				$return_value = false;
				
				include(elgg_get_plugins_path() . 'tag_tools/procedures/autocomplete.php');
				break;
		}
		
		if ($return_value === false) {
			return false;
		}
	}
}
