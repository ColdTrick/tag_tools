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

$tag_names = tag_tools_rules_get_tag_names();

$entity_where = EntityWhereClause::factory(new QueryOptions([
	'type_subtype_pairs' => elgg_entity_types_with_capability('searchable'),
]));
$access_where = new AccessWhereClause();
$access_where->use_enabled_clause = false;

$metadata_where = new MetadataWhereClause();
$metadata_where->names = $tag_names;
$metadata_where->values = [$tag];
$metadata_where->case_sensitive = false;
$metadata_where->value_type = ELGG_VALUE_STRING;

$select = Select::fromTable('metadata', 'md2');
$select->select('md2.value')
	->addSelect('count(*) as total');

$subquery = $select->subquery('metadata', 'md');
$subquery->select('md.entity_guid')
	->where($entity_where->prepare($subquery, $subquery->joinEntitiesTable('md', 'entity_guid')))
	->andWhere($metadata_where->prepare($subquery, 'md'));

$access_part = $access_where->prepare($subquery, $subquery->joinEntitiesTable('md', 'entity_guid'));
if (!empty($access_part)) {
	$subquery->andWhere($access_part);
}

$metadata_where = new MetadataWhereClause();
$metadata_where->names = $tag_names;

$select->setParameters($subquery->getParameters(), $subquery->getParameterTypes());
$select->andWhere($metadata_where->prepare($select, 'md2'))
	->andWhere($entity_where->prepare($select, $select->joinEntitiesTable('md2', 'entity_guid')))
	->andWhere($select->compare('md2.entity_guid', 'in', $subquery->getSQL()))
	->andWhere($select->compare('md2.value', '!=', $tag, ELGG_VALUE_STRING))
	->groupBy('md2.value')
	->orderBy('total', 'desc')
	->setMaxResults(10);

$access_part = $access_where->prepare($select, $select->joinEntitiesTable('md2', 'entity_guid'));
if (!empty($access_part)) {
	$select->andWhere($access_part);
}

$res = $select->execute()->fetchAllAssociative();
if (empty($res)) {
	return;
}

$tags = [];
foreach ($res as $row) {
	$tags[] = $row['value'];
}

$content = elgg_view('output/tags', ['value' => $tags]);

echo elgg_view('tag_tools/tag/content/item', [
	'title' => elgg_echo('tag_tools:tag:content:related_tags'),
	'content' => $content,
]);
