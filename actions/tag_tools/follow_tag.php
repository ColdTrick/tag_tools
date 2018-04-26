<?php

$encoded_tag = htmlspecialchars(get_input('tag'), ENT_QUOTES, 'UTF-8', false);
if (empty($encoded_tag)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

tag_tools_toggle_following_tag($encoded_tag);
if (tag_tools_is_user_following_tag($encoded_tag)) {
	$message = elgg_echo('tag_tools:actions:follow_tag:success:follow', [$encoded_tag]);
} else {
	$message = elgg_echo('tag_tools:actions:follow_tag:success:unfollow', [$encoded_tag]);
}

return elgg_ok_response('', $message);
