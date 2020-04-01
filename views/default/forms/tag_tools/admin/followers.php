<?php

$fields = [
	[
		'#type' => 'text',
		'title' => elgg_echo('search'),
		'placeholder' => elgg_echo('search'),
		'name' => 'q',
		'value' => get_input('q'),
	],
	[
		'#type' => 'date',
		'title' => elgg_echo('tag_tools:admin:followers:created_since'),
		'placeholder' => elgg_echo('tag_tools:admin:followers:created_since'),
		'name' => 'created_since',
		'value' => get_input('created_since'),
		'timestamp' => true,
	],
	[
		'#type' => 'date',
		'title' => elgg_echo('tag_tools:admin:followers:created_until'),
		'placeholder' => elgg_echo('tag_tools:admin:followers:created_until'),
		'name' => 'created_until',
		'value' => get_input('created_until'),
		'timestamp' => true,
	],
	[
		'#type' => 'select',
		'title' => elgg_echo('tag_tools:search:order'),
		'name' => 'order',
		'value' => get_input('order'),
		'options_values' => [
			'popular' => elgg_echo('sort:popular'),
			'alpha' => elgg_echo('sort:alpha'),
		],
	],
	[
		'#type' => 'submit',
		'value' => elgg_echo('search'),
	],
];

echo elgg_view('input/fieldset', [
	'align' => 'horizontal',
	'fields' => $fields,
]);
