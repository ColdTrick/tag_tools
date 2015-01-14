<?php

$user = elgg_extract("entity", $vars, elgg_get_page_owner_entity());
if (empty($user) || !elgg_instanceof($user, "user")) {
	return;
}

$NOTIFICATION_HANDLERS = _elgg_services()->notifications->getMethodsAsDeprecatedGlobal();

$tags = tag_tools_get_user_following_tags($user->getGUID());
if (empty($tags)) {
	echo elgg_echo("tag_tools:notifications:empty");
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
	echo "<tr>";
	echo "<td>";
	echo $tag;
	echo elgg_view("input/hidden", array("name" => "tags[" . $tag . "]", "value" => "0"));
	echo "</td>";
	
	foreach ($NOTIFICATION_HANDLERS as $method => $foo) {
		$checked = tag_tools_check_user_tag_notification_method($tag, $method, $user->getGUID());
		
		echo "<td class='center'>";
		echo elgg_view("input/checkbox", array(
			"name" => "tags[" . $tag . "][]",
			"checked" => $checked,
			"value" => $method,
			"default" => false
		));
		echo "</td>";
	}
	
	echo "<td class='center'>";

	echo elgg_view("output/url", array(
		"text" => elgg_view_icon("delete"),
		"href" => "action/tag_tools/follow_tag?tag=" . $tag,
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
echo elgg_view("input/submit", array("value" => elgg_echo("save"), "class" => "mtl"));
echo "</div>";
