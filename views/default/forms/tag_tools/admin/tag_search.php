<?php

$type_subtypes = tag_tools_rules_get_type_subtypes();

$option_values = ['' => ''];
foreach ($type_subtypes as $type => $subtypes) {
	if (empty($subtypes)) {
		$option_values[$type] = elgg_echo("item:{$type}");
		continue;
	}
	
	foreach ($subtypes as $subtype) {
		$option_values["{$type}:{$subtype}"] = elgg_echo("item:{$type}:{$subtype}");
	}
}

$fields = [
	[
		'#type' => 'text',
		'name' => 'q',
		'value' => get_input('q'),
		'placeholder' => elgg_echo('search'),
		'title' => elgg_echo('search'),
	],
	[
		'#type' => 'number',
		'name' => 'min_count',
		'value' => sanitize_int(get_input('min_count', 10), false),
		'placeholder' => elgg_echo('tag_tools:search:min_count'),
		'title' => elgg_echo('tag_tools:search:min_count'),
		'style' => 'width: 3em',
		'min' => 0,
	],
	[
		'#type' => 'select',
		'name' => 'type_subtype',
		'value' => get_input('type_subtype'),
		'options_values' => $option_values,
		'title' => elgg_echo('tag_tools:search:content_type'),
	],
	[
		'#type' => 'select',
		'name' => 'order',
		'value' => get_input('order'),
		'options_values' => [
			'count' => elgg_echo('sort:popular'),
			'alpha' => elgg_echo('sort:alpha'),
		],
		'title' => elgg_echo('tag_tools:search:order'),
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
