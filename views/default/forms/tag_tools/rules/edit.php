<?php

/* @var $entity \TagToolsRule */
$entity = elgg_extract('entity', $vars);
$edit = false;
if ($entity instanceof TagToolsRule) {
	$edit = true;
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'guid',
		'value' => $entity->getGUID(),
	]);
	
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'tag_action',
		'value' => elgg_extract('tag_action', $vars),
	]);
}

// from tag
echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('tag_tools:rules:from_tag'),
	'name' => 'from_tag',
	'value' => elgg_extract('from_tag', $vars),
	'required' => true,
	'readonly' => $edit,
]);

// action (only for new)
if (!$edit) {
	echo elgg_view_field([
		'#type' => 'select',
		'#label' => elgg_echo('tag_tools:rules:tag_action'),
		'id' => 'tag-tools-rules-edit-action',
		'name' => 'tag_action',
		'value' => elgg_extract('tag_action', $vars),
		'options_values' => [
			'replace' => elgg_echo('tag_tools:rules:tag_action:replace'),
			'delete' => elgg_echo('tag_tools:rules:tag_action:delete'),
		],
	]);
	
	echo elgg_format_element('script', [], 'require(["tag_tools/rules/edit"]);');
}

// to tag
if (!$edit || $entity->tag_action !== 'delete') {
	echo elgg_view_field([
		'#type' => 'text',
		'#label' => elgg_echo('tag_tools:rules:to_tag'),
		'#class' => (elgg_extract('tag_action', $vars) === 'delete') ? 'hidden' : null,
		'id' => 'tag-tools-rules-edit-to',
		'name' => 'to_tag',
		'value' => elgg_extract('to_tag', $vars),
		'required' => true,
		'disabled' => (elgg_extract('tag_action', $vars) === 'delete'),
	]);
}

// footer
if ($edit) {
	$footer = elgg_view_field([
		'#type' => 'submit',
		'name' => 'save',
		'value' => elgg_echo('tag_tools:rules:save_execute'),
	]);
} else {
	$footer = elgg_view_field([
		'#type' => 'fieldset',
		'align' => 'horizontal',
		'fields' => [
			[
				'#type' => 'submit',
				'name' => 'save',
				'value' => elgg_echo('tag_tools:rules:save_execute'),
			],
			[
				'#type' => 'submit',
				'name' => 'save',
				'value' => elgg_echo('tag_tools:rules:execute'),
			],
		]
	]);
}

elgg_set_form_footer($footer);
