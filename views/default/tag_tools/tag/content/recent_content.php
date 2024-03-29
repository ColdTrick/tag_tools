<?php
/**
 * Show users with a lot of content created with the given tag
 *
 * @uses $vars['tag'] the tag being shown
 */

$tag = elgg_extract('tag', $vars);
if (elgg_is_empty($tag)) {
	return;
}

$tag = strtolower($tag);

$types = elgg_entity_types_with_capability('searchable');
$comments_index = array_search('comment', $types['object']);
if ($comments_index !== false) {
	unset($types['object'][$comments_index]);
}

$content = elgg_list_entities([
	'query' => $tag,
	'type_subtype_pairs' => $types,
	'search_type' => 'entities',
	'limit' => 3,
	'pagination' => false,
	'sort_by' => [
		'property_type' => 'attribute',
		'property' => 'time_created',
		'direction' => 'desc',
	],
], 'elgg_search');

if (empty($content)) {
	return;
}

$more = elgg_view('output/url', [
	'text' => elgg_echo('tag_tools:tag:view:more'),
	'href' => elgg_generate_url('default:search', [
		'q' => $tag,
		'sort_by' => [
			'property_type' => 'attribute',
			'property' => 'time_created',
			'direction' => 'desc',
		],
	]),
	'is_trusted' => true,
]);

echo elgg_view('tag_tools/tag/content/item', [
	'title' => elgg_echo('tag_tools:tag:content:content'),
	'content' => $content,
	'more' => $more,
]);
