<?php

$user = elgg_get_page_owner_entity();

$NOTIFICATION_HANDLERS = _elgg_services()->notifications->getMethodsAsDeprecatedGlobal();

$annotation_options = array(
	"guid" => $user->guid,
	"limit" => false,
	"annotation_name" => "follow_tag"
);

$annotations = elgg_get_annotations($annotation_options);
if ($annotations) {
	
	echo "<table class='elgg-table-alt'>";
	echo "<tr><th>&nbsp;</th>";
	
	foreach($NOTIFICATION_HANDLERS as $method => $foo) {
		echo "<th class='center'>" . elgg_echo('notification:method:' . $method) . "</th>";
	}
	echo "<th class='center'>" . elgg_echo("delete") . "</th>";
	
	echo "</tr>";
	
	foreach ($annotations as $annotation) {
		$tag = $annotation->value;
		
		echo "<tr>";
		echo "<td>" . $tag . "</td>";
		foreach($NOTIFICATION_HANDLERS as $method => $foo) {
			$checked = tag_tools_is_user_following_tag($tag, $user->guid);
			
			echo "<td class='center'>";
			echo elgg_view("input/checkbox", array(
				"name" => "tags[" . $tag . "][" . $method . "]", 
				"checked" => $checked
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
	
	echo "</table>";
	
	echo elgg_view("input/submit", array("value" => elgg_echo("save"), "class" => "mtl"));
		
} else {
	echo elgg_echo("tag_tools:notifications:empty");
}	 
	