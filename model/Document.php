<?php

namespace App\Model;

use App\Core\Storage\Storable;
use App\Core\Utility\StringHelper;
use DateTime;

class Document extends Storable
{
    static $fields = [
        'name' => [
            'required' => true,
            'type' => 'string',
        ],
        'slug' => [
            'type' => 'string',
            'regex' => '/^[a-z_-]+$/'
        ],
        'pagetitle' => [
            'required' => true,
            'type' => 'string',
        ],
        'nocache' => [
            'type' => 'boolean',
            'default' => true,
        ],
        'publised' => [
            'default' => false,
            'type' => 'boolean'
        ],
        'published_at' => [
            'type' => 'integer'
        ],
        'created_at' => [
            'type' => 'integer',
        ],
        'modified_at' => [
            'type' => 'integer',
        ],
        'author' => [
            'required' => true,
            'type' => 'string',
        ],
        'segments' => [
            'type' => 'array'
        ],
        'langcode' => [
            'type' => 'string',
            'required' => true
        ],
        'template' => [
            'type' => 'string',
            'default' => 'default'
        ],
        'pretty_urls' => [
            'array' => [
                'type' => 'string'
            ],
        ]
    ];

    static function preCreate(array $data = []): array
    {
        $data['pagetitle'] = $data['pagetitle'] ?? $data['name'];
        $data['created_at'] = (new DateTime())->getTimestamp();
        $data['langcode'] = strtolower($data['langcode']);

        if (isset($data['slug'])) {
            $data['slug'] = StringHelper::convertToSlug($data['slug']);
        }

        return $data;
    }

    function preSave()
    {
        $this->data['langcode'] = strtolower($this->data['langcode'] ?? 'de');
        $this->data['modified_at'] = (new DateTime())->getTimestamp();

        if ($this->data['publised']) {
            $this->data['published_at'] = $this->data['modified_at'];
        }

        if (isset($this->data['slug'])) {
            $this->data['slug'] = StringHelper::convertToSlug($this->data['slug']);
        }
    }
}
