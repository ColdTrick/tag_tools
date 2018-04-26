<?php

$widget = elgg_extract('entity', $vars);

$annotations = elgg_get_annotations([
	'guid' => $widget->getOwnerGUID(),
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
	elgg_echo('widgets:follow_tags:empty');
}
