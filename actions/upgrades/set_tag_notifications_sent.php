<?php

$success_count = 0;
$error_count = 0;
$dbprefix = elgg_get_config('dbprefix');

$offset = (int) get_input('offset', 0);
$upgrade_complete = (bool) get_input('upgrade_completed', false);

// prepare options
$options = [
	'type_subtype_pairs' => $type_subtypes,
	'metadata_name_value_pairs' => [
		'name' => 'tags',
		'value' => '',
		'operand' => '!=',
	],
	'limit' => 25,
	'offset' => $offset,
	'wheres' => ["NOT EXISTS (
		SELECT 1 FROM {$dbprefix}private_settings ps
		WHERE ps.entity_guid = e.guid
		AND ps.name = 'tag_tools:sent_tags')",
	],
];

$batch = new ElggBatch('elgg_get_entities_from_metadata', $options);
foreach ($batch as $entity) {
	/* @var $entity \ElggEntity */
	$unsent_tags = tag_tools_get_unsent_tags($entity);
	if (empty($unsent_tags)) {
		$error_count++;
		continue;
	}
	
	// mark the tags as sent
	if (tag_tools_add_sent_tags($entity, $unsent_tags)) {
		// report success
		$success_count++;
	} else {
		// something went wrong
		$error_count++;
	}
}

// are we done?
if ((($success_count + $error_count) === 0) || $upgrade_complete) {
	$path = 'admin/upgrades/set_tag_notifications_sent';
	
	$factory = new ElggUpgrade();
	
	$upgrade = $factory->getUpgradeFromPath($path);
	if ($upgrade instanceof ElggUpgrade) {
		$upgrade->setCompleted();
	}
}

// Give some feedback for the UI
echo json_encode([
	'numSuccess' => $success_count,
	'numErrors' => $error_count,
]);
