<?php
/**
 * Form to follow a new tag on the notification settings page
 *
 * @uses $vars['entity'] the user to manage
 */

/* @var $entity \ElggUser */
$user = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'fieldset',
	'fields' => [
		[
			'#type' => 'autocomplete',
			'#label' => elgg_echo('tag_tools:notifications:follow:search'),
			'#help' => elgg_echo('tag_tools:notifications:follow:search:help'),
			'#class' => 'elgg-field-stretch elgg-field-horizontal',
			'placeholder' => elgg_echo('tag_tools:notifications:follow:search:placeholder'),
			'name' => 'tag',
			'required' => true,
			'match_on' => 'tags',
			'options' => [
				'user_guid' => $user->guid,
			],
		],
		[
			'#type' => 'submit',
			'text' => elgg_echo('tag_tools:follow_tag:menu:on:text'),
			'title' => elgg_echo('tag_tools:follow_tag:menu:on'),
		],
	],
]);
