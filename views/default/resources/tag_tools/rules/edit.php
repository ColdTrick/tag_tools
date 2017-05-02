<?php
/**
 * Edit an existing tag rule
 */

$guid = (int) elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', TagToolsRule::SUBTYPE);

/* @var $entity TagToolsRule */
$entity = get_entity($guid);

// build page elements
$title = elgg_echo('tag_tools:rules:edit', [$entity->getDisplayName()]);

$body_vars = tag_tools_rules_prepare_form_vars($entity);

$body = elgg_view_form('tag_tools/rules/edit', [], $body_vars);

// how to display content
if (elgg_is_xhr()) {
	echo elgg_view_module('inline', $title, $body);
} else {
	// build page
	$page = elgg_view_layout('content', [
		'title' => $title,
		'body' => $body,
		'filter' => false,
	]);
	
	echo elgg_view_page($title, $page);
}
