<?php

use Elgg\BadRequestException;
use ColdTrick\TagTools\EditDefinition;

$tag = strtolower(elgg_extract('tag', $vars));
if (elgg_is_empty($tag)) {
	throw new BadRequestException();
}

$definition = TagDefinition::find($tag);
if ($definition instanceof TagDefinition) {
	// a definition already exists, so go edit that one
	forward(elgg_generate_entity_url($definition, 'edit'));
}

$title = elgg_echo('tag_tools:tag_definition:add:title', [$tag]);

$edit_form = new EditDefinition($tag);

$content = elgg_view_form('tag_definition/edit', [], $edit_form());

// lightbox create
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
