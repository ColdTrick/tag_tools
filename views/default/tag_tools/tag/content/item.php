<?php
/**
 * Output view for a tag content module
 */

$title = elgg_extract('title', $vars);
$content = elgg_extract('content', $vars);
$more = elgg_extract('more', $vars);

echo elgg_view_module('tag_content', $title, $content, ['menu' => $more]);
