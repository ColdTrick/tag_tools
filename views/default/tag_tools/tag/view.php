<?php

elgg_admin_gatekeeper();

$tag = elgg_extract('tag', $vars);
if (elgg_is_empty($tag)) {
	register_error(elgg_echo('error:missing_data'));
	return;
}

$details = '';

// get stats
$stats = tag_tools_get_tag_stats($tag);
if (!empty($stats)) {
	
	$rows = [];
	
	// header
	$row = [
		elgg_format_element('th', [], elgg_echo('admin:statistics:numentities:type')),
		elgg_format_element('th', [], elgg_echo('admin:statistics:numentities:number')),
	];
	$rows[] = elgg_format_element('thead', [], elgg_format_element('tr', [], implode(PHP_EOL, $row)));
	
	foreach ($stats as $type_subtype => $count) {
		$row = [
			elgg_format_element('td', [], elgg_language_key_exists("item:{$type_subtype}") ? elgg_echo("item:{$type_subtype}") : $type_subtype),
			elgg_format_element('td', [], $count),
		];
		$rows[] = elgg_format_element('tr', [], implode(PHP_EOL, $row));
	}
	
	$table = elgg_format_element('table', ['class' => 'elgg-table'], implode(PHP_EOL, $rows));
	
	$details .= elgg_view_module('info', elgg_echo('admin:statistics:numentities'), $table);
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
	$details .= elgg_view_module('info', elgg_echo('tag_tools:search:rules'), $rules);
}

// return data
echo elgg_format_element('div', [], $details);
