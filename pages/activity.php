<?php
/**
 * activity stream based on tags
 */

elgg_gatekeeper();

elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());

$tags = tag_tools_get_user_following_tags();
$tag_names = tag_tools_rules_get_tag_names();
if (empty($tags) || empty($tag_names)) {
	forward('activity');
}

$name_ids = elgg_get_metastring_map($tags);
$tag_ids = elgg_get_metastring_map($tag_names);

$options = [];

$title = elgg_echo('tag_tools:activity:tags');

$type = preg_replace('[\W]', '', get_input('type', 'all'));
$subtype = preg_replace('[\W]', '', get_input('subtype', ''));
if ($subtype) {
	$selector = "type={$type}&subtype={$subtype}";
} else {
	$selector = "type={$type}";
}

if ($type != 'all') {
	$options['type'] = $type;
	if ($subtype) {
		$options['subtype'] = $subtype;
	}
}
$dbprefix = elgg_get_config('dbprefix');

$options['joins'] = ["JOIN {$dbprefix}metadata md ON rv.object_guid = md.entity_guid"];
$options['wheres'] = ["(md.name_id IN (" . implode(',',$tag_ids) . ")) AND md.value_id IN (" . implode(',', $name_ids) . ")"];

$activity = elgg_list_river($options);
if (!$activity) {
	$activity = elgg_echo('river:none');
}

$content = elgg_view('core/river/filter', ['selector' => $selector]);

$sidebar = elgg_view('core/river/sidebar');

$params = [
	'title' => $title,
	'content' => $content . $activity,
	'sidebar' => $sidebar,
	'filter_context' => 'tags',
	'class' => 'elgg-river-layout',
];

$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);
