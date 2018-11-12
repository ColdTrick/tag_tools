<?php
/**
 * All plugin functions are bundled here
 */

use Elgg\Database\Select;
use Elgg\Database\Clauses\EntityWhereClause;
use Elgg\Database\QueryOptions;

/**
 * Get all the tags a user is following
 *
 * @param int  $user_guid   the user to get the tags for (default: current user)
 * @param bool $reset_cache reset the internal cache
 *
 * @return bool|array
 */
function tag_tools_get_user_following_tags($user_guid = 0, $reset_cache = false) {
	static $cache;
	
	if (!isset($cache)) {
		$cache = [];
	}
	
	$user_guid = (int) $user_guid;
	if ($user_guid < 1) {
		$user_guid = elgg_get_logged_in_user_guid();
	}
	
	if (empty($user_guid)) {
		return false;
	}
	
	if (!isset($cache[$user_guid]) || $reset_cache) {
		$cache[$user_guid] = [];

		$annotations = elgg_call(ELGG_IGNORE_ACCESS, function() use ($user_guid) {
			return elgg_get_annotations([
				'guid' => $user_guid,
				'annotation_name' => 'follow_tag',
				'limit' => false,
			]);
		});
		
		if (!empty($annotations)) {
			foreach ($annotations as $annotation) {
				$cache[$user_guid][] = $annotation->value;
			}
		}
	}
	
	return $cache[$user_guid];
}

/**
 * Check if a user is following a certain tag
 *
 * @param string $tag       the tag to check
 * @param int    $user_guid the user to check for (default: current user)
 *
 * @return bool
 */
function tag_tools_is_user_following_tag($tag, $user_guid = 0) {
	
	if (empty($tag)) {
		return false;
	}

	$user_guid = (int) $user_guid;
	if ($user_guid < 1) {
		$user_guid = elgg_get_logged_in_user_guid();
	}

	if (empty($user_guid)) {
		return false;
	}
	
	$user_tags = tag_tools_get_user_following_tags($user_guid);
	if (empty($user_tags)) {
		return false;
	}
	
	return in_array($tag, $user_tags);
}

/**
 * Add or remove a tag from the follow list of a user
 *
 * @param string $tag       the tag to (un)follow
 * @param int    $user_guid the user to save the setting for
 * @param bool   $track     add/remove the tag
 *
 * @return void
 */
function tag_tools_toggle_following_tag($tag, $user_guid = 0, $track = null) {
	
	if (empty($tag)) {
		return;
	}
	
	$user_guid = (int) $user_guid;
	if ($user_guid < 1) {
		$user_guid = elgg_get_logged_in_user_guid();
	}

	$user = get_user($user_guid);
	if (empty($user)) {
		return;
	}
	
	if ($track === null) {
		$track = !tag_tools_is_user_following_tag($tag, $user_guid);
	}

	// remove the tag from the follow list
	elgg_call(ELGG_IGNORE_ACCESS, function() use ($user_guid, $tag) {
		elgg_delete_annotations([
			'guid' => $user_guid,
			'limit' => false,
			'annotation_name' => 'follow_tag',
			'annotation_value' => $tag,
		]);
		
		tag_tools_remove_tag_from_notification_settings($tag, $user_guid);
	});
	
	// did the user want to follow the tag
	if ($track) {
		$user->annotate('follow_tag', $tag, ACCESS_PUBLIC);
	}
	
	// reset cache
	tag_tools_get_user_following_tags($user_guid, true);
}

/**
 * Remove the notification settings for a specific tag
 *
 * @param string $tag       the tag to remove the settings for
 * @param int    $user_guid the user to change the settings for (default: current user)
 *
 * @return bool
 */
function tag_tools_remove_tag_from_notification_settings($tag, $user_guid = 0) {
	
	if (empty($tag)) {
		return false;
	}
	
	$user_guid = (int) $user_guid;
	if ($user_guid < 1) {
		$user_guid = elgg_get_logged_in_user_guid();
	}
	
	if (empty($user_guid)) {
		return false;
	}
	
	$settings = tag_tools_get_user_notification_settings($user_guid);
	if (empty($settings)) {
		// user has no notification settings, so all is good
		return true;
	}
	
	if (!isset($settings[$tag])) {
		// the user had no notification setting for this tag
		return true;
	}
	
	// remove the tag from the settings
	unset($settings[$tag]);
	elgg_set_plugin_user_setting('notifications', json_encode($settings), $user_guid, 'tag_tools');
	tag_tools_get_user_notification_settings($user_guid, true);
	
	return true;
}

/**
 * Get the notification settings for a user
 *
 * @param int  $user_guid   the user to get the settings for
 * @param bool $reset_cache reset the internal cache
 *
 * @return bool|array
 */
function tag_tools_get_user_notification_settings($user_guid = 0, $reset_cache = false) {
	static $cache;
	
	if (!isset($cache)) {
		$cache = [];
	}
	
	$user_guid = (int) $user_guid;
	if ($user_guid < 1) {
		$user_guid = elgg_get_logged_in_user_guid();
	}
	
	if (empty($user_guid)) {
		return false;
	}
	
	if (!isset($cache[$user_guid]) || $reset_cache) {
		$cache[$user_guid] = [];
		
		$setting = elgg_get_plugin_user_setting('notifications', $user_guid, 'tag_tools');
		if (!empty($setting)) {
			$cache[$user_guid] = json_decode($setting, true);
		}
	}
	
	return $cache[$user_guid];
}

/**
 * Get the notification settings for a tag
 *
 * @param string $tag       the tag to get the settings for
 * @param int    $user_guid the user to get the settings for (default: current user)
 *
 * @return false|array
 */
function tag_tools_get_user_tag_notification_settings($tag, $user_guid = 0) {
	
	if (empty($tag)) {
		return false;
	}
	
	$user_guid = (int) $user_guid;
	if ($user_guid < 1) {
		$user_guid = elgg_get_logged_in_user_guid();
	}
	
	if (empty($user_guid)) {
		return false;
	}
	
	$settings = tag_tools_get_user_notification_settings($user_guid);
	if (empty($settings) || !isset($settings[$tag])) {
		// default notifications go the mail
		return ['email'];
	}
	
	if (empty($settings[$tag])) {
		// the user disabled notifications for this tag
		return [];
	}
	
	// return the saved settings
	return $settings[$tag];
}

/**
 * Check if a user has selected the notification method for a tag
 *
 * @param string $tag       the tag to check
 * @param string $method    the notification method to check
 * @param int    $user_guid the user to check for (default: current user)
 *
 * @return bool
 */
function tag_tools_check_user_tag_notification_method($tag, $method, $user_guid = 0) {
	
	if (empty($tag)) {
		return false;
	}
	
	if (empty($method)) {
		return false;
	}
	
	$user_guid = (int) $user_guid;
	if ($user_guid < 1) {
		$user_guid = elgg_get_logged_in_user_guid();
	}
	
	if (empty($user_guid)) {
		return false;
	}
	
	$tag_settings = tag_tools_get_user_tag_notification_settings($tag, $user_guid);
	if (empty($tag_settings)) {
		// user has disabled notifications for this tag
		return false;
	}
	
	// check if the user has selected the notification method
	return in_array($method, $tag_settings);
}

/**
 * Check is notifications for this entity are allowed
 *
 * @param int $entity_guid the entity guid
 *
 * @return bool
 */
function tag_tools_is_notification_entity($entity_guid) {
	
	$entity_guid = (int) $entity_guid;
	$entity_row = get_entity_as_row($entity_guid);
	if (empty($entity_row)) {
		return false;
	}
	
	$type_subtypes = tag_tools_get_notification_type_subtypes();
	if (empty($type_subtypes) || !is_array($type_subtypes)) {
		return false;
	}
	
	$type = $entity_row->type;
	if (empty($type) || !isset($type_subtypes[$type])) {
		return false;
	}
	
	if ($type !== 'object') {
		// user, group, site
		return true;
	}
	
	return in_array($entity_row->subtype, elgg_extract($type, $type_subtypes));
}

/**
 * Get the type/subtypes for which tag_tools notifications are allowed
 *
 * @return false|array
 */
function tag_tools_get_notification_type_subtypes() {
	static $result;
	
	if (!isset($result)) {
		// default to registered (searchable) entities
		$result = get_registered_entity_types();
		
		// remove users from tag notifications
		unset($result['user']);
		
		// allow others to change the type/subtypes
		$result = elgg_trigger_plugin_hook('notification_type_subtype', 'tag_tools', $result, $result);
	}
	
	return $result;
}

/**
 * Add tags to the sent tags of an entity
 *
 * @param ElggEntity $entity       the entity to add to
 * @param string[]   $sending_tags the newly sent tags
 *
 * @return bool
 */
function tag_tools_add_sent_tags(ElggEntity $entity, $sending_tags = []) {
	
	if (empty($sending_tags)) {
		// nothing to add
		return true;
	}
	
	if (!is_array($sending_tags)) {
		$sending_tags = [$sending_tags];
	}
	
	$sent_tags = $entity->getPrivateSetting('tag_tools:sent_tags');
	if (!empty($sent_tags)) {
		$sent_tags = json_decode($sent_tags, true);
	} else {
		$sent_tags = [];
	}
	
	// store all processed tags
	$processed_tags = array_merge($sent_tags, $sending_tags);
	$processed_tags = array_unique($processed_tags);
	
	return $entity->setPrivateSetting('tag_tools:sent_tags', json_encode($processed_tags));
}

/**
 * Prepare the form for edit/create of a tag rule
 *
 * @param TagToolsRule $entity the entity to edit
 *
 * @return array
 */
function tag_tools_rules_prepare_form_vars($entity = null) {
	
	$defaults = [
		'from_tag' => get_input('from_tag'),
		'to_tag' => get_input('to_tag'),
		'tag_action' => get_input('tag_action', 'replace'),
		'notify_user' => (bool) get_input('notify_user'),
	];
	
	// load data from existing entity
	if ($entity instanceof TagToolsRule) {
		
		foreach ($defaults as $name => $value) {
			$defaults[$name] = $entity->$name;
		}
		
		$defaults['entity'] = $entity;
	}
	
	// load sticky form
	$sticky = elgg_get_sticky_values('tag_tools/rules/edit');
	if (!empty($sticky)) {
		return array_merge($defaults, $sticky);
	}
	
	return $defaults;
}

/**
 * Get the metadata names for which to apply tag rules
 *
 * @todo support mulitple names
 *
 * @return string[]
 */
function tag_tools_rules_get_tag_names() {
	
	return [
		'tags',
	];
	
	// make this work
	$tag_names = elgg_get_registered_tag_metadata_names();
	return $tag_names;
}

/**
 * Get the type/subtype pairs which are supported for tag rules
 *
 * Result can be used in elgg_get_entities* functions
 *
 * @return array
 */
function tag_tools_rules_get_type_subtypes() {
	static $result;
	
	if (isset($result)) {
		return $result;
	}
	
	$result = [];
	
	$entity_types = get_registered_entity_types();
	if (!empty($entity_types)) {
		foreach ($entity_types as $type => $subtypes) {
			if (empty($subtypes) || !is_array($subtypes)) {
				$result[$type] = ELGG_ENTITIES_ANY_VALUE;
				continue;
			}
			
			$result[$type] = $subtypes;
		}
	}
	
	$result = elgg_trigger_plugin_hook('rules_type_subtypes', 'tag_tools', $result, $result);
	return $result;
}

/**
 * Get the tag rule for the from string
 *
 * @param string $from_tag the string to fetch the rule for
 *
 * @return false|TagToolsRule
 */
function tag_tools_rules_get_rule($from_tag) {
	
	if(trim($from_tag) === '') {
		return false;
	}
	
	// base options
	$rules = elgg_get_entities([
		'type' => 'object',
		'subtype' => TagToolsRule::SUBTYPE,
		'limit' => 1,
		'metadata_name_value_pairs' => [
			'name' => 'from_tag',
			'value' => $from_tag,
			'case_sensitive' => false,
		],
	]);
	if (empty($rules)) {
		return false;
	}
	
	return elgg_extract(0, $rules);
}

/**
 * Get stats about a tag
 *
 * @param string $tag the tag
 *
 * @return false|array
 */
function tag_tools_get_tag_stats($tag) {
	
	if (elgg_is_empty($tag)) {
		return false;
	}
	
	$type_subtypes = tag_tools_rules_get_type_subtypes();
	$tag_names = tag_tools_rules_get_tag_names();
	
	if (empty($type_subtypes) || empty($tag_names)) {
		return false;
	}
	
	$types_where = EntityWhereClause::factory(new QueryOptions(['type_subtype_pairs' => $type_subtypes]));
	
	$select = Select::fromTable('metadata', 'md');
	$select->select('e.type')
		->addSelect('e.subtype')
		->addSelect('count(*) as total')
		->join('md', 'entities', 'e', $select->compare('md.entity_guid', '=', 'e.guid'))
		->where($select->compare('md.name', 'in', $tag_names, ELGG_VALUE_STRING))
		->andWhere($types_where->prepare($select, 'e'))
		->andWhere($select->compare('md.value', '=', $tag, ELGG_VALUE_STRING))
		->groupBy('e.type')
		->addGroupBy('e.subtype')
		->orderBy('total', 'desc')
	;
	
	$res = $select->execute()->fetchAll();
	if (empty($res)) {
		return false;
	}
	
	$result = [];
	foreach ($res as $row) {
		$type_subtype = [
			$row->type,
			$row->subtype,
		];
		$type_subtype = implode(':', $type_subtype);
		$result[$type_subtype] = (int) $row->total;
	}
	
	return $result;
}
