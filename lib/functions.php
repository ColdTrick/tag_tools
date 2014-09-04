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
