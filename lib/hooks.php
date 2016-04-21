<?php
/**
 * All plugin hook handlers are bundled here
 */

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
	
	if (!elgg_in_context('activity') || !elgg_is_logged_in()) {
		return $return_value;
	}
	
	$tags = tag_tools_get_user_following_tags();
	if (empty($tags)) {
		return $return_value;
	}
	
	$selected = false;
	if (strpos(current_page_url(), elgg_normalize_url('activity/tags')) === 0) {
		$selected = true;
	}
	
	$return_value[] = ElggMenuItem::factory([
		'name' => 'tags',
		'text' => elgg_echo('tags'),
		'href' => 'activity/tags',
		'selected' => $selected,
		'priority' => 9999,
	]);
	
	return $return_value;
}
