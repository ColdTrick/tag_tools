<?php
/**
 * All plugin events are bundled here
 */

function tag_tools_create_metadata_event_handler($event, $type, ElggMetadata $metadata) {
	$ia = elgg_set_ignore_access(true);
	
	$entity_guid = $metadata->entity_guid;
	// can't use elgg get entity because cache is not correctly updated
	$entity = get_entity_as_row($entity_guid);
	
	$time_created_treshold = 5;
	
	if ($entity->time_created < (time() - 5)) {
		// assume it is an update
		return;
	}
	
	if ($metadata->name == "tags") {
		$tag = $metadata->value;
		
		$options = array(
			'type' => "user",
			'annotation_name' => "follow_tag",
			'annotation_value' => $tag,
			'limit' => false
		);		
		
		$entities = elgg_get_entities_from_annotations($options);
		
		$dbprefix = elgg_get_config("dbprefix");
		foreach ($entities as $user) {
			
			// check access, can't use has_access_to_entity
			$access_bit = _elgg_get_access_where_sql(array('user_guid' => $user->getGUID()));
			
			$query = "SELECT guid from {$dbprefix}entities e WHERE e.guid = " . $entity_guid;
			// Add access controls
			$query .= " AND " . $access_bit;
			
			if (get_data($query)) {
				tag_tools_notify_user($user->guid, $entity->guid, $tag);
			}
		}		
	}
	
	elgg_set_ignore_access($ia);
}
