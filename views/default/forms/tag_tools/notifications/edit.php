<?php

$user = elgg_extract('entity', $vars, elgg_get_page_owner_entity());
if (!$user instanceof ElggUser) {
	return;
}

$methods = elgg_get_notification_methods();
if (empty($methods)) {
	return;
}

echo elgg_view('output/longtext', ['value' => elgg_echo('tag_tools:notifications:description')]);

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

elgg_import_esm('forms/tag_tools/notifications/edit');
elgg_import_esm('notifications/subscriptions/record');
elgg_require_css('notifications/subscriptions/record');

$method_options = [];
foreach ($methods as $method) {
	$label = elgg_echo("notification:method:{$method}");
	$method_options[$label] = $method;
}

$lis = [];

foreach ($tags as $tag) {
	$encoded_tag = htmlspecialchars($tag, ENT_QUOTES, 'UTF-8', false);
	
	$preferred_methods = tag_tools_get_user_tag_notification_settings($tag, $user->guid);
	
	$container = elgg_format_element('div', ['class' => 'elgg-subscription-description'], elgg_view_url(elgg_generate_url('collection:tag', ['tag' => $tag]), $tag));
	$container .= elgg_view_field([
		'#type' => 'checkboxes',
		'#class' => 'elgg-subscription-methods',
		'name' => "tags[{$encoded_tag}]",
		'options' => $method_options,
		'value' => $preferred_methods,
		'align' => 'horizontal',
	]);
	
	$container .= elgg_view('output/url', [
		'href' => elgg_generate_action_url('tag_tools/follow_tag', [
			'tag' => $tag,
		]),
		'text' => false,
		'title' => elgg_echo('tag_tools:follow_tag:menu:off'),
		'icon' => 'delete',
	]);
	
	$item = elgg_format_element('div', ['class' => 'elgg-subscription-container'], $container);
	$lis[] = elgg_format_element('li', ['class' => 'elgg-item elgg-subscription-record'], $item);
}

echo elgg_format_element('ul', ['class' => 'elgg-list elgg-subscriptions'], implode($lis));

$foot = elgg_view('input/submit', [
	'text' => elgg_echo('save'),
	'class' => 'elgg-button-submit mtl',
]);

elgg_set_form_footer($foot);
