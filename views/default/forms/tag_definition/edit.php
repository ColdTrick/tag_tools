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
	'readonly' => ($entity instanceof TagDefinition),
]);

echo elgg_view_field([
	'#type' => 'longtext',
	'#label' => elgg_echo('description'),
	'name' => 'description',
	'value' => elgg_extract('description', $vars),
]);

// footer
$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('save'),
]);

elgg_set_form_footer($footer);
