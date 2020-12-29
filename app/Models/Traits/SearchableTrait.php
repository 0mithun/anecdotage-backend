<?php

namespace App\Models\Traits;

use Elasticquent\ElasticquentTrait;

trait SearchableTrait
{
    use ElasticquentTrait;



    public static function customSearch($query = null, $aggregations = null, $sourceFields = null, $limit = null, $offset = null, $sort = null)
    {
        $instance = new static;

        $params = $instance->getBasicEsParams(true, $limit, $offset);

        if (!empty($sourceFields)) {
            $params['body']['_source']['include'] = $sourceFields;
        }

        if (!empty($query)) {
            $params['body']['query'] = $query;
        }

        if (!empty($aggregations)) {
            $params['body']['aggs'] = $aggregations;
        }

        if (!empty($sort)) {
            $params['body']['sort'] = $sort;
        }

        $result = $instance->getElasticSearchClient()->search($params);

        return static::hydrateElasticsearchResult($result);
    }
}
