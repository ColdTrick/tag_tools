<?php
/**
 * Extend on output/tag
 *
 * @uses $vars['value'] the tag to show for
 */

echo elgg_view_menu('follow_tag', [
	'tag' => elgg_extract('value', $vars),
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
]);
