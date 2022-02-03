<?php

use Elgg\Database\Select;

if (\Elgg\Application::isCli()) {
	// this is in case of PHPUnit action integration tests
	return elgg_error_response();
}

$created_since = get_input('created_since');
$created_until = get_input('created_until');
$order = get_input('order', 'popular');
$q = get_input('q');

$select = Select::fromTable('annotations');
$select->select('count(*) as total')
	->addSelect('value')
	->where($select->compare('name', '=', 'follow_tag', ELGG_VALUE_STRING))
	->groupBy('value');

if (!empty($created_since) || !empty($created_until)) {
	$select->andWhere($select->between('time_created', $created_since, $created_until, ELGG_VALUE_TIMESTAMP));
}

if (!empty($q)) {
	$select->andWhere($select->compare('value', 'like', "%{$q}%", ELGG_VALUE_STRING));
}

if ($order === 'popular') {
	$select->orderBy('total', 'desc');
}

$select->addOrderBy('value', 'asc');

$tags = elgg()->db->getData($select);

$file = elgg_get_temp_file();

$fh = $file->open('write');

fputcsv($fh, [
	elgg_echo('tags'),
	elgg_echo('tag_tools:search:count'),
], ';');

foreach ($tags as $tag) {
	fputcsv($fh, [
		$tag->value,
		$tag->total,
	], ';');
}

$file->close();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="tag-followers.csv"');
header('Content-Length: ' . $file->getSize());

echo $file->grabFile();

exit();
