<?php

use Elgg\BadRequestException;

$tag = strtolower(elgg_extract('tag', $vars));
if (elgg_is_empty($tag)) {
	throw new BadRequestException();
}

// prepare page elements
$title = elgg_echo('tag_tools:tag:view:title', [$tag]);

$content_vars = [
	'tag' => $tag,
];

$definition = TagDefinition::find($tag);
if ($definition instanceof TagDefinition) {
	$content_vars['entity'] = $definition;
}

$content = elgg_view('tag_tools/tag/contents', $content_vars);

// build page
$page_data = elgg_view_layout('default', [
	'title' => $title,
	'content' => $content,
	'sidebar' => false,
	'tag' => $tag,
	'entity' => $definition,
]);

// draw page
echo elgg_view_page($title, $page_data);
