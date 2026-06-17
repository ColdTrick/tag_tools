<?php

use ColdTrick\TagTools\EditDefinition;

$guid = (int) elgg_extract('guid', $vars);

/* @var $entity TagDefinition */
$entity = elgg_entity_gatekeeper($guid, 'object', TagDefinition::SUBTYPE, true);

$title = elgg_echo('tag_tools:tag_definition:edit:title', [$entity->getDisplayName()]);

$edit_form = new EditDefinition($entity);

$content = elgg_view_form('tag_definition/edit', ['sticky_enabled' => true], $edit_form());

// lightbox edit
if (elgg_is_xhr()) {
	echo elgg_view_module('info', $title, $content);
	return;
}

// draw page
echo elgg_view_page($title, [
	'content' => $content,
]);
