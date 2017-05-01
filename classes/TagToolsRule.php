<?php

class TagToolsRule extends ElggObject {
	
	const SUBTYPE = 'tag_tools_rule';
	
	/**
	 * {@inheritDoc}
	 * @see ElggObject::initializeAttributes()
	 */
	protected function initializeAttributes() {
		
		parent::initializeAttributes();
		
		$site = elgg_get_site_entity();
		
		$this->attributes['subtype'] = self::SUBTYPE;
		$this->attributes['owner_guid'] = $site->getGUID();
		$this->attributes['container_guid'] = $site->getGUID();
		$this->attributes['access_id'] = ACCESS_PUBLIC;
	}
	
	/**
	 * {@inheritDoc}
	 * @see ElggObject::getDisplayName()
	 */
	public function getDisplayName() {
		
		switch ($this->action) {
			case 'delete':
				return elgg_echo('tag_tools:rule:title:delete', [$this->from_tag]);
				break;
			case 'replace':
				return elgg_echo('tag_tools:rule:title:replace', [$this->from_tag, $this->to_tag]);
				break;
		}
		
		return parent::getDisplayName();
	}
}
