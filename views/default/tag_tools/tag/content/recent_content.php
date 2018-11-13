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

$content = elgg_list_entities([
	'type_subtype_pairs' => get_registered_entity_types(),
	'limit' => 10,
	'metadata_name_value_pairs' => [
		'name' => elgg_get_registered_tag_metadata_names(),
		'value' => $tag,
		'case_sensitive' => false,
	],
	'pagination' => false,
]);

if (empty($content)) {
	return;
}

$more = elgg_view('output/url', [
	'text' => elgg_echo('tag_tools:tag:view:more'),
	'href' => elgg_generate_url('default:search', [
		'q' => $tag,
		'sort' => 'time_created',
	]),
	'is_trusted' => true,
]);

echo elgg_view_module('tag_content', elgg_echo('tag_tools:tag:content:content'), $content, ['menu' => $more]);