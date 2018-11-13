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

$old_bgcolor = $entity->bgcolor;
$old_textcolor = $entity->textcolor;

$new_bgcolor = get_input('bgcolor');
if ($new_bgcolor === '#000000') {
	if (!empty($old_bgcolor)) {
		unset($entity->bgcolor);
	}
} else {
	$entity->bgcolor = $new_bgcolor;
}

$new_textcolor = get_input('textcolor');
if ($new_textcolor === '#000000') {
	if (!empty($old_textcolor)) {
		unset($entity->textcolor);
	}
} else {
	$entity->textcolor = $new_textcolor;
}

if (!$entity->save()) {
	return elgg_error_response(elgg_echo('save:fail'));
}

if (($entity->bgcolor !== $old_bgcolor) || ($entity->textcolor !== $old_textcolor)) {
	elgg_invalidate_simplecache();
}

elgg_clear_sticky_form('tag_definition/edit');

return elgg_ok_response('', elgg_echo('save:success'), $entity->getURL());
