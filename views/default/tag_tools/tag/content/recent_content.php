<?php
/**
 * Show users with a lot of content created with the given tag
 *
 * @uses $vars['tag'] thje tag being shown
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

echo elgg_view_module('tag_content', elgg_echo('tag_tools:tag:content:content'), $content);