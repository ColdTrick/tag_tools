<?php
/**
 * Render an edit link
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
	'text' => elgg_view_icon('edit'),
	'href' => "tag_tools/rules/edit/{$entity->getGUID()}",
	'title' => elgg_echo('edit'),
	'is_trusted' => true,
	'class' => 'elgg-lightbox',
	'data-colorbox-opts' => json_encode([
		'width' => '600px',
	]),
]);

echo elgg_format_element('td', [
	'style' => 'width: 40px;',
	'class' => 'center',
], $link);
