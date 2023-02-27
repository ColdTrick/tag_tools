<?php

namespace ColdTrick\TagTools\Notifications;

use Elgg\Notifications\SubscriptionNotificationEvent;
use Elgg\Notifications\Notification;

/**
 * Extend the normal content creation notification with tag subscribers and a tag text
 */
class ExtendedContentNotification {
	
	/**
	 * Add the tag subscribers to the content subscribers
	 *
	 * @param \Elgg\Event $event 'get', 'subscriptions'
	 *
	 * @return array|null
	 */
	public static function getSubscribers(\Elgg\Event $event): ?array {
		
		if ((bool) elgg_get_plugin_setting('separate_notifications', 'tag_tools')) {
			// don't extend normal notifications
			return null;
		}
		
		$notification_event = $event->getParam('event');
		if (!$notification_event instanceof SubscriptionNotificationEvent) {
			return null;
		}
		
		$entity = $notification_event->getObject();
		$action = $notification_event->getAction();
		if (!$entity instanceof \ElggEntity || !in_array($action, ['create', 'publish'])) {
			return null;
		}
		
		if (!tag_tools_is_notification_entity($entity->guid)) {
			return null;
		}
		
		$sending_tags = tag_tools_get_unsent_notification_tags($entity);
		$subscribers = $event->getValue();
		
		// get interested users
		$users_batch = elgg_get_entities([
			'type' => 'user',
			'annotation_name_value_pairs' => [
				'name' => 'follow_tag',
				'value' => $sending_tags,
				'case_sensitive' => false,
			],
			'limit' => false,
			'batch' => true,
		]);
		
		/* @var $user \ElggUser */
		foreach ($users_batch as $user) {
			if ($entity->owner_guid === $user->guid) {
				continue;
			}
			
			// check user access
			if (!tag_tools_validate_entity_access($entity, $user)) {
				continue;
			}
			
			// get the notification settings of the user for one of the sending tags
			// this will prevent duplicate notifications,
			foreach ($sending_tags as $tag) {
				if (!tag_tools_is_user_following_tag($tag, $user->guid)) {
					// user is not following this tag, check the next tag
					continue;
				}
				
				$notifiction_settings = tag_tools_get_user_tag_notification_settings($tag, $user->guid);
				if (empty($notifiction_settings)) {
					// no notification settings for this tag
					continue;
				}
				
				if (isset($subscribers[$user->guid])) {
					$subscribers[$user->guid] = array_merge($subscribers[$user->guid], $notifiction_settings);
					$subscribers[$user->guid] = array_unique($subscribers[$user->guid]);
				} else {
					$subscribers[$user->guid] = $notifiction_settings;
				}
			}
		}
		
		return $subscribers;
	}
	
	/**
	 * Extend the content notification with the tag text
	 *
	 * @param \Elgg\Event $event 'prepare', 'notification'
	 *
	 * @return Notification|null
	 */
	public static function extendNotificationBody(\Elgg\Event $event): ?Notification {
		if ((bool) elgg_get_plugin_setting('separate_notifications', 'tag_tools')) {
			// don't extend normal notifications
			return null;
		}
		
		$notification_event = $event->getParam('event');
		if (!$notification_event instanceof SubscriptionNotificationEvent) {
			return null;
		}
		
		$object = $event->getParam('object');
		$action = $event->getParam('action');
		if (!$object instanceof \ElggEntity || !in_array($action, ['create', 'publish'])) {
			return null;
		}
		
		if (!tag_tools_is_notification_entity($object->guid)) {
			return null;
		}
		
		/* @var $notification Notification */
		$notification = $event->getValue();
		$recipient = $event->getParam('recipient');
		$language = $event->getParam('language');
		$method = $event->getParam('method');
		
		$sending_tags = tag_tools_get_unsent_notification_tags($object);
		$user_tags = tag_tools_get_unsent_notification_tags_for_recipient($sending_tags, $recipient, $method);
		if (empty($user_tags)) {
			// recipient wasn't added because of tag notifications
			return null;
		}
				
		$notification->body .= PHP_EOL . PHP_EOL . elgg_echo('tag_tools:notification:extended:content_tags', [implode(', ', $user_tags)], $language);
		
		return $notification;
	}
}
