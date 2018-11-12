<?php

use Elgg\Database\Select;

$tag_names = tag_tools_rules_get_tag_names();

echo elgg_view_form('tag_tools/admin/tag_search', [
	'action' => 'admin/tags/search',
	'method' => 'GET',
	'disable_security' => true,
]);

$select = Select::fromTable('metadata', 'md');
$select->select('md.value')
	->addSelect('count(md.id) AS total')
	->where($select->compare('md.name', 'in', $tag_names, ELGG_VALUE_STRING))
	->groupBy('md.value');

$min_count = (int) get_input('min_count', 10);
if ($min_count > 0) {
	$select->having($select->compare('total', '>', $min_count, ELGG_VALUE_INTEGER));
}

$query = get_input('q');
if (!empty($query)) {
	$select->andWhere($select->compare('md.value', 'LIKE', "%{$query}%"));
}

$type_subtype = get_input('type_subtype');
if (!empty($type_subtype)) {
	list($type, $subtype) = explode(':', $type_subtype);
	
	$alias = $select->joinEntitiesTable('md', 'entity_guid');
	
	$select->andWhere($select->compare("{$alias}.type", '=', $type, ELGG_VALUE_STRING));
	if (!elgg_is_empty($subtype)) {
		$select->andWhere($select->compare("{$alias}.subtype", '=', $subtype, ELGG_VALUE_STRING));
	}
}

$order = get_input('order', 'count');
if ($order === 'count') {
	$select->orderBy('total', 'DESC');
}
$select->addOrderBy('md.value', 'ASC');

$count_query = "SELECT count(*) as row_count
	FROM ({$select->getSQL()}) c
";

$count_res = elgg()->db->getDataRow($count_query, false, $select->getParameters());
if (empty($count_res) || empty($count_res->row_count)) {
	echo elgg_echo('notfound');
	return;
}

$count = (int) $count_res->row_count;

$offset = (int) get_input('offset', 0);
$limit = max((int) get_input('limit'), 50, elgg_get_config('default_limit'));

$select->setFirstResult($offset);
$select->setMaxResults($limit);

$results = $select->execute()->fetchAll();

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
$rows[] = elgg_format_element('thead', [], elgg_format_element('tr', [], implode(PHP_EOL, $row)));

// list tags
foreach ($results as $result) {
	$tag_link_params = [
		'text' => $result->value,
		'href' => false,
		'class' => 'tag-tools-search-result-tag',
		'data-tag' => $result->value,
		'is_trusted' => true,
	];
	
	$row = [
		elgg_format_element('td', [], elgg_view('output/url', $tag_link_params)),
		elgg_format_element('td', ['style' => 'width: 1%;', 'class' => 'center'], $result->total),
	];
	
	$rule = tag_tools_rules_get_rule($result->value);
	if (empty($rule)) {
		// create a rule (replace)
		$row[] = elgg_format_element('td', [
			'style' => 'width: 1%;',
			'class' => 'center',
		], elgg_view('output/url', [
			'text' => elgg_view_icon('random'),
			'href' => elgg_http_add_url_query_elements('tag_tools/rules/add', [
				'from_tag' => $result->value,
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
				'from_tag' => $result->value,
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
		$link = elgg_view('output/url', $tag_link_params);
		$link .= ' - ' . elgg_format_element('span', ['class' => 'elgg-quiet'], $rule->getDisplayName());
		
		$row[0] = elgg_format_element('td', [], $link);
		
		// edit/delete links
		$row[] = elgg_view('page/components/column/tag_tools/rules/edit', [
			'item' => $rule,
		]);
		$row[] = elgg_view('page/components/column/tag_tools/rules/delete', [
			'item' => $rule,
		]);
	}
	
	$rows[] = elgg_format_element('tr', [], implode(PHP_EOL, $row));
}

// show result
echo elgg_format_element('table', [
	'class' => 'elgg-table',
	'id' => 'tag-tools-search-results',
], implode(PHP_EOL, $rows));

// show pagination
echo elgg_view('navigation/pagination', [
	'count' => $count,
	'limit' => $limit,
	'offset' => $offset,
]);
