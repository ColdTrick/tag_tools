<?php

$user = elgg_extract("entity", $vars, elgg_get_page_owner_entity());
if (empty($user) || !elgg_instanceof($user, "user")) {
	return;
}

echo elgg_view_module("info", "", elgg_echo("tag_tools:notifications:description"));

$NOTIFICATION_HANDLERS = _elgg_services()->notifications->getMethodsAsDeprecatedGlobal();

$tags = tag_tools_get_user_following_tags($user->getGUID());
if (empty($tags)) {
	echo elgg_view('output/longtext', array('value' => elgg_echo("tag_tools:notifications:empty")));
	return;
}

elgg_require_js("tag_tools/notifications");

echo "<table class='elgg-table-alt'>";
// header with notification methods and delete
echo "<thead>";
echo "<tr><th>&nbsp;</th>";
foreach ($NOTIFICATION_HANDLERS as $method => $foo) {
	echo "<th class='center'>" . elgg_echo('notification:method:' . $method) . "</th>";
}
echo "<th class='center'>" . elgg_echo("delete") . "</th>";
echo "</tr>";
echo "</thead>";

echo "<tbody>";
// all tags
foreach ($tags as $tag) {
	$encoded_tag = htmlspecialchars($tag, ENT_QUOTES, "UTF-8", false);
	
	echo "<tr>";
	echo "<td>";
	echo $encoded_tag;
	echo elgg_view("input/hidden", array("name" => "tags[" . $encoded_tag . "]", "value" => "0"));
	echo "</td>";
	
	foreach ($NOTIFICATION_HANDLERS as $method => $foo) {
		$checked = tag_tools_check_user_tag_notification_method($encoded_tag, $method, $user->getGUID());
		
		echo "<td class='center'>";
		echo elgg_view("input/checkbox", array(
			"name" => "tags[" . $encoded_tag . "][]",
			"checked" => $checked,
			"value" => $method,
			"default" => false
		));
		echo "</td>";
	}
	
	echo "<td class='center'>";

	echo elgg_view("output/url", array(
		"text" => elgg_view_icon("delete"),
		"href" => "action/tag_tools/follow_tag?tag=" . urlencode($encoded_tag),
		"class" => "tag-tools-unfollow-tag",
		"is_action" => true
	));
	
	echo "</td>";
	
	echo "</tr>";
}

echo "</tbody>";
echo "</table>";

echo "<div class='elgg-foot'>";
echo elgg_view("input/hidden", array("name" => "user_guid", "value" => $user->getGUID()));
echo elgg_view("input/submit", array("value" => elgg_echo("save"), "class" => "elgg-button-submit mtl"));
echo "</div>";
