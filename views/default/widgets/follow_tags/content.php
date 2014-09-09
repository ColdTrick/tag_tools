<?php 

$widget = $vars["entity"];
$user = $widget->getOwnerEntity();

$annotation_options = array(
	"guid" => $user->guid,
	"limit" => false,
	"annotation_name" => "follow_tag"
);

$annotations = elgg_get_annotations($annotation_options);
if ($annotations) {
	$tags = array();
	foreach ($annotations as $tag) {
		$tags[] = $tag->value;
	}
	echo elgg_view("output/tags", array("value" => $tags));
} else {
	elgg_echo("tag_tools:widgets:follow_tags:empty");
}
