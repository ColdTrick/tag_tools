<?php

use Elgg\Database\Select;

echo elgg_view_form('tag_tools/admin/followers', [
	'method' => 'GET',
	'disable_security' => true,
	'action' => 'admin/tags/followers',
]);

$offset = (int) get_input('offset');
$limit = max((int) get_input('limit'), 0, elgg_get_config('default_limit'));
$q = get_input('q');

$select = Select::fromTable('annotations');
$select->select('count(*) as total')
	->addSelect('value')
	->where($select->compare('name', '=', 'follow_tag', ELGG_VALUE_STRING))
	->groupBy('value');

$created_since = get_input('created_since');
$created_until = get_input('created_until');

if (!empty($created_since) || !empty($created_until)) {
	$select->andWhere($select->between('time_created', $created_since, $created_until, ELGG_VALUE_TIMESTAMP));
}

if (!empty($q)) {
	$select->andWhere($select->compare('value', 'like', "%{$q}%", ELGG_VALUE_STRING));
}

$counts = elgg()->db->getData($select);
$count = count($counts);
if ($count < 1) {
	echo elgg_echo('notfound');
	return;
}
$order = get_input('order', 'popular');

elgg_register_menu_item('title', [
	'name' => 'download',
	'text' => elgg_echo('download'),
	'icon' => 'download',
	'href' => elgg_generate_action_url('tag_tools/followers/export', [
		'created_since' => $created_since,
		'created_until' => $created_until,
		'order' => $order,
		'q' => $q,
	]),
	'link_class' => 'elgg-button elgg-button-action',
]);

$select->setMaxResults($limit)
	->setFirstResult($offset);

if ($order === 'popular') {
	$select->orderBy('total', 'desc');
}

$select->addOrderBy('value', 'asc');

$tags = elgg()->db->getData($select);

$row = [
	elgg_format_element('th', [], elgg_echo('tags')),
	elgg_format_element('th', ['style' => 'width: 1%;', 'class' => 'center'], elgg_echo('tag_tools:search:count')),
];
$header = elgg_format_element('thead', [], elgg_format_element('tr', [], implode(PHP_EOL, $row)));

$rows = [];
foreach ($tags as $tag) {
	$row = [];
	
	$row[] = elgg_format_element('td', [], $tag->value);
	$row[] = elgg_format_element('td', ['style' => 'width: 1%;', 'class' => 'center'], $tag->total);
	
	$rows[] = elgg_format_element('tr', [], implode(PHP_EOL, $row));
}
$body = elgg_format_element('tbody', [], implode(PHP_EOL, $rows));

echo elgg_format_element('table', ['class' => 'elgg-table'], $header . $body);

echo elgg_view('navigation/pagination', [
	'offset' => $offset,
	'limit' => $limit,
	'count' => $count,
]);
