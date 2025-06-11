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

echo elgg_view('input/fieldset', [
	'align' => 'horizontal',
	'fields' => [
		[
			'#type' => 'text',
			'#class' => 'elgg-field-stretch',
			'name' => 'q',
			'value' => get_input('q'),
			'placeholder' => elgg_echo('search'),
			'title' => elgg_echo('search'),
		],
		[
			'#type' => 'number',
			'name' => 'min_count',
			'value' => (int) get_input('min_count', 10),
			'placeholder' => elgg_echo('tag_tools:search:min_count'),
			'title' => elgg_echo('tag_tools:search:min_count'),
			'style' => 'width: 4rem',
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
				'count_9_0' => elgg_echo('sort:popular') . ' (9-0)',
				'count_0_9' => elgg_echo('sort:popular') . ' (0-9)',
				'alpha_a_z' => elgg_echo('sort:alpha') . ' (a-z)',
				'alpha_z_a' => elgg_echo('sort:alpha') . ' (z-a)',
			],
			'title' => elgg_echo('tag_tools:search:order'),
		],
		[
			'#type' => 'submit',
			'text' => elgg_echo('search'),
		],
	],
]);
