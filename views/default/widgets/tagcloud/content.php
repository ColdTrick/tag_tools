<?php

/* @var $widget ElggWidget */
$widget = elgg_extract('entity', $vars);
$owner = $widget->getOwnerEntity();

$cloud_options = [];
if ($owner instanceof ElggUser) {
	$cloud_options['owner_guid'] = $owner->guid;
} elseif ($owner instanceof ElggGroup) {
	$cloud_options['container_guid'] = $owner->guid;
}

$cloud_options['limit'] = $widget->num_items ?: 30;

elgg_push_context('tags');
$cloud_data = elgg_get_tags($cloud_options);

if ($cloud_data) {
	shuffle($cloud_data);
	echo elgg_view('output/tagcloud', ['value' => $cloud_data]);
} else {
	echo elgg_echo('widgets:tagcloud:no_data');
}

elgg_pop_context();
