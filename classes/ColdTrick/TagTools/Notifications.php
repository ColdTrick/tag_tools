<?php

namespace ColdTrick\TagTools;

use Elgg\Notifications\NotificationEvent;

class Notifications {
	
	/**
	 * Prevent subscribers for the tag notifications
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param array  $return_value current return value
	 * @param array  $params       supplied params
	 *
	 * @return void|array
	 */
	public static function getSubscribers($hook, $type, $return_value, $params) {
		
		if (!self::validateNotificationEvent($params)) {
			// not the correct notification event
			return;
		}
		
		$event = elgg_extract('event', $params);
		if (!$event instanceof NotificationEvent) {
			return;
		}
		
		$relationship = $event->getObject();
		if (!$relationship instanceof \ElggRelationship) {
			return;
		}
		
		$entity = get_entity($relationship->guid_two);
		if (!$entity instanceof \ElggEntity) {
			return;
		}
		
		$sending_tags = tag_tools_get_unsent_tags($entity);
		if (empty($sending_tags)) {
			return [];
		}
		
		
		$validate_access = function(\ElggUser $user) use ($entity) {
			static $acl_members;
			
			if ($entity->access_id === ACCESS_PRIVATE) {
				return false;
			}
			
			if (!has_access_to_entity($entity, $user)) {
				return false;
			}
			
			if (!isset($acl_members)) {
				$acl_members = false;
				
				if (get_access_collection($entity->access_id) !== false) {
					// this is an acl
					$acl_members = get_members_of_access_collection($entity->access_id, true);
				}
			}
			
			if ($acl_members === false) {
				// not an acl
				return true;
			}
			
			return in_array($user->guid, $acl_members);
		};
		
		$tag_subscribers = [];
		// get interested users
		$user_options = [
			'type' => 'user',
			'annotation_name_value_pairs' => [
				'name' => 'follow_tag',
				'value' => $sending_tags,
			],
			'limit' => false,
		];
		$users_batch = new \ElggBatch('elgg_get_entities_from_annotations', $user_options);
		/* @var $user \ElggUser */
		foreach ($users_batch as $user) {
			
			// check user access
			if (!$validate_access($user)) {
				continue;
			}
			
			// get the notification settings of the user for one of the sending tags
			// this will prevent duplicate notifications,
			foreach ($sending_tags as $tag) {
				
				if (!tag_tools_is_user_following_tag($tag, $user->getGUID())) {
					// user is not following this tag, check the next
					continue;
				}
				
				$notifiction_settings = tag_tools_get_user_tag_notification_settings($tag, $user->getGUID());
				if (empty($notifiction_settings)) {
					// no notification settings for this tag
					continue;
				}
				
				if (isset($tag_subscribers[$user->getGUID()])) {
					$tag_subscribers[$user->getGUID()] = array_merge($tag_subscribers[$user->getGUID()], $notifiction_settings);
					$tag_subscribers[$user->getGUID()] = array_unique($tag_subscribers[$user->getGUID()]);
				} else {
					$tag_subscribers[$user->getGUID()] = $notifiction_settings;
				}
			}
		}
		
		if (!empty($tag_subscribers)) {
			return $tag_subscribers;
		}
		
		return [];
	}
	
	/**
	 * Make the tag tools notification
	 *
	 * @param string                           $hook         the name of the hook
	 * @param string                           $type         the type of the hook
	 * @param \Elgg\Notifications\Notification $return_value current return value
	 * @param array                            $params       supplied params
	 *
	 * @return void|\Elgg\Notifications\Notification
	 */
	public static function prepareMessage($hook, $type, $return_value, $params) {
		
		if (!self::validateNotificationEvent($params)) {
			return;
		}
		
		$recipient = $return_value->getRecipient();
		$method = elgg_extract('method', $params);
		$relationship = elgg_extract('object', $params);
		$language = elgg_extract('language', $params);
		
		$entity = get_entity($relationship->guid_two);
		
		$sending_tags = tag_tools_get_unsent_tags($entity);
		$tag = [];
		foreach ($sending_tags as $sending_tag) {
			
			if (!tag_tools_is_user_following_tag($sending_tag, $recipient->getGUID())) {
				// user is not following this tag
				continue;
			}
			
			if (!tag_tools_check_user_tag_notification_method($sending_tag, $method, $recipient->getGUID())) {
				continue;
			}
			
			$tag[] = $sending_tag;
		}
		$tag = implode(', ', $tag);
		
		// is this a new entity of an update on an existing
		$time_diff = (int) $entity->time_updated - (int) $entity->time_created;
		if ($time_diff < 60) {
			// new entity
			$return_value->subject = elgg_echo('tag_tools:notification:follow:subject', [$tag], $language);
			$return_value->summary = elgg_echo('tag_tools:notification:follow:summary', [$tag], $language);
			$return_value->body = elgg_echo('tag_tools:notification:follow:message', [$tag, $entity->getURL()], $language);
		} else {
			// updated entity
			$return_value->subject = elgg_echo('tag_tools:notification:follow:update:subject', [$tag], $language);
			$return_value->summary = elgg_echo('tag_tools:notification:follow:update:summary', [$tag], $language);
			$return_value->body = elgg_echo('tag_tools:notification:follow:update:message', [$tag, $entity->getURL()], $language);
		}
		
		return $return_value;
	}
	
	/**
	 * Cleanup some stuff
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param null   $return_value current return value
	 * @param array  $params       supplied params
	 *
	 * @return void
	 */
	public static function afterCleanup($hook, $type, $return_value, $params) {
		
		if (!self::validateNotificationEvent($params)) {
			// not the correct notification event
			return;
		}
		
		/* @var $event \Elgg\Notifications\Event */
		$event = elgg_extract('event', $params);
		
		/* @var $relationship \ElggRelationship */
		$relationship = $event->getObject();
		
		$entity = get_entity($relationship->guid_two);
		
		// cleanup the relationship
		remove_entity_relationships($entity->getGUID(), 'tag_tools:notification', true);
		
		// save the newly sent tags
		$sending_tags = tag_tools_get_unsent_tags($entity);
		if (empty($sending_tags)) {
			return;
		}
		
		tag_tools_add_sent_tags($entity, $sending_tags);
	}
	
	/**
	 * Validate that we have a tag_tools notification event
	 *
	 * @param array $params the hook params to check
	 *
	 * @return bool
	 */
	protected static function validateNotificationEvent($params) {
		
		if (empty($params) || !is_array($params)) {
			return false;
		}
		
		$event = elgg_extract('event', $params);
		if (!($event instanceof \Elgg\Notifications\Event)) {
			return false;
		}
		
		$relationship = $event->getObject();
		if (!($relationship instanceof \ElggRelationship)) {
			return false;
		}
		
		if ($relationship->relationship !== 'tag_tools:notification') {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Set the correct URL for the notification relationship
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param string $return_value current return value
	 * @param array  $params       supplied params
	 *
	 * @return void|string
	 */
	public static function getNotificationURL($hook, $type, $return_value, $params) {
		
		$relationship = elgg_extract('relationship', $params);
		if (!($relationship instanceof \ElggRelationship)) {
			return;
		}
		
		if ($relationship->relationship !== 'tag_tools:notification') {
			return;
		}
		
		$entity = get_entity($relationship->guid_two);
		if (!($entity instanceof \ElggEntity)) {
			return;
		}
		
		return $entity->getURL();
	}
}
