<?php

echo elgg_view('output/longtext', ['value' => elgg_echo('tag_tools:admin:tags:suggest:info')]);

$dbprefix = elgg_get_config('dbprefix');

$min_count = 5; // limit small tag conut

$tag_names = tag_tools_rules_get_tag_names();
$name_ids = implode(', ', elgg_get_metastring_map($tag_names));

$all_tags_query = "
		SELECT * FROM (
			SELECT msv.string, COUNT(md.id) AS count
			FROM {$dbprefix}metadata md
			JOIN {$dbprefix}metastrings msv ON md.value_id = msv.id
			WHERE md.name_id IN ({$name_ids})
			AND msv.string != ''
			AND CHAR_LENGTH(msv.string) > 2
			GROUP BY md.value_id
		) tags
		WHERE count > {$min_count}
		ORDER BY count DESC
";

$all_tags = get_data($all_tags_query);

$ignore_config = elgg_get_plugin_setting('ignored_suggestions', 'tag_tools');
if (empty($ignore_config)) {
	$ignore_config = [];
} else {
	$ignore_config = json_decode($ignore_config, true);
}

$configured_rule_tags = [];

$metadata_tags = elgg_get_metadata([
	'type' => 'object',
	'subtype' => \TagToolsRule::SUBTYPE,
	'limit' => false,
	'metadata_names' => ['from_tag', 'to_tag'],
]);

$configured_rule_tags = array_unique(metadata_array_to_values($metadata_tags));

$suggestions = [];

while ($current_tag = array_pop($all_tags)) {

	// filter out tags that already have a rule configured
	if (in_array($current_tag->string, $configured_rule_tags)) {
		continue;
	}
	
	// check if tags are ignored
	$ignore_tags = (array) elgg_extract($current_tag->string, $ignore_config, []);
	
	// check current tag for good suggestions
	foreach ($all_tags as $tag) {
		if (in_array($tag->string, $ignore_tags)) {
			continue;
		}
		
		$levenshtein = levenshtein($current_tag->string, $tag->string);
		
		$max_length = max(strlen($current_tag->string), strlen($tag->string));
		
		if (($levenshtein / $max_length) > (1/3)) {
			continue;
		}
		
		$suggestions[$current_tag->string][] = $tag->string;
	}
}

if (empty($suggestions)) {
	echo elgg_view_module('inline', elgg_echo('tag_tools:admin:tags:suggest:results:title'), elgg_echo('notfound'));
	return;
}

$suggestion_items = '';
foreach ($suggestions as $from_tag => $to_tags) {
	
	$to_items = '';
	foreach ($to_tags as $to_tag) {
		$to_items .= elgg_view('output/url', [
			'text' => $to_tag,
			'title' => elgg_echo('tag_tools:rules:add'),
			'href' => elgg_http_add_url_query_elements('tag_tools/rules/add', [
				'from_tag' => $from_tag,
				'to_tag' => $to_tag,
			]),
			'class' => 'elgg-button elgg-button-action elgg-lightbox',
			'data-colorbox-opts' => json_encode([
				'width' => '600px',
			]),
		]);
	}
	
	$to_items .= elgg_view('output/url', [
		'text' => elgg_echo('cancel'),
		'href' => elgg_http_add_url_query_elements('action/tag_tools/suggestions/ignore', [
			'from_tag' => $from_tag,
			'ignores' => $to_tags,
		]),
		'is_action' => true,
		'class' => 'elgg-button elgg-button-cancel hidden',
	]);

	$suggestion_item = '<div>' . elgg_echo('tag_tools:admin:tags:suggest:item', [$from_tag]) . '</div>';
	$suggestion_item .= elgg_format_element('div', ['class' => ['mll', 'mts']], $to_items);

	$suggestion_items .= elgg_format_element('li', ['class' => 'mbm'], $suggestion_item);
}

$body = elgg_format_element('ul', ['class' => 'tag-tools-admin-suggestions'], $suggestion_items);

echo elgg_view_module('inline', elgg_echo('tag_tools:admin:tags:suggest:results:title'), $body);
