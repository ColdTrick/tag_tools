<?php

$tb = elgg()->table_columns;

elgg_register_menu_item('title', [
	'name' => 'add',
	'icon' => 'plus',
	'text' => elgg_echo('add'),
	'href' => elgg_generate_url('add:object:tag_tools_rule'),
	'class' => [
		'elgg-button',
		'elgg-button-action',
		'elgg-lightbox',
	],
	'data-colorbox-opts' => json_encode([
		'width' => '600px',
	]),
]);

echo elgg_list_entities([
	'type' => 'object',
	'subtype' => \TagToolsRule::SUBTYPE,
	'no_results' => true,
	'list_type' => 'table',
	'columns' => [
		$tb->getDisplayName(),
		$tb->fromView('tag_tools/rules/edit', elgg_echo('edit')),
		$tb->fromView('tag_tools/rules/delete', elgg_echo('delete')),
	],
]);
