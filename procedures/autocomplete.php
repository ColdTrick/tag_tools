<?php

elgg_gatekeeper();

$query = get_input('q');

$valid_tags = elgg_get_registered_tag_metadata_names();

$result = [];

if (!empty($query) && !empty($valid_tags)) {
	
	$dbprefix = elgg_get_config('dbprefix');

	foreach( $valid_tags as $tag_name )
		$tags_id[] = elgg_get_metastring_id( $tag_name );

	$sql = "SELECT msv.string as string, count(*) as total";
	$sql .= " FROM {$dbprefix}metadata md";
	$sql .= " JOIN {$dbprefix}metastrings msv ON md.value_id = msv.id";
	$sql .= " WHERE md.name_id IN (" . implode(',',$tags_id) . ")";
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

header('Content-Type: application/json');
header('Content-Length: ' . strlen($contents));

echo $contents;
