<?php

use Elgg\Database\Select;

elgg_gatekeeper();

$query = get_input('q');

$result = [];

if (!empty($query)) {
	$select = Select::fromTable('metadata');
	$select->select('value AS string')
		->addSelect('count(*) AS total')
		->where($select->compare('name', '=', 'tags', ELGG_VALUE_STRING))
		->andWhere($select->compare('value', 'LIKE', "%{$query}%", ELGG_VALUE_STRING))
		->groupBy('value')
		->orderBy('total', 'desc')
		->setMaxResults(20);
	
	$result = elgg()->db->getData($select, function($row) {
		return $row->string;
	});
}

$contents = json_encode($result);

header('Content-Type: application/json');
header('Content-Length: ' . strlen($contents));

echo $contents;
