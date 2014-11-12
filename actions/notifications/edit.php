<?php

$tags = get_input("tags");
$user_guid = (int) get_input("user_guid");

if (empty($user_guid)) {
	register_error(elgg_echo("error:missing_data"));
	forward(REFERER);
}

$user = get_user($user_guid);
if (empty($user) || !elgg_instanceof($user, "user")) {
	register_error(elgg_echo("error:missing_data"));
	forward(REFERER);
}

if (!$user->canEdit()) {
	register_error(elgg_echo("noaccess"));
	forward(REFERER);
}

if (empty($tags)) {
	elgg_unset_plugin_user_setting("notifications", $user->getGUID(), "tags_tools");
} else {
	elgg_set_plugin_user_setting("notifications", json_encode($tags), $user->getGUID(), "tag_tools");
}

system_message(elgg_echo("save:success"));
forward(REFERER);
