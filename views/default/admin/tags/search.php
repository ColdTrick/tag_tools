<?php

$tag_names = elgg_get_registered_tag_metadata_names();
if (empty($tag_names)) {
	echo elgg_echo('notfound');
	return;
}

$min_count = sanitize_int(get_input('min_count', 0), false);

$dbprefix = elgg_get_config('dbprefix');
$name_ids = implode(', ', elgg_get_metastring_map($tag_names));


$query = "SELECT * FROM (
		SELECT msv.string, COUNT(md.id) AS count
		FROM {$dbprefix}metadata md
		JOIN {$dbprefix}metastrings msv ON md.value_id = msv.id
		WHERE md.name_id IN ({$name_ids})
		AND msv.string != ''
		GROUP BY md.value_id
		) tags
		WHERE count > {$min_count}
		ORDER BY count DESC
";

$results = get_data($query);

if (empty($results)) {
	echo elgg_echo('notfound');
	return;
}

echo '<table class="elgg-table">';
echo '<tr><th>' . elgg_echo('tags') . '</th><th>' . elgg_echo('count') . '</th></tr>';
foreach ($results as $row) {
	echo "<tr><td>{$row->string}</td><td>{$row->count}</td></tr>";
}
echo '</table>';
