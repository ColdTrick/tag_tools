<?php

namespace ColdTrick\TagTools;

/**
 * Defines and edit rule
 */
class EditDefinition {
	
	protected ?string $tag;
	
	protected ?\TagDefinition $entity = null;
	
	/**
	 * Create new edit form preparation
	 *
	 * @param string|\TagDefinition|null $tag starting point
	 */
	public function __construct(string|\TagDefinition $tag = null) {
		
		if (is_string($tag)) {
			$this->tag = strtolower($tag);
		}
		
		if ($tag instanceof \TagDefinition) {
			$this->entity = $tag;
		}
	}
	
	/**
	 * Get form body vars
	 *
	 * @return array
	 */
	public function __invoke() {
		
		$defaults = [
			'title' => '',
			'description' => '',
			'bgcolor' => null,
			'textcolor' => null,
		];
		
		if (isset($this->tag)) {
			$defaults['title'] = $this->tag;
		}
		
		// edit
		if ($this->entity instanceof \TagDefinition) {
			foreach ($defaults as $name => $value) {
				$defaults[$name] = $this->entity->$name;
			}
			
			$defaults['entity'] = $this->entity;
		}
		
		$sticky_vars = elgg_get_sticky_values('tag_definition/edit');
		if (!empty($sticky_vars)) {
			foreach ($sticky_vars as $name => $value) {
				$defaults[$name] = $value;
			}
			
			elgg_clear_sticky_form('tag_definition/edit');
		}
		
		return $defaults;
	}
}
