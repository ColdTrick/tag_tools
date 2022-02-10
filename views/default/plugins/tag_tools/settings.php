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

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('tag_tools:settings:whitelist'),
	'#help' => elgg_echo('tag_tools:settings:whitelist:help'),
	'name' => 'params[whitelist]',
	'value' => 1,
	'checked' => (bool) $plugin->whitelist,
	'switch' => true,
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
