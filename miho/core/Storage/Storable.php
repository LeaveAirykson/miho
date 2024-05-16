<?php

namespace Miho\Core\Storage;

use RuntimeException;
use JsonSerializable;

class Storable implements JsonSerializable
{
    protected $storage = STORAGE_PATH;
    protected $id;
    protected $data = [];
    protected $fields = [];
    protected $model;

    public function __construct($data = [])
    {
        // set storage based on model class
        $this->model = strtolower((new \ReflectionClass(get_class($this)))->getShortName());

        // save storage path
        $this->storage = trim(STORAGE_PATH . '/' . $this->model);

        // check for mandatory props
        $this->setupStorage();

        // define data
        $this->__set($data);

        // define id if exists in $data
        if (array_key_exists('_id', $data)) {
            $this->id = $data['_id'];
        } else {
            // define a id if its not set yet
            $this->id = $this->createId();
            $data['_id'] = $this->id;
        }
    }

    protected function createId()
    {
        return str_replace('.', '', uniqid($this->model . '_', true));
    }

    private function setupStorage()
    {
        if (!is_string($this->storage)) {
            throw new RuntimeException('No storage path has been set for ' . get_class($this));
        }

        if (!is_dir($this->storage)) {
            mkdir($this->storage, 0755, true);
        }

        if (!is_writable($this->storage)) {
            throw new RuntimeException('Storage for ' . get_class($this) . ' is not writable!');
        }
    }

    public function save()
    {
        // define object id
        $this->data['_id'] = $this->id;

        // execute prepare callback
        $this->prepare();

        // encode data 
        $data = json_encode($this->data);

        // define filepat
        $filepath = $this->storage . '/' . $this->id . '.json';

        // save data to file
        file_put_contents($filepath, $data);

        return $data;
    }

    public function prepare()
    {
    }

    public function getData()
    {
        return $this->data;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function jsonSerialize()
    {
        return $this->getData();
    }


    public function __set($key = null, $val = null)
    {
        // everything besides array and strings are invalid
        if (!is_array($key) && !is_string($key)) {
            throw new RuntimeException('Invalid key given in ' . get_called_class() . '->set()');
        }

        // array assignments
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->__set($k, $v);
            }

            return $this;
        }

        // single string key,val
        $this->data[$key] = $val;

        return $this;
    }

    public function __get($key = null)
    {
        if (!is_string($key)) {
            throw new RuntimeException('Invalid key given to ' . get_called_class() . '->get(): ' . $key);
        }

        return $this->data[$key] ?? null;
    }
}
