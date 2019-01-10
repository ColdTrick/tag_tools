<?php

$user = elgg_extract('entity', $vars, elgg_get_page_owner_entity());
if (!$user instanceof ElggUser) {
	return;
}

echo elgg_view_module('info', '', elgg_echo('tag_tools:notifications:description'));

$notification_methods = elgg_get_notification_methods();

$tags = tag_tools_get_user_following_tags($user->guid);
if (empty($tags)) {
	echo elgg_view('output/longtext', ['value' => elgg_echo('tag_tools:notifications:empty')]);
	return;
}

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'user_guid',
	'value' => $user->guid,
]);

elgg_require_js('tag_tools/notifications');

echo "<table class='elgg-table-alt'>";
// header with notification methods and delete
echo '<thead>';
echo '<tr><th>&nbsp;</th>';
foreach ($notification_methods as $method) {
	echo elgg_format_element('th', ['class' => 'center'], elgg_echo("notification:method:{$method}"));
}
echo elgg_format_element('th', ['class' => 'center'], elgg_echo('delete'));

echo '</tr>';
echo '</thead>';

echo '<tbody>';
// all tags
foreach ($tags as $tag) {
	$encoded_tag = htmlspecialchars($tag, ENT_QUOTES, 'UTF-8', false);
	
	echo '<tr>';
	echo '<td>';
	echo elgg_view('output/url', [
		'text' => $tag,
		'href' => elgg_generate_url('collection:tag', [
			'tag' => $tag,
		]),
	]);
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => "tags[{$encoded_tag}]",
		'value' => '0',
	]);
	echo '</td>';
	
	foreach ($notification_methods as $method) {
		$checked = tag_tools_check_user_tag_notification_method($encoded_tag, $method, $user->guid);
		
		echo elgg_format_element('td', ['class' => 'center'], elgg_view("input/checkbox", [
			'name' => "tags[{$encoded_tag}][]",
			'checked' => $checked,
			'value' => $method,
			'default' => false,
		]));
	}
	
	echo elgg_format_element('td', ['class' => 'center'], elgg_view('output/url', [
		'icon' => 'delete',
		'text' => false,
		'href' => elgg_generate_action_url('tag_tools/follow_tag', [
			'tag' => $tag,
		]),
		'class' => 'tag-tools-unfollow-tag',
		'is_action' => true,
	]));
	
	echo '</tr>';
}

echo '</tbody>';
echo '</table>';

$foot = elgg_view('input/submit', [
	'value' => elgg_echo('save'),
	'class' => 'elgg-button-submit mtl',
]);

elgg_set_form_footer($foot);
