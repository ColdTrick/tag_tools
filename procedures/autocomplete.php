<?php

elgg_gatekeeper();

$query = get_input("q");

$result = array();

if (!empty($query)) {
	
	$dbprefix = elgg_get_config("dbprefix");
	$tags_id = elgg_get_metastring_id("tags");
	
	$sql = "SELECT msv.string as string, count(*) as total";
	$sql .= " FROM " . $dbprefix . "metadata md";
	$sql .= " JOIN " . $dbprefix . "metastrings msv ON md.value_id = msv.id";
	$sql .= " WHERE md.name_id = " . $tags_id;
	$sql .= " AND msv.string LIKE '" . sanitise_string($query) . "%'";
	$sql .= " GROUP BY msv.string";
	$sql .= " ORDER BY total DESC";
	$sql .= " LIMIT 0, 20";
	
	$data = get_data($sql);
	if (!empty($data)) {
		foreach ($data as $row) {
			$result[] = $row->string;
		}
	}
	
}

$contents = json_encode($result);

header("Content-Type: application/json");
header("Content-Length: " . strlen($contents));

echo $contents;