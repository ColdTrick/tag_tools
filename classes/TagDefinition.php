<?php

use Elgg\EntityNotFoundException;

class TagDefinition extends ElggObject {
	
	/**
	 * @var string the subtype of this entity
	 */
	const SUBTYPE = 'tag_definition';
	
	/**
	 * {@inheritDoc}
	 * @see ElggEntity::initializeAttributes()
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		
		$site = elgg_get_site_entity();
		
		$this->attributes['subtype'] = self::SUBTYPE;
		$this->attributes['owner_guid'] = $site->guid;
		$this->attributes['container_guid'] = $site->guid;
		$this->attributes['access_id'] = ACCESS_PUBLIC;
	}
	
	/**
	 * Find a definition for the given tag
	 *
	 * @param string $tag
	 *
	 * @return false|TagDefinition
	 */
	public static function find(string $tag) {
		
		if (elgg_is_empty($tag)) {
			return false;
		}
		
		$tag = strtolower($tag);
		
		$entities = elgg_call(ELGG_IGNORE_ACCESS, function() use ($tag) {
			return elgg_get_entities([
				'type' => 'object',
				'subtype' => self::SUBTYPE,
				'limit' => 1,
				'metadata_name_value_pairs' => [
					'name' => 'title',
					'value' => $tag,
					'case_sensitive' => false,
				],
			]);
		});
		if (empty($entities)) {
			return false;
		}
		
		return $entities[0];
	}
	
	/**
	 * Create a new tag definition
	 *
	 * @param array $options all new data to set
	 *
	 * @return void|TagDefinition null on error, otherwise a TagDefinition
	 */
	public static function factory(array $options = []) {
		
		$tag = elgg_extract('title', $options);
		if (elgg_is_empty($tag)) {
			elgg_log('A "title" is required for a new Tag Definition', 'ERROR');
			return;
		}
		
		unset($options['title']);
		
		$tag = strtolower($tag);
		
		$definition = static::find($tag);
		if (!$definition instanceof static) {
			$definition = new static();
			$definition->title = $tag;
			
			if (!$definition->save()) {
				return;
			}
		}
		
		foreach ($options as $name => $value) {
			$definition->$name = $value;
		}
		
		return $definition;
	}
	
	/**
	 * Handle /tag_definition/view/guid/title links
	 *
	 * @param \Elgg\Request $request the request
	 *
	 * @throws EntityNotFoundException
	 * @return void
	 */
	public static function forwarder(\Elgg\Request $request) {
		
		$entity = $request->getEntityParam();
		if (!$entity instanceof TagDefinition) {
			throw new EntityNotFoundException();
		}
		
		forward(elgg_generate_url('collection:tag', [
			'tag' => $entity->title,
		]));
	}
}
