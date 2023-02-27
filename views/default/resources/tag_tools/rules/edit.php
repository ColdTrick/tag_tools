<?php
/**
 * Edit an existing tag rule
 */

$guid = (int) elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', TagToolsRule::SUBTYPE);

/* @var $entity TagToolsRule */
$entity = get_entity($guid);

$title = elgg_echo('tag_tools:rules:edit', [$entity->getDisplayName()]);

$body = elgg_view_form('tag_tools/rules/edit', [
	'prevent_double_submit' => false,
	'sticky_enabled' => true,
], ['entity' => $entity]);

if (elgg_is_xhr()) {
	echo elgg_view_module('inline', $title, $body);
	return;
}

echo elgg_view_page($title, [
	'content' => $body,
]);
