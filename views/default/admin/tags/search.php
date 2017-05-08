<?php

$tag_names = ['tags'];

echo elgg_view_form('tag_tools/admin/tag_search', [
	'action' => 'admin/tags/search',
]);

$dbprefix = elgg_get_config('dbprefix');
$name_ids = implode(', ', elgg_get_metastring_map($tag_names));

$wheres = [];

$min_count_string = '';

$min_count = sanitize_int(get_input('min_count', 10), false);
if ($min_count) {
	$min_count_string = "WHERE count >= {$min_count}";
}

$likes_string = '';
$query = sanitize_string(get_input('q'));
if (!empty($query)) {
	$likes_string = "AND msv.string LIKE '%{$query}%'";
}

$type_subtype_join = '';
$type_subtype_where = '';
	
$type_subtype = get_input('type_subtype');
if (!empty($type_subtype)) {
	list($type, $subtype) = explode(':', $type_subtype);
	
	$type_subtype_join = "JOIN {$dbprefix}entities e ON md.entity_guid = e.guid";
	
	$type = sanitize_string($type);
	$type_subtype_where = "AND e.type = '{$type}'";
	
	$subtype = sanitize_string($subtype);
	if (!empty($subtype)) {
		$subtype_id = get_subtype_id($type, $subtype);
		$type_subtype_where = "AND e.subtype = '{$subtype_id}'";
	}
}

$query = "SELECT * FROM (
		SELECT msv.string, COUNT(md.id) AS count
		FROM {$dbprefix}metadata md
		JOIN {$dbprefix}metastrings msv ON md.value_id = msv.id
		{$type_subtype_join}
		WHERE md.name_id IN ({$name_ids})
		AND msv.string != ''
		{$likes_string}
		{$type_subtype_where}
		GROUP BY md.value_id
		) tags
		{$min_count_string}
		ORDER BY count DESC
";

$results = get_data($query);

if (empty($results)) {
	echo elgg_echo('notfound');
	return;
}

$rows = '<tr><th>' . elgg_echo('tags') . '</th><th>' . elgg_echo('count') . '</th></tr>';
foreach ($results as $row) {
	$rows .= "<tr><td>{$row->string}</td><td>{$row->count}</td></tr>";
}
echo elgg_format_element('table', [
	'class' => 'elgg-table',
], $rows);
