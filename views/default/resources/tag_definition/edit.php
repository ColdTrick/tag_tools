<?php

use Elgg\EntityPermissionsException;
use ColdTrick\TagTools\EditDefinition;

$guid = (int) elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', TagDefinition::SUBTYPE);

/* @var $entity TagDefinition */
$entity = get_entity($guid);

if (!$entity->canEdit()) {
	throw new EntityPermissionsException();
}

$title = elgg_echo('tag_tools:tag_definition:edit:title', [$entity->getDisplayName()]);

$edit_form = new EditDefinition($entity);

$content = elgg_view_form('tag_definition/edit', [], $edit_form());

// lightbox edit
if (elgg_is_xhr()) {
	echo elgg_view_module('info', $title, $content);
	return;
}

// build page
$page_data = elgg_view_layout('content', [
	'title' => $title,
	'content' => $content,
	'filter' => false,
]);

// draw page
echo elgg_view_page($title, $page_data);
