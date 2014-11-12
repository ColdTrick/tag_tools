<?php

$tag = get_input("tag");
$encoded_tag = htmlspecialchars($tag, ENT_QUOTES, "UTF-8", false);

if (empty($encoded_tag)) {
	register_error(elgg_echo("error:missing_data"));
	forward(REFERER);
}

tag_tools_toggle_following_tag($encoded_tag);
if (tag_tools_is_user_following_tag($encoded_tag)) {
	system_message(elgg_echo("tag_tools:actions:follow_tag:success:follow", array($encoded_tag)));
} else {
	system_message(elgg_echo("tag_tools:actions:follow_tag:success:unfollow", array($encoded_tag)));
}

forward(REFERER);