<?php

use Elgg\Database\MetadataTable;
use Elgg\Database\Select;

$tag_names = tag_tools_rules_get_tag_names();

echo elgg_view_form('tag_tools/admin/tag_search', [
	'action' => 'admin/tags/search',
	'method' => 'GET',
	'disable_security' => true,
]);

$select = Select::fromTable(MetadataTable::TABLE_NAME, 'md');
$select->select("{$select->getTableAlias()}.value")
	->addSelect("count({$select->getTableAlias()}.id) AS total")
	->where($select->compare("{$select->getTableAlias()}.name", 'in', $tag_names, ELGG_VALUE_STRING))
	->groupBy("{$select->getTableAlias()}.value");

$min_count = (int) get_input('min_count', 10);
if ($min_count > 0) {
	$select->having($select->compare('total', '>=', $min_count, ELGG_VALUE_INTEGER));
}

$query = get_input('q');
if (!empty($query)) {
	$select->andWhere($select->compare("{$select->getTableAlias()}.value", 'LIKE', "%{$query}%", ELGG_VALUE_STRING));
}

$type_subtype = get_input('type_subtype');
if (!empty($type_subtype)) {
	list($type, $subtype) = explode(':', $type_subtype);
	
	$alias = $select->joinEntitiesTable($select->getTableAlias(), 'entity_guid');
	
	$select->andWhere($select->compare("{$alias}.type", '=', $type, ELGG_VALUE_STRING));
	if (!elgg_is_empty($subtype)) {
		$select->andWhere($select->compare("{$alias}.subtype", '=', $subtype, ELGG_VALUE_STRING));
	}
}

$order = get_input('order');
switch ($order) {
	case 'alpha_a_z':
		$select->orderBy("{$select->getTableAlias()}.value", 'ASC');
		break;
	case 'alpha_z_a':
		$select->orderBy("{$select->getTableAlias()}.value", 'DESC');
		break;
	case 'count_0_9':
		$select->orderBy('total', 'ASC');
		$select->addOrderBy("{$select->getTableAlias()}.value", 'ASC');
		
		break;
	case 'count_9_0':
	default:
		$select->orderBy('total', 'DESC');
		$select->addOrderBy("{$select->getTableAlias()}.value", 'ASC');
		
		break;
}

$offset = (int) get_input('offset', 0);
$select->setFirstResult($offset);

$count = $select->execute()->rowCount();
if (empty($count)) {
	echo elgg_echo('notfound');
	return;
}

$limit = max((int) get_input('limit'), 50, elgg_get_config('default_limit'));
$select->setMaxResults($limit);

$results = $select->execute()->fetchAllAssociative();

elgg_import_esm('admin/tags/search');

// header
$row = [
	elgg_format_element('th', [], elgg_echo('tags')),
	elgg_format_element('th', ['style' => 'width: 1%;', 'class' => 'center'], elgg_echo('tag_tools:search:count')),
	elgg_format_element('th', ['colspan' => 2, 'class' => 'center'], elgg_echo('tag_tools:search:rules')),
];
$header = elgg_format_element('thead', [], elgg_format_element('tr', [], implode(PHP_EOL, $row)));

// build results
$rows = [];

// list tags
foreach ($results as $result) {
	$tag_link_params = [
		'text' => $result['value'],
		'href' => false,
		'class' => 'tag-tools-search-result-tag',
		'data-tag' => $result['value'],
		'is_trusted' => true,
	];
	
	$row = [
		elgg_format_element('td', [], elgg_view('output/url', $tag_link_params)),
		elgg_format_element('td', ['style' => 'width: 1%;', 'class' => 'center'], $result['total']),
	];
	
	$rule = tag_tools_rules_get_rule($result['value']);
	if (empty($rule)) {
		// create a rule (replace)
		$row[] = elgg_format_element('td', [
			'style' => 'width: 1%;',
			'class' => 'center',
		], elgg_view('output/url', [
			'icon' => 'random',
			'text' => false,
			'href' => elgg_http_add_url_query_elements('tag_tools/rules/add', [
				'from_tag' => $result['value'],
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
			'icon' => 'delete',
			'text' => false,
			'href' => elgg_http_add_url_query_elements('tag_tools/rules/add', [
				'from_tag' => $result['value'],
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

$body = elgg_format_element('tbody', [], elgg_format_element('tr', [], implode(PHP_EOL, $rows)));

// show result
echo elgg_format_element('table', [
	'class' => 'elgg-table',
	'id' => 'tag-tools-search-results',
], $header . $body);

// show pagination
echo elgg_view('navigation/pagination', [
	'count' => $count,
	'limit' => $limit,
	'offset' => $offset,
]);
