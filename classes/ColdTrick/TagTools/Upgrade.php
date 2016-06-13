<?php

namespace ColdTrick\TagTools;

class Upgrade {
	
	/**
	 * Listen to the upgrade event to check if there is any old content with unsent tag notifications
	 *
	 * @param string $event  the name of the event
	 * @param string $type   the type of the event
	 * @param mixed  $entity supplied entity/params
	 *
	 * @return void
	 */
	public static function markOldTagsAsSent($event, $type, $entity) {
		
		$path = 'admin/upgrades/set_tag_notifications_sent';
		$upgrade = new \ElggUpgrade();
		
		// ignore acces while checking for existence
		$ia = elgg_set_ignore_access(true);
		
		// already registered?
		if (!$upgrade->getUpgradeFromPath($path)) {
			
			$upgrade->title = elgg_echo('admin:upgrades:set_tag_notifications_sent');
			$upgrade->description = elgg_echo('admin:upgrades:set_tag_notifications_sent:description');
			
			$upgrade->setPath($path);
			
			$upgrade->save();
		}
		
		// restore access
		elgg_set_ignore_access($ia);
	}
}
