<?php

/* @var $plugin ElggPlugin */
$plugin = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('tag_tools:settings:transform_hashtag'),
	'#help' => elgg_echo('tag_tools:settings:transform_hashtag:help'),
	'name' => 'params[transform_hashtag]',
	'value' => 1,
	'checked' => (bool) $plugin->transform_hashtag,
	'switch' => true,
]);
