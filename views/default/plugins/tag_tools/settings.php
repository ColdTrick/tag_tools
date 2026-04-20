<?php

/* @var $plugin ElggPlugin */
$plugin = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'switch',
	'#label' => elgg_echo('tag_tools:settings:transform_hashtag'),
	'#help' => elgg_echo('tag_tools:settings:transform_hashtag:help'),
	'name' => 'params[transform_hashtag]',
	'value' => $plugin->transform_hashtag,
]);

echo elgg_view_field([
	'#type' => 'switch',
	'#label' => elgg_echo('tag_tools:settings:whitelist'),
	'#help' => elgg_echo('tag_tools:settings:whitelist:help'),
	'name' => 'params[whitelist]',
	'value' => $plugin->whitelist,
]);

echo elgg_view_field([
	'#type' => 'radio',
	'#label' => elgg_echo('tag_tools:settings:separate_notifications'),
	'name' => 'params[separate_notifications]',
	'options_values' => [
		1 => elgg_echo('tag_tools:settings:separate_notifications:enabled'),
		0 => elgg_echo('tag_tools:settings:separate_notifications:disabled'),
	],
	'value' => $plugin->separate_notifications,
]);
