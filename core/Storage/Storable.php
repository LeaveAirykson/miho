<?php

namespace App\Core\Storage;

use App\CoreStorage\EntityFoundError;
use RuntimeException;
use JsonSerializable;

class Storable implements JsonSerializable
{
    public $data = [];
    public $_id;
    public static $fields = [];
    protected static $internalFields = [
        'rank' => ['type' => 'integer'],
        'id' => ['type' => 'string']
    ];

    function __construct($data = [])
    {
        if (empty($data)) {
            throw new InvalidFieldError(static::class . " constructor data can not be empty!");
        }

        // set defaults based on field definitions
        $this->setDefaults();

        // define data
        $this->__set($data);

        // define id if exists in $data
        if (array_key_exists('id', $data)) {
            $this->_id = $data['id'];
        } else {
            $this->_id = $this->createId();
            $this->data['id'] = $this->_id;
        }
    }

    function setDefaults()
    {
        $this->__set('rank', 0);

        $defs = array_filter(self::getFields(), function ($v, $k) {
            return array_key_exists('default', $v);
        }, ARRAY_FILTER_USE_BOTH);

        foreach ($defs as $key => $value) {
            $this->__set($key, $value['default']);
        }
    }

    public static function getModelName()
    {
        return strtolower((new \ReflectionClass(static::class))->getShortName());
    }

    public static function getStorage()
    {
        return trim(DATA_PATH . '/' . self::getModelName());
    }

    public static function create($data = [])
    {
        return (new static(static::preCreate($data)))->save();
    }

    function runFieldChecks()
    {
        if (empty(self::getFields())) {
            return;
        }

        $data = $this->getData();

        foreach ($data as $field => $value) {
            $def = self::getFields()[$field];

            if (!$def) {
                throw new InvalidFieldError("Field with key $field is not allowed in " . static::class);
            }

            $this->checkConstraints($field, $value);
        }
    }

    function resolveConstraint($constraint)
    {
        $nsp = "App\\Core\\Storage\\Constraint\\";
        try {
            $path = $nsp . ucfirst($constraint) . "Constraint";
            $class = str_replace('/\\\/', '\\', $path);
            return new $class();
        } catch (\Throwable $th) {
            return null;
        }
    }

    function checkConstraints($field, $value)
    {
        $def = self::getFields()[$field];
        unset($def['default']);

        foreach ($def as $constraint => $constraintValue) {
            $constraintClass = ConstraintResolver::resolve($constraint);

            if (!$constraintClass) {
                continue;
            }

            $constraintClass->test($field, $value, $constraintValue, $this);
        }
    }

    function createId()
    {
        return str_replace('.', '', uniqid('', true));
    }

    private function setupStorage()
    {
        $store = self::getStorage();

        if (!is_string($store)) {
            throw new RuntimeException('No storage path has been set for ' . static::class);
        }

        if (!is_dir($store)) {
            mkdir($store, 0770, true);
        }

        if (!is_writable($store)) {
            throw new RuntimeException('Storage for ' . static::class . ' is not writable!');
        }
    }

    protected static function parseFromFile($file): Storable
    {
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $data = json_decode($content, true);
            return new static($data);
        } else {
            throw new \Error('File ' . $file . ' does not exist!');
        }
    }

    static function exists($id = null)
    {
        return file_exists(self::filePath($id));
    }

    static function getByPropertyValue($prop, $value, $single = false): StorableList | Storable | null
    {
        $files = self::getAllFiles();
        $entry = [];

        foreach ($files as $file) {
            $obj = self::parseFromFile($file);
            $data = $obj->getData();
            if (array_key_exists($prop, $data) && $data[$prop] == $value) {
                $entry[] = $obj;
                if ($single) {
                    break;
                }
            }
        }

        if (count($entry) < 1) {
            return null;
        }

        return $single ? $entry[0] : new StorableList($entry);
    }

    static function getAllFiles(): array | false
    {
        return glob(self::getStorage() . '/*.json');
    }


    static function get(array | callable $conditions = []): StorableList
    {
        $list = new StorableList(self::getAll());
        return count($conditions) ? $list->filterBy($conditions) : $list;
    }

    protected static function getAll(): array
    {
        $data = [];

        foreach (self::getAllFiles() as $file) {
            $data[] = self::parseFromFile($file);
        }

        return $data;
    }

    static function getById($id = null)
    {
        return self::parseFromFile(self::filePath($id));
    }

    function save()
    {
        // run checks for field definitions
        $this->runFieldChecks();

        // execute prepare callback
        $this->preSave();

        // encode data 
        $data = json_encode($this->getData());

        // create storage
        $this->setupStorage();

        // save data to file
        file_put_contents(self::filePath($this->_id), $data);

        return $this;
    }

    function getData()
    {
        return $this->data;
    }

    public static function deleteById($id = null)
    {
        if (!self::exists($id)) {
            throw new EntityFoundError("Document with id $id could not be deleted! does not exist!");
        }

        return unlink(self::filePath($id));
    }

    public static function filePath($id)
    {
        return self::getStorage() . '/' . $id . '.json';
    }

    public static function getFields()
    {
        return array_merge(static::$internalFields, static::$fields);
    }

    function jsonSerialize()
    {
        return $this->getData();
    }


    function __set($key = null, $val = null)
    {
        // everything besides array and strings are invalid
        if (!is_array($key) && !is_string($key)) {
            throw new RuntimeException('Invalid key given in ' . static::class . '->set()');
        }

        // array assignments
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->__set($k, $v);
            }

            return $this;
        }

        // only allow fields defined in model
        if (!in_array($key, array_keys(self::getFields()))) {
            return $this;
        }

        // single string key,val
        $this->data[$key] = $val;

        return $this;
    }

    function __get($key = null)
    {
        if (!is_string($key)) {
            throw new RuntimeException('Invalid key given to ' . static::class . '->get(): ' . $key);
        }

        return $this->data[$key] ?? null;
    }

    /**
     * Hook for logic that needs to run before
     * the Storable is saved
     *
     * @return void
     */
    function preSave()
    {
    }

    /**
     * Hook for logic that needs to run before
     * the Storable is created
     *
     * @param array $data the storable data
     * @return void
     */
    static function preCreate(array $data = []): array
    {
        return $data;
    }
}
