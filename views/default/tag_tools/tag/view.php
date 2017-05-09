<?php

elgg_admin_gatekeeper();

$tag = elgg_extract('tag', $vars);
if (is_null($tag) || ($tag === '')) {
	register_error(elgg_echo('error:missing_data'));
	return '';
}

$details = '';

// get stats
$stats = tag_tools_get_tag_stats($tag);
if (!empty($stats)) {
	
	$rows = [];
	
	// header
	$row = [
		elgg_format_element('th', [], elgg_echo('widget:content_stats:type')),
		elgg_format_element('th', [], elgg_echo('widget:content_stats:number')),
	];
	$rows[] = elgg_format_element('tr', [], implode('', $row));
	
	foreach ($stats as $type_subtype => $count) {
		$row = [
			elgg_format_element('td', [], elgg_echo("item:{$type_subtype}")),
			elgg_format_element('td', [], $count),
		];
		$rows[] = elgg_format_element('tr', [], implode('', $row));
	}
	
	$table = elgg_format_element('table', ['class' => 'elgg-table'], implode('', $rows));
	
	$details .= elgg_view_module('inline', elgg_echo('admin:widget:content_stats'), $table);
}

// list rules
$tb = elgg()->table_columns;

$rules = elgg_list_entities_from_metadata([
	'type' => 'object',
	'subtype' => \TagToolsRule::SUBTYPE,
	'metadata_name_value_pairs' => [[
			'name' => 'from_tag',
			'value' => $tag,
		],
		[
			'name' => 'to_tag',
			'value' => $tag,
		],
	],
	'metadata_name_value_pairs_operator' => 'OR',
	'list_type' => 'table',
	'columns' => [
		$tb->getDisplayName(),
		$tb->fromView('tag_tools/rules/edit', elgg_echo('edit')),
		$tb->fromView('tag_tools/rules/delete', elgg_echo('delete')),
	],
]);
if (!empty($rules)) {
	$details .= elgg_view_module('inline', elgg_echo('tag_tools:search:rules'), $rules);
}

// return data
echo elgg_format_element('div', [], $details);
