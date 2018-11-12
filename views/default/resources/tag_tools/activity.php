<?php
/**
 * activity stream based on tags
 */

elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());

$tags = tag_tools_get_user_following_tags();
if (empty($tags)) {
	forward(elgg_generate_url('collection:river:all'));
}

$options = [
	'no_results' => elgg_echo('river:none'),
];

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

$options['metadata_name_value_pairs'] = [
	[
		'name' => 'tags',
		'value' => $tags,
	],
];

$activity = elgg_list_river($options);

$content = elgg_view('river/filter', ['selector' => $selector]);

$sidebar = elgg_view('river/sidebar');

$body = elgg_view_layout('default', [
	'title' => $title,
	'content' =>  $content . $activity,
	'sidebar' => $sidebar ? : false,
	'filter_value' => 'tags',
	'class' => 'elgg-river-layout',
]);

echo elgg_view_page($title, $body);
