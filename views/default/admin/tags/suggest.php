<?php

use Elgg\Database\Select;

echo elgg_view('output/longtext', ['value' => elgg_echo('tag_tools:admin:tags:suggest:info')]);

$tag_names = tag_tools_rules_get_tag_names();

$select = Select::fromTable('metadata', 'md');
$select->select('md.value')
	->addSelect('count(md.id) AS total')
	->where($select->compare('md.name', 'in', $tag_names, ELGG_VALUE_STRING))
	->groupBy('md.value')
	->having($select->compare('CHAR_LENGTH(md.value)', '>', 2));

// limit small tag conut
$min_count = (int) get_input('min_count', 10);
if ($min_count > 0) {
	$select->having($select->compare('total', '>', $min_count, ELGG_VALUE_INTEGER));
}

$order = get_input('order', 'count');
if ($order === 'count') {
	$select->orderBy('total', 'DESC');
}
$select->addOrderBy('md.value', 'ASC');

$results = $select->execute()->fetchAll();

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

$configured_rule_tags = [];
if (!empty($metadata_tags)) {
	/* @var $configured_tag \ElggMetdata */
	foreach ($metadata_tags as $configured_tag) {
		$configured_rule_tags[] = $configured_tag->value;
	}
	
	$configured_rule_tags = array_values(array_unique($configured_rule_tags));
}

$suggestions = [];

while ($current_tag = array_pop($results)) {
	
	// filter out tags that already have a rule configured
	if (in_array($current_tag->value, $configured_rule_tags)) {
		continue;
	}
	
	// check if tags are ignored
	$ignore_tags = (array) elgg_extract($current_tag->value, $ignore_config, []);
	
	// check current tag for good suggestions
	foreach ($results as $tag) {
		if (in_array($tag->value, $ignore_tags)) {
			continue;
		}
		
		$levenshtein = levenshtein($current_tag->value, $tag->value);
		$max_length = max(strlen($current_tag->value), strlen($tag->value));
		
		if (($levenshtein / $max_length) > (1/3)) {
			continue;
		}
		
		$suggestions[$current_tag->value][] = $tag->value;
	}
}

if (empty($suggestions)) {
	echo elgg_view_module('info', elgg_echo('tag_tools:admin:tags:suggest:results:title'), elgg_echo('notfound'));
	return;
}

foreach ($suggestions as $from_tag => $to_tags) {
	
	$buttons = [];
	foreach ($to_tags as $to_tag) {
		$buttons[] = elgg_view('output/url', [
			'icon' => 'angle-right',
			'text' => $to_tag,
			'title' => elgg_echo('tag_tools:rules:add'),
			'href' => elgg_generate_url('add:object:tag_tools_rule', [
				'from_tag' => $from_tag,
				'to_tag' => $to_tag,
			]),
			'class' => 'elgg-button elgg-button-action elgg-lightbox mbs',
			'data-colorbox-opts' => json_encode([
				'width' => '600px',
			]),
		]);
	}
	
	$ignore = elgg_view('output/url', [
		'icon' => 'trash-alt',
		'text' => elgg_echo('tag_tools:suggest:ignore'),
		'href' => elgg_generate_action_url('tag_tools/suggestions/ignore', [
			'from_tag' => $from_tag,
			'ignores' => $to_tags,
		]),
	]);

	$title = elgg_echo('tag_tools:admin:tags:suggest:item', [$from_tag]);
	
	$content = implode('<br />', $buttons);

	echo elgg_view_module('info', $title, $content, ['menu' => $ignore]);
}
