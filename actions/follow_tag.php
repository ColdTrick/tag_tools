<?php

$tag = get_input("tag");

$encoded_tag = htmlspecialchars($tag, ENT_QUOTES, "UTF-8", false);

if (empty($encoded_tag)) {
	register_error("empty tag input");
} else {
	tag_tools_toggle_following_tag($encoded_tag);
	if (tag_tools_is_user_following_tag($encoded_tag, null, true)) {
		system_message("You are now following the tag: " . $encoded_tag);	
	} else {
		system_message("You are no longer following the tag: " . $encoded_tag);
	}
}

forward(REFERER);