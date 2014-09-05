<?php
/**
 * All plugin functions are bundled here
 */

function tag_tools_is_user_following_tag($tag, $user_guid = null) {
	$result = false;
	if (empty($tag)) {
		return false;
	}

	if (empty($user_guid)) {
		$user_guid = elgg_get_logged_in_user_guid();
	}

	$user = get_user($user_guid);

	if ($user) {
		$ia = elgg_set_ignore_access(true);

		$options = array(
				'guid' => $user_guid,
				'annotation_name' => "follow_tag",
				'annotation_value' => $tag,
				'count' => true
		);

		if (elgg_get_annotations($options)) {
			$result = true;
		}
		elgg_set_ignore_access($ia);
	}

	return $result;
}

function tag_tools_toggle_following_tag($tag, $user_guid = null, $track = null) {
	if (empty($user_guid)) {
		$user_guid = elgg_get_logged_in_user_guid();
	}

	$user = get_user($user_guid);

	if ($user) {
		if ($track === null) {
			$track = !tag_tools_is_user_following_tag($tag, $user_guid);
		}

		$ia = elgg_set_ignore_access(true);

		$options = array(
				'guid' => $user_guid,
				'limit' => 0,
				'annotation_name' => "follow_tag",
				'annotation_value' => $tag
		);

		elgg_delete_annotations($options);
		elgg_set_ignore_access($ia);

		if ($track) {
			$user->annotate("follow_tag", $tag);
		}
	}
}

function tag_tools_notify_user($user_guid, $entity_guid, $tag) {
	static $notificiations;
	
	if (!isset($notifications)) {
		$notifications = array();
	}
	
	if (!array_key_exists($entity_guid, $notifications)) {
		$notifications[$entity_guid] = array();
	}
	
	if (!in_array($user_guid, $notifications[$entity_guid])) {
		$activity_url = elgg_normalize_url("activity/tags");
		system_message(elgg_echo("tag_tools:notification:follow:subject", array($tag)));
		system_message($tag);
		
		$subject = elgg_echo("tag_tools:notification:follow:subject", array($tag));
		$message = elgg_echo("tag_tools:notification:follow:message", array($tag, $activity_url));
		
		notify_user($user_guid, elgg_get_site_entity()->guid, $subject, $message, array());
		
		$notifications[$entity_guid][] = $user_guid;
	}
	
}