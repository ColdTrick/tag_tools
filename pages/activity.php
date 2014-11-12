<?php
/**
 * activity stream based on tags
 */

elgg_gatekeeper();

elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());

$tags = tag_tools_get_user_following_tags();
if (empty($tags)) {
	forward("activity");
}

$name_ids = array();
foreach ($tags as $tag) {
	$name_ids[] = elgg_get_metastring_id($tag);
}

$options = array();

$title = elgg_echo("tag_tools:activity:tags");

$type = preg_replace('[\W]', '', get_input('type', 'all'));
$subtype = preg_replace('[\W]', '', get_input('subtype', ''));
if ($subtype) {
	$selector = "type=$type&subtype=$subtype";
} else {
	$selector = "type=$type";
}

if ($type != "all") {
	$options["type"] = $type;
	if ($subtype) {
		$options["subtype"] = $subtype;
	}
}
$dbprefix = elgg_get_config("dbprefix");

$tags_id = elgg_get_metastring_id("tags");

$options["joins"] = array("JOIN {$dbprefix}metadata md ON rv.object_guid = md.entity_guid");
$options["wheres"] = array("(md.name_id = $tags_id) AND md.value_id IN (" . implode(",", $name_ids) . ")");

$activity = elgg_list_river($options);
if (!$activity) {
	$activity = elgg_echo("river:none");
}

$content = elgg_view("core/river/filter", array("selector" => $selector));

$sidebar = elgg_view("core/river/sidebar");

$params = array(
	"title" => $title,
	"content" => $content . $activity,
	"sidebar" => $sidebar,
	"filter_context" => "tags",
	"class" => "elgg-river-layout",
);

$body = elgg_view_layout("content", $params);

echo elgg_view_page($title, $body);
