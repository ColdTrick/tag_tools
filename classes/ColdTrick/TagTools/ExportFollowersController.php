<?php

namespace ColdTrick\TagTools;

use Elgg\Database\Select;

/**
 * Backup download
 *
 * @since 7.0
 */
class ExportFollowersController extends \Elgg\Controllers\CsvDownloadAction {

	/**
	 * {@inheritdoc}
	 */
	protected function getFilename(): string {
		return 'tag-followers.csv';
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getContentHeaders(): array {
		return [
			elgg_echo('tags'),
			elgg_echo('tag_tools:search:count'),
		];
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getContentRows(): array {
		
		$created_since = $this->request->getParam('created_since');
		$created_until = $this->request->getParam('created_until');
		$order = $this->request->getParam('order', 'popular');
		$q = $this->request->getParam('q');
		
		$select = Select::fromTable('annotations');
		$select->select('count(*) as total')
			->addSelect('value')
			->where($select->compare('name', '=', 'follow_tag', ELGG_VALUE_STRING))
			->groupBy('value');
		
		if (!empty($created_since) || !empty($created_until)) {
			$select->andWhere($select->between('time_created', $created_since, $created_until, ELGG_VALUE_TIMESTAMP));
		}
		
		if (!empty($q)) {
			$select->andWhere($select->compare('value', 'like', "%{$q}%", ELGG_VALUE_STRING));
		}
		
		if ($order === 'popular') {
			$select->orderBy('total', 'desc');
		}
		
		$select->addOrderBy('value', 'asc');
		
		$tags = elgg()->db->getData($select);
		
		$results = [];
		foreach ($tags as $tag) {
			$results[] = [
				$tag->value,
				$tag->total,
			];
		}
		
		return $results;
	}
}
