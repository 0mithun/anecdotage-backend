<?php

namespace App\Models\Traits;

use Elasticquent\ElasticquentTrait;

trait SearchableTrait
{
    use ElasticquentTrait;


    protected $mappingProperties = array(
        'title' => array(
            'type' => 'string',
            'analyzer' => 'standard'
        ),
        'body' => array(
            'type' => 'string',
            'analyzer' => 'standard'
        ),
        'tag_names' => array(
            'type' => 'string',
            'analyzer' => 'standard'
        ),
        'cno' => array(
            'type' => 'string',
            'analyzer' => 'not_analyzed'
        ),
        'is_published' => array(
            'type' => 'boolean',
        ),
    );

    function getIndexDocumentData()
    {
        return array(
            'id'                    =>  $this->id,
            'user_id'               =>  $this->user_id,
            'title'                 =>  $this->title,
            // 'slug'                  =>  $this->slug,
            'body'                  =>  $this->body,
            'is_published'          =>  $this->is_published,
            'age_restriction'       =>  $this->age_restriction,
            'like_count'            =>  $this->like_count,
            'dislike_count'         =>  $this->dislike_count,
            'favorite_count'        =>  $this->favorite_count,
            'visits'                =>  $this->visits,
            'word_count'            =>  $this->word_count,
            'cno'                   =>  $this->cno,
            'tag_ids'               =>  $this->tag_ids,
            'tag_names'             =>  $this->tag_names,
            'emoji_ids'             =>  $this->emoji_ids,
        );
    }


    function getIndexName()
    {
        return 'threads';
    }

    function getTypeName()
    {
        return 'threads';
    }


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
