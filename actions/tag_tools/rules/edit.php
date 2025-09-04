<?php

$guid = (int) get_input('guid');
$from_tag = get_input('from_tag');
$tag_action = get_input('tag_action');

$save = (bool) get_input('save');

if (empty($from_tag) || empty($tag_action)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

if (!empty($guid)) {
	// edit
	$entity = get_entity($guid);
	if (!$entity instanceof \TagToolsRule || !$entity->canEdit()) {
		return elgg_error_response(elgg_echo('actionunauthorized'));
	}
} else {
	// new
	$entity = new \TagToolsRule();
	
	$entity->from_tag = $from_tag;
	$entity->tag_action = $tag_action;
	
	if ($save && !$entity->save()) {
		return elgg_error_response(elgg_echo('save:fail'));
	}
}

// save new data
if ($entity->tag_action === 'replace') {
	$entity->to_tag = get_input('to_tag');
}

$entity->notify_user = (bool) get_input('notify_user');

$entity->apply();

return elgg_ok_response('', elgg_echo('save:success'));
