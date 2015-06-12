<?php
/**
 * All plugin functions are bundled here
 */

/**
 * Get all the tags a user is following
 *
 * @param int  $user_guid   the user to get the tags for (default: current user)
 * @param bool $reset_cache reset the internal cache
 *
 * @return bool|array
 */
function tag_tools_get_user_following_tags($user_guid = 0, $reset_cache = false) {
	static $cache;
	
	if (!isset($cache)) {
		$cache = array();
	}
	
	$user_guid = sanitise_int($user_guid, false);
	if (empty($user_guid)) {
		$user_guid = elgg_get_logged_in_user_guid();
	}
	
	if (empty($user_guid)) {
		return false;
	}
	
	if (!isset($cache[$user_guid]) || $reset_cache) {
		$cache[$user_guid] = array();
		
		$options = array(
			"guid" => $user_guid,
			"annotation_name" => "follow_tag",
			"limit" => false
		);
		
		$ia = elgg_set_ignore_access(true);
		$annotations = elgg_get_annotations($options);
		elgg_set_ignore_access($ia);
		
		if (!empty($annotations)) {
			foreach ($annotations as $annotation) {
				$cache[$user_guid][] = $annotation->value;
			}
		}
	}
	
	return $cache[$user_guid];
}

/**
 * Check if a user is following a certain tag
 *
 * @param string $tag       the tag to check
 * @param int    $user_guid the user to check for (default: current user)
 *
 * @return bool
 */
function tag_tools_is_user_following_tag($tag, $user_guid = 0) {
	
	if (empty($tag)) {
		return false;
	}

	$user_guid = sanitise_int($user_guid, false);
	if (empty($user_guid)) {
		$user_guid = elgg_get_logged_in_user_guid();
	}

	if (empty($user_guid)) {
		return false;
	}
	
	$user_tags = tag_tools_get_user_following_tags($user_guid);
	if (empty($user_tags)) {
		return false;
	}
	
	return in_array($tag, $user_tags);
}

/**
 * Add or remove a tag from the follow list of a user
 *
 * @param string $tag       the tag to (un)follow
 * @param int    $user_guid the user to save the setting for
 * @param bool   $track     add/remove the tag
 *
 * @return void
 */
function tag_tools_toggle_following_tag($tag, $user_guid = 0, $track = null) {
	
	if (empty($tag)) {
		return;
	}
	
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

	// remove the tag from the follow list
	$options = array(
		"guid" => $user_guid,
		"limit" => false,
		"annotation_name" => "follow_tag",
		"annotation_value" => $tag
	);
	
	$ia = elgg_set_ignore_access(true);
	elgg_delete_annotations($options);
	tag_tools_remove_tag_from_notification_settings($tag, $user_guid);
	elgg_set_ignore_access($ia);

	// did the user want to follow the tag
	if ($track) {
		$user->annotate("follow_tag", $tag, ACCESS_PUBLIC);
	}
	
	// reset cache
	tag_tools_get_user_following_tags($user_guid, true);
}

/**
 * Notify a user about a new tag
 *
 * @param int    $user_guid   the user to notify
 * @param int    $entity_guid the entity to notify about
 * @param string $tag         the tag to notify about
 *
 * @return void
 */
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
	
	if (empty($tag)) {
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
	
	$user_tag_notification_settings = tag_tools_get_user_tag_notification_settings($tag, $user_guid);
	if (empty($user_tag_notification_settings)) {
		// the user is following the tag, but doesn't want notifications
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
	
	notify_user($user_guid, $entity->getOwnerGUID(), $subject, $message, $params, $user_tag_notification_settings);
	
	$notifications[$entity_guid][] = $user_guid;
}

/**
 * Remove the notification settings for a specific tag
 *
 * @param string $tag       the tag to remove the settings for
 * @param int    $user_guid the user to change the settings for (default: current user)
 *
 * @return bool
 */
function tag_tools_remove_tag_from_notification_settings($tag, $user_guid = 0) {
	
	if (empty($tag)) {
		return false;
	}
	
	$user_guid = sanitise_int($user_guid, false);
	if (empty($user_guid)) {
		$user_guid = elgg_get_logged_in_user_guid();
	}
	
	if (empty($user_guid)) {
		return false;
	}
	
	$settings = tag_tools_get_user_notification_settings($user_guid);
	if (empty($settings)) {
		// user has no notification settings, so all is good
		return true;
	}
	
	if (!isset($settings[$tag])) {
		// the user had no notification setting for this tag
		return true;
	}
	
	// remove the tag from the settings
	unset($settings[$tag]);
	elgg_set_plugin_user_setting("notifications", json_encode($settings), $user_guid, "tag_tools");
	tag_tools_get_user_notification_settings($user_guid, true);
	
	return true;
}

/**
 * Get the notification settings for a user
 *
 * @param int  $user_guid   the user to get the settings for
 * @param bool $reset_cache reset the internal cache
 *
 * @return bool|array
 */
function tag_tools_get_user_notification_settings($user_guid = 0, $reset_cache = false) {
	static $cache;
	
	if (!isset($cache)) {
		$cache = array();
	}
	
	$user_guid = sanitise_int($user_guid, false);
	if (empty($user_guid)) {
		$user_guid = elgg_get_logged_in_user_guid();
	}
	
	if (empty($user_guid)) {
		return false;
	}
	
	if (!isset($cache[$user_guid]) || $reset_cache) {
		$cache[$user_guid] = array();
		
		$setting = elgg_get_plugin_user_setting("notifications", $user_guid, "tag_tools");
		if (!empty($setting)) {
			$cache[$user_guid] = json_decode($setting, true);
		}
	}
	
	return $cache[$user_guid];
}

/**
 * Get the notification settings for a tag
 *
 * @param string $tag       the tag to get the settings for
 * @param int    $user_guid the user to get the settings for (default: current user)
 *
 * @return bool|array
 */
function tag_tools_get_user_tag_notification_settings($tag, $user_guid = 0) {
	
	if (empty($tag)) {
		return false;
	}
	
	$user_guid = sanitise_int($user_guid, false);
	if (empty($user_guid)) {
		$user_guid = elgg_get_logged_in_user_guid();
	}
	
	if (empty($user_guid)) {
		return false;
	}
	
	$settings = tag_tools_get_user_notification_settings($user_guid);
	if (empty($settings) || !isset($settings[$tag])) {
		// default notifications go the mail
		return array("email");
	}
	
	if (empty($settings[$tag])) {
		// the user disabled notifications for this tag
		return array();
	}
	
	// return the saved settings
	return $settings[$tag];
}

/**
 * Check if a user has selected the notification method for a tag
 *
 * @param string $tag       the tag to check
 * @param string $method    the notification method to check
 * @param int    $user_guid the user to check for (default: current user)
 *
 * @return bool
 */
function tag_tools_check_user_tag_notification_method($tag, $method, $user_guid = 0) {
	
	if (empty($tag)) {
		return false;
	}
	
	if (empty($method)) {
		return false;
	}
	
	$user_guid = sanitise_int($user_guid, false);
	if (empty($user_guid)) {
		$user_guid = elgg_get_logged_in_user_guid();
	}
	
	if (empty($user_guid)) {
		return false;
	}
	
	$tag_settings = tag_tools_get_user_tag_notification_settings($tag, $user_guid);
	if (empty($tag_settings)) {
		// user has disabled notifications for this tag
		return false;
	}
	
	// check if the user has selected the notification method
	return in_array($method, $tag_settings);
}

/**
 * Check is notifications for this entity are allowed
 *
 * @param int $entity_guid the entity guid
 *
 * @return bool
 */
function tag_tools_is_notification_entity($entity_guid) {
	
	$entity_guid = sanitise_int($entity_guid);
	$entity = get_entity($entity_guid);
	if (empty($entity)) {
		return false;
	}
	
	$type_subtypes = tag_tools_get_notification_type_subtypes();
	if (empty($type_subtypes) || !is_array($type_subtypes)) {
		return false;
	}
	
	$type = $entity->getType();
	if (empty($type) || !isset($type_subtypes[$type])) {
		return false;
	}
	
	$subtypes = elgg_extract($type, $type_subtypes);
	$subtype = $entity->getSubtype();
	if (empty($subtype)) {
		// user, group, site
		return true;
	}
	
	return in_array($subtype, $subtypes);
}

/**
 * Get the type/subtypes for which tag_tools notifications are allowed
 *
 * @return false|array
 */
function tag_tools_get_notification_type_subtypes() {
	static $result;
	
	if (!isset($result)) {
		$result = get_registered_entity_types();
		
		$result = trigger_plugin_hook('notification_type_subtype', 'tag_tools', $result, $result);
	}
	
	return $result;
}
