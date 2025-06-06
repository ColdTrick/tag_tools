<?php
/**
 * Search for tags for a user to follow
 */

use Elgg\Database\MetadataTable;
use Elgg\Database\Select;

$user_guid = (int) elgg_extract('user_guid', $vars);
$term = elgg_extract('term', $vars);
$limit = (int) elgg_extract('limit', $vars);

$select = Select::fromTable(MetadataTable::TABLE_NAME, 'md');
$select->select("{$select->getTableAlias()}.value")
	->addSelect('count(*) as total')
	->where($select->compare("{$select->getTableAlias()}.name", '=', 'tags', ELGG_VALUE_STRING))
	->andWhere($select->compare("{$select->getTableAlias()}.value", 'like', "%{$term}%", ELGG_VALUE_STRING))
	->groupBy("{$select->getTableAlias()}.value")
	->orderBy("{$select->getTableAlias()}.value", 'ASC')
	->setMaxResults($limit);

$followed_tags = tag_tools_get_user_following_tags($user_guid);
if (!empty($followed_tags)) {
	$select->andWhere($select->compare("{$select->getTableAlias()}.value", 'not in', $followed_tags, ELGG_VALUE_STRING));
}

$body = [];
$result = elgg()->db->getData($select);
foreach ($result as $row) {
	$label = $row->value;
	$label .= elgg_format_element('span', ['class' => 'elgg-subtext'], elgg_echo('tag_tools:livesearch:tags:count', [$row->total]));
	
	$data = [
		'label' => $label,
		'value' => $row->value,
	];
	
	$body[] = $data;
}

echo elgg_view_page('', json_encode($body));
