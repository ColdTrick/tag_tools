<?php
/**
 * View to output content based on a given tag
 *
 * @uses $vars['tag']  the tag being viewed right now
 * @uses $vars[entity] the TagDefinition for the given tag (if any)
 */

$tag = elgg_extract('tag', $vars);
if (elgg_is_empty($tag)) {
	return;
}
$tag = strtolower($tag);

$definition = elgg_extract('entity', $vars);
if ($definition instanceof TagDefinition) {
	echo elgg_view('output/longtext', [
		'value' => $definition->description,
		'class' => 'tag-tools-tag-description',
	]);
}

$content = elgg_view('tag_tools/tag/content', $vars);
if (empty($content)) {
	echo elgg_view('page/components/no_results', [
		'no_results' => elgg_echo('tag_tools:tag:view:no_results', [$tag]),
	]);
	return;
}

echo elgg_format_element('div', ['class' => 'tag-tools-tag-content-wrapper'], $content);
