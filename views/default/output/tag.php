<?php
/**
 * Elgg single tag output
 *
 * @uses $vars['value']    String
 * @uses $vars['type']     The entity type, optional
 * @uses $vars['subtype']  The entity subtype, optional
 * @uses $vars['base_url'] Base URL for tag link, optional, defaults to search URL
 *
 */

if (empty($vars['value'])) {
	return;
}

$query_params = array();

$query_params["q"] = $vars['value'];
$query_params["search_type"] = "tags";

if (!empty($vars['type'])) {
	$query_params["type"] = $vars['type'];
}

if (!empty($vars['subtype'])) {
	$query_params["subtype"] = $vars['subtype'];
}

if (!empty($vars['base_url'])) {
	$url = $vars['base_url'];
} else {
	$url = elgg_get_site_url() . "search";
}

$http_query = http_build_query($query_params);
if ($http_query) {
	$url .= "?" . $http_query;
}

echo elgg_view('output/url', array(
	'href' => $url,
	'text' => $vars['value'],
	'encode_text' => true,
	'rel' => 'tag',
));

if (elgg_is_logged_in()) {
	$encoded_tag = htmlspecialchars($vars["value"], ENT_QUOTES, "UTF-8", false);

	$on_class = "";
	$off_class = "hidden";
	if (tag_tools_is_user_following_tag($encoded_tag)) {
		$on_class = "hidden";
		$off_class = "";
	}
	$urlencoded_tag = urlencode($encoded_tag);
	echo "<ul class='elgg-menu elgg-menu-hz elgg-menu-follow-tag'><li class='elgg-menu-item-follow-tag-on $on_class'>";
	echo elgg_view("output/url", array(
		"text" => elgg_view_icon("refresh"),
		"href" => "action/tag_tools/follow_tag?tag=" . $urlencoded_tag,
		"title" => elgg_echo("tag_tools:follow_tag:menu:on"),
		"is_action" => true
	));
	echo "</li><li class='elgg-menu-item-follow-tag-off $off_class'>";
	echo elgg_view("output/url", array(
		"text" => elgg_view_icon("refresh-hover"),
		"href" => "action/tag_tools/follow_tag?tag=" . $urlencoded_tag,
		"title" => elgg_echo("tag_tools:follow_tag:menu:off"),
		"is_action" => true
	));
	echo "</li></ul>";
}
