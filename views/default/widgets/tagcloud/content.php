<?php

$widget = elgg_extract('entity', $vars);
$owner = $widget->getOwnerEntity();

$cloud_options = [];
if ($owner instanceof ElggUser) {
	$cloud_options['owner_guid'] = $owner->getGUID();
} elseif ($owner instanceof ElggGroup) {
	$cloud_options['container_guid'] = $owner->getGUID();
}

$cloud_options['limit'] = $widget->num_items ?: 30;

elgg_push_context('tags');
$cloud_data = elgg_get_tags($cloud_options);

if ($cloud_data) {
	shuffle($cloud_data);
	$cloud = elgg_view('output/tagcloud', ['value' => $cloud_data]);
} else {
	$cloud = elgg_echo('tag_tools:widgets:tagcloud:no_data');
}
echo $cloud;

elgg_pop_context();