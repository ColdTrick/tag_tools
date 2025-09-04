<?php

use ColdTrick\TagTools\EditDefinition;
use Elgg\Exceptions\Http\BadRequestException;

$tag = strtolower(elgg_extract('tag', $vars));
if (elgg_is_empty($tag)) {
	throw new BadRequestException();
}

$definition = TagDefinition::find($tag);
if ($definition instanceof TagDefinition) {
	// a definition already exists, so go edit that one
	$exception = new BadRequestException(elgg_echo('tag_tools:tag_definition:exists'));
	$exception->setRedirectUrl(elgg_generate_entity_url($definition, 'edit'));
	throw $exception;
}

$title = elgg_echo('tag_tools:tag_definition:add:title', [$tag]);

$edit_form = new EditDefinition($tag);

$content = elgg_view_form('tag_definition/edit', ['sticky_enabled' => true], $edit_form());

// lightbox create
if (elgg_is_xhr()) {
	echo elgg_view_module('info', $title, $content);
	return;
}

// draw page
echo elgg_view_page($title, [
	'content' => $content,
]);
