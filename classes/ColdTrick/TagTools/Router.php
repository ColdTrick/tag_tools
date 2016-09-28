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
		
		switch (elgg_extract(0, $page)) {
			case 'autocomplete':
				$return_value = false;
				
				include(elgg_get_plugins_path() . 'tag_tools/procedures/autocomplete.php');
				break;
		}
		
		if ($return_value === false) {
			return false;
		}
	}
	
	/**
	 * Listen to the 'activity' page handler
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param array  $return_value the current return value
	 * @param array  $params       supplied params
	 *
	 * @return void|false
	 */
	public static function activity($hook, $type, $return_value, $params) {
		
		if (empty($return_value) || !is_array($return_value)) {
			return;
		}
		
		$page = elgg_extract('segments', $return_value);
		
		switch (elgg_extract(0, $page)) {
			case 'tags':
				$return_value = false;
				
				include(elgg_get_plugins_path() . 'tag_tools/pages/activity.php');
				break;
		}
		
		if ($return_value === false) {
			return false;
		}
	}
	
	/**
	 * Listen to the 'notifications' page handler
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param array  $return_value the current return value
	 * @param array  $params       supplied params
	 *
	 * @return void|false
	 */
	public static function notifications($hook, $type, $return_value, $params) {
		
		if (empty($return_value) || !is_array($return_value)) {
			return;
		}
		
		$page = elgg_extract('segments', $return_value);
		
		switch (elgg_extract(0, $page)) {
			case 'tag':
				
				$user = get_user_by_username($page[1]);
				if (empty($user)) {
					forward();
				} else {
					elgg_set_page_owner_guid($user->getGUID());
				}
				
				$return_value = false;
				
				include(elgg_get_plugins_path() . 'tag_tools/pages/notifications.php');
				break;
		}
		
		if ($return_value === false) {
			return false;
		}
	}
}
