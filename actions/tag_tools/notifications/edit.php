<?php

$tags = get_input('tags');
$user_guid = (int) get_input('user_guid');

if (empty($user_guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$user = get_user($user_guid);
if (!$user instanceof \ElggUser || !$user->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

if (empty($tags)) {
	$user->removePluginSetting('tag_tools', 'notifications');
} else {
	$user->setPluginSetting('tag_tools', 'notifications', json_encode($tags));
}

return elgg_ok_response('', elgg_echo('save:success'));
