<?php

elgg_make_sticky_form('tag_definition/edit');

$guid = (int) get_input('guid');
$entity = false;
if (!empty($guid)) {
	// edit
	$entity = get_entity($guid);
	if (!$entity instanceof TagDefinition || !$entity->canEdit()) {
		return elgg_error_response(elgg_echo('error:missing_data'));
	}
} else {
	// create
	$title = get_input('title');
	if (empty($title)) {
		return elgg_error_response(elgg_echo('error:missing_data'));
	}
	
	$entity = TagDefinition::factory([
		'title' => $title,
	]);
}

if (!$entity instanceof TagDefinition) {
	return elgg_error_response(elgg_echo('save:fail'));
}

$entity->description = get_input('description');
$entity->bgcolor = get_input('bgcolor');
$entity->textcolor = get_input('textcolor');

if (!$entity->save()) {
	return elgg_error_response(elgg_echo('save:fail'));
}

elgg_clear_sticky_form('tag_definition/edit');

return elgg_ok_response('', elgg_echo('save:success'), $entity->getURL());
