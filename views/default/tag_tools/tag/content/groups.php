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

$entity_where = EntityWhereClause::factory(new QueryOptions([
	'type_subtype_pairs' => elgg_entity_types_with_capability('searchable'),
]));
$access_where = new AccessWhereClause();
$access_where->use_enabled_clause = false;

$metadata_where = new MetadataWhereClause();
$metadata_where->names = tag_tools_rules_get_tag_names();
$metadata_where->values = [strtolower($tag)];
$metadata_where->case_sensitive = false;
$metadata_where->value_type = ELGG_VALUE_STRING;

$select = Select::fromTable('entities', 'e');
$access_part = $access_where->prepare($select, 'e');

$select->select('e.container_guid')
	->addSelect('count(*) as total')
	->join('e', 'entities', 'ec', $select->compare('e.container_guid', '=', 'ec.guid'))
	->where($entity_where->prepare($select, 'e'))
	->andWhere($metadata_where->prepare($select, $select->joinMetadataTable('e')))
	->andWhere($select->compare('ec.type', '=', 'group', ELGG_VALUE_STRING))
	->groupBy('e.container_guid')
	->orderBy('total', 'desc')
	->setMaxResults(3);

if (!empty($access_part)) {
	$select->andWhere($access_part);
}

$res = $select->execute()->fetchAllAssociative();
if (empty($res)) {
	return;
}

$guids = [];
foreach ($res as $row) {
	$guids[] = (int) $row['container_guid'];
}

$groups = elgg_get_entities([
	'type' => 'group',
	'guids' => $guids,
]);
$ordered_groups = [];
foreach ($groups as $group) {
	$key = array_search($group->guid, $guids);
	$ordered_groups[$key] = $group;
}

$content = elgg_view_entity_list($ordered_groups, [
	'full_view' => false,
	'pagination' => false,
]);

echo elgg_view('tag_tools/tag/content/item', [
	'title' => elgg_echo('tag_tools:tag:content:groups'),
	'content' => $content,
]);
