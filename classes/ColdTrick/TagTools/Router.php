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
	
	/**
	 * handle /tag_tools
	 *
	 * @param array $page url segments
	 *
	 * @return bool
	 */
	public static function tagTools($page) {
		
		switch (elgg_extract(0, $page)) {
			case 'rules':
				array_shift($page);
				return self::rules($page);
				break;
		}
		
		return false;
	}
	
	/**
	 * handle /tag_tools/rules
	 *
	 * @param array $page url segments
	 *
	 * @return bool
	 */
	protected static function rules($page) {
		
		elgg_admin_gatekeeper();
		
		switch (elgg_extract(0, $page)) {
			case 'add':
				echo elgg_view_resource('tag_tools/rules/add');
				return true;
				break;
			case 'edit':
				
				$vars = [
					'guid' => elgg_extract(1, $page),
				];
				
				echo elgg_view_resource('tag_tools/rules/edit', $vars);
				return true;
				break;
		}
		
		return false;
	}
}
