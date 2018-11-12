<?php

/* @var $widget ElggWidget */
$widget = elgg_extract('entity', $vars);

$annotations = elgg_get_annotations([
	'guid' => $widget->owner_guid,
	'limit' => false,
	'annotation_name' => 'follow_tag',
]);

if ($annotations) {
	$tags = [];
	foreach ($annotations as $tag) {
		$tags[] = $tag->value;
	}
	echo elgg_view('output/tags', ['value' => $tags]);
} else {
	echo elgg_echo('widgets:follow_tags:empty');
}
