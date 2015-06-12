<?php
/**
 * All plugin events are bundled here
 */

/**
 * Listen to the creation of metadata
 *
 * @param string       $event    the name of the event
 * @param string       $type     the type of the event
 * @param ElggMetadata $metadata supplied metadata
 *
 * @return void
 */
function tag_tools_create_metadata_event_handler($event, $type, $metadata) {
	
	if (empty($metadata) || !($metadata instanceof ElggMetadata)) {
		return;
	}
	
	// is it a tag
	if ($metadata->name != "tags") {
		return;
	}
	
	// get the entity for further use
	$ia = elgg_set_ignore_access(true);
	
	$entity_guid = $metadata->entity_guid;
	// can't use elgg get entity because cache is not correctly updated
	$entity_row = get_entity_as_row($entity_guid);
	
	elgg_set_ignore_access($ia);
	
	// shortcut for private entities
	if ($entity_row->access_id == ACCESS_PRIVATE) {
		return;
	}
	
	// only send notifications on creation of the entity
	$time_created_treshold = 5;
	if ($entity_row->time_created < (time() - $time_created_treshold)) {
		// assume it is an update
		return;
	}
	
	// check of the entity is allowed for notifications
	if (!tag_tools_is_notification_entity($entity_row->guid)) {
		return;
	}
	
	$tag = $metadata->value;
	
	$options = array(
		"type" => "user",
		"annotation_name_value_pairs" => array(
			"name" => "follow_tag",
			"value" => $tag
		),
		"limit" => false
	);

	$ia = elgg_set_ignore_access(true);
	
	$dbprefix = elgg_get_config("dbprefix");
	$entities = new ElggBatch("elgg_get_entities_from_annotations", $options);
	foreach ($entities as $user) {
		
		// check if not trying to notify the owner
		if ($user->getGUID() == $entity_row->owner_guid) {
			continue;
		}
		
		// force a correct access bit
		elgg_set_ignore_access(false);
		
		// check access for the user, can't use has_access_to_entity
		// because that requires a full entity
		$access_bit = _elgg_get_access_where_sql(array("user_guid" => $user->getGUID()));
		
		// ignore access to get the correct next user
		elgg_set_ignore_access(true);
		
		// build correct query to check access
		$query = "SELECT guid FROM {$dbprefix}entities e
			 WHERE e.guid = {$entity_guid}
			 AND {$access_bit}";
		
		if (get_data($query)) {
			// regsiter shutdown function because we need the full entity
			// this is a workaround and should be reviewed in the near future
			register_shutdown_function("tag_tools_notify_user", $user->getGUID(), $entity_row->guid, $tag);
		}
	}
	
	elgg_set_ignore_access($ia);
}
