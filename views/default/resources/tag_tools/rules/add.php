<?php
/**
 * Create a new tag rule
 */

$title = elgg_echo('tag_tools:rules:add');

$body = elgg_view_form('tag_tools/rules/edit', [
	'prevent_double_submit' => false,
	'sticky_enabled' => true,
]);

if (elgg_is_xhr()) {
	echo elgg_view_module('inline', $title, $body);
	return;
}

echo elgg_view_page($title, [
	'content' => $body,
]);
