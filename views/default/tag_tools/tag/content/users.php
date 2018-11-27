<?php
/**
 * Show users with a lot of content created with the given tag
 *
 * @uses $vars['tag'] the tag being shown
 */

use Elgg\Database\Select;
use Elgg\Database\Clauses\EntityWhereClause;
use Elgg\Database\QueryOptions;
use Elgg\Database\Clauses\AccessWhereClause;
use Elgg\Database\Clauses\MetadataWhereClause;

$tag = elgg_extract('tag', $vars);
if (elgg_is_empty($tag)) {
	return;
}

$tag = strtolower($tag);

$tag_names = elgg_get_registered_tag_metadata_names();

$entity_where = EntityWhereClause::factory(new QueryOptions([
	'type_subtype_pairs' => get_registered_entity_types(),
]));
$access_where = new AccessWhereClause();
$access_where->use_enabled_clause = false;

$metadata_where = new MetadataWhereClause();
$metadata_where->names = $tag_names;
$metadata_where->values = [$tag];
$metadata_where->case_sensitive = false;
$metadata_where->value_type = ELGG_VALUE_STRING;

$select = Select::fromTable('entities', 'e');
$select->select('e.owner_guid')
	->addSelect('count(*) as total')
	->join('e', 'entities', 'eo', $select->compare('e.owner_guid', '=', 'eo.guid'))
	->where($entity_where->prepare($select, 'e'))
	->andWhere($access_where->prepare($select, 'e'))
	->andWhere($metadata_where->prepare($select, $select->joinMetadataTable('e')))
	->andWhere($select->compare('eo.type', '=', 'user', ELGG_VALUE_STRING))
	->groupBy('e.owner_guid')
	->orderBy('total', 'desc')
	->setMaxResults(3)
;

$res = $select->execute()->fetchAll();
if (empty($res)) {
	return;
}

$guids = [];
foreach ($res as $row) {
	$guids[] = (int) $row->owner_guid;
}

$users = elgg_get_entities([
	'type' => 'user',
	'guids' => $guids,
]);
$ordered_users = [];
foreach ($users as $user) {
	$key = array_search($user->guid, $guids);
	$ordered_users[$key] = $user;
}

$content = elgg_view_entity_list($ordered_users, [
	'full_view' => false,
	'pagination' => false,
]);

echo elgg_view('tag_tools/tag/content/item', [
	'title' => elgg_echo('tag_tools:tag:content:users'),
	'content' => $content,
]);
