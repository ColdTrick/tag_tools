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
		"title" => elgg_echo("follow tag on"),
		"is_action" => true,
		"item_class" => $on_class
	));
	
	$return[] = $item;
	
	$item = ElggMenuItem::factory(array(
		"name" => "follow_tag_off",
		"text" => elgg_view_icon("refresh-hover"),
		"href" => "action/tag_tools/follow_tag?tag=" . $encoded_tag,
		"title" => elgg_echo("follow tag off"),
		"is_action" => true,
		"item_class" => $off_class
	));
	
	$return[] = $item;
	
	return $return;
}


