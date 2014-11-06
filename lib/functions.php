<?php
/**
 * All plugin functions are bundled here
 */

function tag_tools_is_user_following_tag($tag, $user_guid = 0, $reset_cache = false) {
	static $follow_cache = array();
	
	if (empty($tag)) {
		return false;
	}

	$user_guid = sanitise_int($user_guid, false);
	if (empty($user_guid)) {
		$user_guid = elgg_get_logged_in_user_guid();
	}

	if (!$reset_cache && array_key_exists($user_guid, $follow_cache)) {
		return in_array($tag, $follow_cache[$user_guid]);
	}
	
	$user = get_user($user_guid);
	if (empty($user)) {
		return false;
	}
	
	$options = array(
		"guid" => $user_guid,
		"annotation_name" => "follow_tag",
		"limit" => false
	);
	
	$ia = elgg_set_ignore_access(true);
	$annotations = elgg_get_annotations($options);
	elgg_set_ignore_access($ia);
	
	$follow_cache[$user_guid] = array();
	foreach ($annotations as $annotation) {
		$follow_cache[$user_guid][] = $annotation->value;
	}
	
	return in_array($tag, $follow_cache[$user_guid]);
}

function tag_tools_toggle_following_tag($tag, $user_guid = 0, $track = null) {
	
	$user_guid = sanitise_int($user_guid, false);
	if (empty($user_guid)) {
		$user_guid = elgg_get_logged_in_user_guid();
	}

	$user = get_user($user_guid);
	if (empty($user)) {
		return;
	}
	
	if ($track === null) {
		$track = !tag_tools_is_user_following_tag($tag, $user_guid);
	}

	$options = array(
		"guid" => $user_guid,
		"limit" => false,
		"annotation_name_value_pairs" => array(
		 	"name" => "follow_tag",
			"value" => $tag
		)
	);

	$ia = elgg_set_ignore_access(true);
	elgg_delete_annotations($options);
	elgg_set_ignore_access($ia);

	if ($track) {
		$user->annotate("follow_tag", $tag, ACCESS_PUBLIC);
	}
}

function tag_tools_notify_user($user_guid, $entity_guid, $tag) {
	static $notifications;
	
	$user_guid = sanitise_int($user_guid, false);
	if (empty($user_guid)) {
		return;
	}
	
	$entity_guid = sanitise_int($entity_guid, false);
	if (empty($entity_guid)) {
		return;
	}
	
	if (!isset($notifications)) {
		$notifications = array();
	}
	
	if (!isset($notifications[$entity_guid])) {
		$notifications[$entity_guid] = array();
	}
	
	if (in_array($user_guid, $notifications[$entity_guid])) {
		// the user already received a notification about this entity, don't do it double
		return;
	}
	
	$ia = elgg_set_ignore_access(true);
	$entity = get_entity($entity_guid);
	elgg_set_ignore_access($ia);
	
	$subject = elgg_echo("tag_tools:notification:follow:subject", array($tag));
	$message = elgg_echo("tag_tools:notification:follow:message", array($tag, $entity->getURL()));
	
	$params = array(
		"action" => "tag_tools",
		"object" => $entity
	);
	
	notify_user($user_guid, elgg_get_site_entity()->getGUID(), $subject, $message, $params);
	
	$notifications[$entity_guid][] = $user_guid;
}
