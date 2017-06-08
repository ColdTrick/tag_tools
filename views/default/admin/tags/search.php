<?php

$tag_names = tag_tools_rules_get_tag_names();

echo elgg_view_form('tag_tools/admin/tag_search', [
	'action' => 'admin/tags/search',
	'method' => 'GET',
	'disable_security' => true,
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

$order = get_input('order', 'count');
$order_string = 'count DESC, string ASC';
if ($order !== 'count') {
	$order_string = 'string ASC';
}

$limit = sanitize_int(get_input('limit'), false);
$limit = max((int) elgg_get_config('default_limit'), $limit, 50);
$offset = sanitize_int(get_input('offset', 0), false);

$sub_query = "SELECT msv.string, COUNT(md.id) AS count
	FROM {$dbprefix}metadata md
	JOIN {$dbprefix}metastrings msv ON md.value_id = msv.id
	{$type_subtype_join}
	WHERE md.name_id IN ({$name_ids})
	AND msv.string != ''
	{$likes_string}
	{$type_subtype_where}
	GROUP BY md.value_id
";

$count_query = "
	SELECT count(*) as total FROM (
		$sub_query
	) tags
	{$min_count_string}
	ORDER BY {$order_string}
";

$count_row = get_data_row($count_query);
$count = (int) $count_row->total;
if (empty($count)) {
	echo elgg_echo('notfound');
	return;
}

$query = "
	SELECT * FROM (
		$sub_query
	) tags
	{$min_count_string}
	ORDER BY {$order_string}
	LIMIT {$offset}, {$limit}
";

$results = get_data($query);

// load js
elgg_require_js('tag_tools/admin/search');

// build results
$rows = [];

// header
$row = [
	elgg_format_element('th', [], elgg_echo('tags')),
	elgg_format_element('th', ['style' => 'width: 1%;', 'class' => 'center'], elgg_echo('tag_tools:search:count')),
	elgg_format_element('th', ['colspan' => 2, 'class' => 'center'], elgg_echo('tag_tools:search:rules')),
];
$rows[] = elgg_format_element('tr', [], implode('', $row));

// list tags
foreach ($results as $result) {
	$tag_link_params = [
		'text' => $result->string,
		'href' => "#",
		'class' => 'tag-tools-search-result-tag',
		'data-tag' => $result->string,
		'is_trusted' => true,
	];
	
	$row = [
		elgg_format_element('td', [], elgg_view('output/url', $tag_link_params)),
		elgg_format_element('td', ['style' => 'width: 1%;', 'class' => 'center'], $result->count),
	];
	
	$rule = tag_tools_rules_get_rule($result->string);
	if (empty($rule)) {
		// create a rule (replace)
		$row[] = elgg_format_element('td', [
			'style' => 'width: 1%;',
			'class' => 'center',
		], elgg_view('output/url', [
			'text' => elgg_view_icon('random'),
			'href' => elgg_http_add_url_query_elements('tag_tools/rules/add', [
				'from_tag' => $result->string,
			]),
			'title' => elgg_echo('tag_tools:search:replace'),
			'class' => 'elgg-lightbox',
			'data-colorbox-opts' => json_encode([
				'width' => '600px',
			]),
		]));
		
		// create a rule (delete)
		$row[] = elgg_format_element('td', [
			'style' => 'width: 1%;',
			'class' => 'center',
		], elgg_view('output/url', [
			'text' => elgg_view_icon('delete'),
			'href' => elgg_http_add_url_query_elements('tag_tools/rules/add', [
				'from_tag' => $result->string,
				'tag_action' => 'delete',
			]),
			'title' => elgg_echo('delete'),
			'class' => 'elgg-lightbox',
			'data-colorbox-opts' => json_encode([
				'width' => '600px',
			]),
		]));
	} else {
		// edit rule, should not happen
		$tag_link_params['text'] = "{$result->string} - {$rule->getDisplayName()}";
		$row[0] = elgg_format_element('td', [], elgg_view('output/url', $tag_link_params));
		
		// edit/delete links
		$row[] = elgg_format_element('td', [
			'style' => 'width: 1%;',
			'class' => 'center',
		], elgg_view('page/components/column/tag_tools/rules/edit', [
			'item' => $rule,
		]));
		$row[] = elgg_format_element('td', [
			'style' => 'width: 1%;',
			'class' => 'center',
		], elgg_view('page/components/column/tag_tools/rules/delete', [
			'item' => $rule,
		]));
	}
	
	$rows[] = elgg_format_element('tr', [], implode('', $row));
}

// show result
echo elgg_format_element('table', [
	'class' => 'elgg-table',
	'id' => 'tag-tools-search-results',
], implode('', $rows));

// show pagination
echo elgg_view('navigation/pagination', [
	'count' => $count,
	'limit' => $limit,
	'offset' => $offset,
]);
