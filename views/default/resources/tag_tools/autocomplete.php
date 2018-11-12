<?php

elgg_gatekeeper();

$query = get_input('q');

$result = [];

if (!empty($query)) {
	
	$dbprefix = elgg_get_config('dbprefix');
	
	$sql = "SELECT md.value as string, count(*) as total";
	$sql .= " FROM {$dbprefix}metadata md";
	$sql .= " WHERE md.name = 'tags'";
	$sql .= " AND md.value LIKE '" . sanitise_string($query) . "%'";
	$sql .= " GROUP BY md.value";
	$sql .= " ORDER BY total DESC";
	$sql .= " LIMIT 0, 20";
	
	$data = elgg()->db->getData($sql);
	if (!empty($data)) {
		foreach ($data as $row) {
			$result[] = $row->string;
		}
	}
}

$contents = json_encode($result);

header('Content-Type: application/json');
header('Content-Length: ' . strlen($contents));

echo $contents;