<?php
/**
 * All plugin hook handlers are bundled here
 */

/**
 * Listen to the 'tags' page handler
 *
 * @param string $hook         the name of the hook
 * @param string $type         the type of the hook
 * @param array  $return_value the current return value
 * @param array  $params       supplied params
 *
 * @return array|bool
 */
function tag_tools_route_tags_hook($hook, $type, $return_value, $params) {
	
	if (empty($return_value) || !is_array($return_value)) {
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

/**
 * Listen to the 'notifications' page handler
 *
 * @param string $hook         the name of the hook
 * @param string $type         the type of the hook
 * @param array  $return_value the current return value
 * @param array  $params       supplied params
 *
 * @return array|bool
 */
function tag_tools_route_notifications_hook($hook, $type, $return_value, $params) {
	
	if (empty($return_value) || !is_array($return_value)) {
		return $return_value;
	}
	
	$page = elgg_extract("segments", $return_value);
	
	switch ($page[0]) {
		case "tag":
			
			$user = get_user_by_username($page[1]);
			if (empty($user)) {
				forward();
			} else {
				elgg_set_page_owner_guid($user->getgUID());
			}
			
			$return_value = false;
			
			include(dirname(dirname(__FILE__)) . "/pages/notifications.php");
			break;
	}
	
	return $return_value;
}

/**
 * Listen to the 'activity' page handler
 *
 * @param string $hook         the name of the hook
 * @param string $type         the type of the hook
 * @param array  $return_value the current return value
 * @param array  $params       supplied params
 *
 * @return array|bool
 */
function tag_tools_route_activity_hook($hook, $type, $return_value, $params) {
	
	if (empty($return_value) || !is_array($return_value)) {
		return $return_value;
	}
	
	$page = elgg_extract("segments", $return_value);
	
	switch ($page[0]) {
		case "tags":
			$return_value = false;
			
			include(dirname(dirname(__FILE__)) . "/pages/activity.php");
			break;
	}
	
	return $return_value;
}

/**
 * Add a filter tab on the activity page
 *
 * @param string         $hook         the name of the hook
 * @param string         $type         the type of the hook
 * @param ElggMenuItem[] $return_value the current return value
 * @param array          $params       supplied params
 *
 * @return ElggMenuItem[]
 */
function tag_tools_activity_filter_menu_hook_handler($hook, $type, $return_value, $params) {
	
	if (!elgg_in_context("activity") || !elgg_is_logged_in()) {
		return $return_value;
	}
	
	$tags = tag_tools_get_user_following_tags();
	if (empty($tags)) {
		return $return_value;
	}
	
	$selected = false;
	if (strpos(current_page_url(), elgg_normalize_url("activity/tags")) === 0) {
		$selected = true;
	}
	
	$item = ElggMenuItem::factory(array(
		"name" => "tags",
		"text" => elgg_echo("tags"),
		"href" => "activity/tags",
		"selected" => $selected,
		"priority" => 9999
	));
	
	$return_value[] = $item;
	
	return $return_value;
}
