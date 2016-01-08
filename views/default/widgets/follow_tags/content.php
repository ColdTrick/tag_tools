<?php 

$widget = elgg_extract('entity', $vars);
$user = $widget->getOwnerEntity();

$annotations = elgg_get_annotations([
	'guid' => $user->guid,
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
	elgg_echo('tag_tools:widgets:follow_tags:empty');
}
