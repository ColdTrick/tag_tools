<?php

$tags = get_input('tags');
$user_guid = (int) get_input('user_guid');

if (empty($user_guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$user = get_user($user_guid);
if (empty($user)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

if (!$user->canEdit()) {
	return elgg_error_response(elgg_echo('noaccess'));
}

if (empty($tags)) {
	elgg_unset_plugin_user_setting('notifications', $user->guid, 'tags_tools');
} else {
	elgg_set_plugin_user_setting('notifications', json_encode($tags), $user->guid, 'tag_tools');
}

return elgg_ok_response('', elgg_echo('save:success'));
