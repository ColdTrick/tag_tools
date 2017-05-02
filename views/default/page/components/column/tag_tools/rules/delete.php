<?php
/**
 * Render a delete link
 *
 * @uses $vars['item']      The item being rendered
 * @uses $vars['item_vars'] Vars received from the page/components/table view
 * @uses $vars['type']      The item type or ""
 */

$entity = elgg_extract('item', $vars);
if (!($entity instanceof TagToolsRule)) {
	return '&nbsp;';
}

$link = elgg_view('output/url', [
	'text' => elgg_view_icon('delete'),
	'href' => "action/tag_tools/rules/delete?guid={$entity->getGUID()}",
	'title' => elgg_echo('delete'),
	'confirm' => elgg_echo('deleteconfirm'),
]);

echo elgg_format_element('td', [
	'style' => 'width: 40px;',
	'class' => 'center',
], $link);
