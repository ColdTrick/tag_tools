<?php

// add description
echo elgg_view('output/longtext', [
	'value' => elgg_echo('admin:upgrades:set_tag_notifications_sent:description'),
]);

// how much content to upgrade
$count = 0;
$type_subtypes = tag_tools_get_notification_type_subtypes();
if (!empty($type_subtypes)) {
	$dbprefix = elgg_get_config('dbprefix');
	
	$count = elgg_get_entities_from_metadata([
		'type_subtype_pairs' => $type_subtypes,
		'count' => true,
		'metadata_name_value_pairs' => [
			'name' => 'tags',
			'value' => '',
			'operand' => '!=',
		],
		'wheres' => ["NOT EXISTS (
			SELECT 1 FROM {$dbprefix}private_settings ps
			WHERE ps.entity_guid = e.guid
			AND ps.name = 'tag_tools:sent_tags')",
		],
	]);
}

// show upgrade progress + start button
echo elgg_view('admin/upgrades/view', [
	'count' => $count,
	'action' => 'action/tag_tools/upgrades/set_tag_notifications_sent',
]);
