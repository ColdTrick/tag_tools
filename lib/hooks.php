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

function tag_tools_route_notifications_hook($hook, $type, $return_value, $params) {
	
	if(empty($return_value) || !is_array($return_value)) {
		return $return_value;
	}
	
	$page = elgg_extract("segments", $return_value);
	
	switch ($page[0]) {
		case "tag":
			
			$user = get_user_by_username($page[1]);
			if (empty($user)) {
				forward();
			} else {
				elgg_set_page_owner_guid($user->guid);
			}
			
			$return_value = false;
			
			include(dirname(dirname(__FILE__)) . "/pages/notifications.php");
			break;
	}
	
	return $return_value;
}

function tag_tools_route_activity_hook($hook, $type, $return_value, $params) {
	if(empty($return_value) || !is_array($return_value)) {
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

function tag_tools_follow_tag_menu_register_hook($hook, $type, $return_value, $params) {
	$return = array();
	
	$encoded_tag = htmlspecialchars($params["tag"], ENT_QUOTES, "UTF-8", false);
	
	$on_class = "";
	$off_class = "hidden";
	if (tag_tools_is_user_following_tag($encoded_tag)) {
		$on_class = "hidden";
		$off_class = "";
	}
	
	$item = ElggMenuItem::factory(array(
		"name" => "follow_tag_on",
		"text" => elgg_view_icon("refresh"),
		"href" => "action/tag_tools/follow_tag?tag=" . $encoded_tag,
		"title" => elgg_echo("tag_tools:follow_tag:menu:on"),
		"is_action" => true,
		"item_class" => $on_class
	));
	
	$return[] = $item;
	
	$item = ElggMenuItem::factory(array(
		"name" => "follow_tag_off",
		"text" => elgg_view_icon("refresh-hover"),
		"href" => "action/tag_tools/follow_tag?tag=" . $encoded_tag,
		"title" => elgg_echo("tag_tools:follow_tag:menu:off"),
		"is_action" => true,
		"item_class" => $off_class
	));
	
	$return[] = $item;
	
	return $return;
}

function tag_tools_activity_filter_menu_hook_handler($hook, $type, $return_value, $params) {
	if (elgg_in_context("activity") && elgg_is_logged_in()) {
		
		$user = elgg_get_logged_in_user_entity();
		
		$annotation_options = array(
			"guid" => $user->guid,
			"count" => true,
			"annotation_name" => "follow_tag"
		);
		
		$annotations = elgg_get_annotations($annotation_options);
		if ($annotations) {
		
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
		}
	}
	
	return $return_value;
}

