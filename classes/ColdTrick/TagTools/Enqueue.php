<?php

namespace ColdTrick\TagTools;

/**
 * Queue related callbacks
 */
class Enqueue {
	
	/** @var \ElggPlugin the Tag Tools plugin entity */
	protected static $plugin;
	
	/**
	 * Listen to the create event of metadata
	 *
	 * @param \Elgg\Event $event 'create', 'metadata'
	 *
	 * @return void
	 */
	public static function createMetadata(\Elgg\Event $event) {
		if (elgg_get_config('testing_mode')) {
			// @todo when the database bug is resolved this can be re-enabled
			return;
		}
		
		$metadata = $event->getObject();
		if (!$metadata instanceof \ElggMetadata) {
			return;
		}
		
		if ($metadata->name !== 'tags') {
			// not a tags metadata
			return;
		}
		
		if (!self::validateEntity($metadata->entity_guid)) {
			return;
		}
		
		self::enqueueEntity($metadata->entity_guid);
	}
	
	/**
	 * After an entity is done with ->save() check if we need to enqueue it
	 *
	 * @param \Elgg\Event $event 'update:after', 'all'
	 *
	 * @return void
	 */
	public static function afterEntityUpdate(\Elgg\Event $event) {
		if (elgg_get_config('testing_mode')) {
			// @todo when the database bug is resolved this can be re-enabled
			return;
		}
		
		$entity = $event->getObject();
		if (!$entity instanceof \ElggEntity) {
			// not an entity, since we listen to 'all'
			return;
		}
		
		if (!isset($entity->tags)) {
			// no tags
			return;
		}
		
		if (!self::validateEntity($entity->guid)) {
			return;
		}
		
		self::enqueueEntity($entity->guid);
	}
	
	/**
	 * Check if an entity_guid is valid for sending tag notifications
	 *
	 * @param int $entity_guid the entity GUID
	 *
	 * @return bool
	 */
	protected static function validateEntity($entity_guid) {
		
		$entity_guid = (int) $entity_guid;
		if ($entity_guid < 1) {
			return false;
		}
		
		// cache plugin
		self::cachePlugin();
		
		if (self::$plugin->hasRelationship($entity_guid, 'tag_tools:notification')) {
			// already enqueued
			return false;
		}
		
		// can't use elgg get entity because cache is not correctly updated
		$entity_row = elgg_get_entity_as_row($entity_guid);
		if (empty($entity_row)) {
			// invalid entity
			return false;
		}
		
		$entity_access = (int) $entity_row->access_id;
		if ($entity_access === ACCESS_PRIVATE) {
			// private entity
			return false;
		}
		
		if (!tag_tools_is_notification_entity($entity_guid)) {
			// not supported entity type/subtype
			return false;
		}
		
		return true;
	}
	
	/**
	 * Add an entity to the notification queue
	 *
	 * @param int $entity_guid the entity to enqueue
	 *
	 * @return void
	 */
	protected static function enqueueEntity($entity_guid) {
		
		$entity_guid = (int) $entity_guid;
		if ($entity_guid < 1) {
			return;
		}
		
		// cache plugin
		self::cachePlugin();
		
		if (!(bool) self::$plugin->getSetting('separate_notifications')) {
			// no separate tag notifications
			return;
		}
		
		if (self::$plugin->hasRelationship($entity_guid, 'tag_tools:notification')) {
			// already queued
			return;
		}
		
		self::$plugin->addRelationship($entity_guid, 'tag_tools:notification');
	}
	
	/**
	 * Cache the plugin for later use
	 *
	 * @return void
	 */
	protected static function cachePlugin() {
		
		if (isset(self::$plugin)) {
			return;
		}
		
		self::$plugin = elgg_get_plugin_from_id('tag_tools');
	}
}
