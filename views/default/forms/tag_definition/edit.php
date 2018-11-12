<?php

$entity = elgg_extract('entity', $vars);
if ($entity instanceof TagDefinition) {
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'guid',
		'value' => $entity->guid,
	]);
}

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('tag_tools:tag_definition:edit:field:title'),
	'name' => 'title',
	'value' => elgg_extract('title', $vars),
	'required' => true,
	'readonly' => true,
]);

echo elgg_view_field([
	'#type' => 'longtext',
	'#label' => elgg_echo('description'),
	'name' => 'description',
	'value' => elgg_extract('description', $vars),
]);

echo elgg_view_field([
	'#type' => 'fieldset',
	'#help' => elgg_echo('tag_tools:tag_definition:edit:colors:help'),
	'align' => 'horizontal',
	'fields' => [
		[
			'#type' => 'text',
			'#label' => elgg_echo('tag_tools:tag_definition:edit:field:bgcolor'),
			'type' => 'color',
			'name' => 'bgcolor',
			'value' => elgg_extract('bgcolor', $vars),
		],
		[
			'#type' => 'text',
			'#label' => elgg_echo('tag_tools:tag_definition:edit:field:textcolor'),
			'type' => 'color',
			'name' => 'textcolor',
			'value' => elgg_extract('textcolor', $vars),
		],
	],
]);

// footer
$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('save'),
]);

elgg_set_form_footer($footer);
