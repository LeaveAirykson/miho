<?php

namespace App\Core\Storage;

use App\Core\Utility\Logger;
use App\Core\Utility\Operator;
use Error;
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

    public function __construct($data = [])
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

    public function setDefaults()
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
        $entity = new static($data);
        $saved = $entity->save();

        return $saved;
    }

    public static function runFieldChecks(array $data = [])
    {
        if (empty(self::getFields())) {
            return;
        }

        if (empty($data)) {
            throw new InvalidFieldError('Empty data passed to runFieldChecks: ' . static::class);
        }

        foreach ($data as $key => $value) {
            $def = self::getFields()[$key];

            if (!$def) {
                throw new InvalidFieldError("Field with key $key is not allowed in " . static::class);
            }

            if (isset($def['type'])) {
                self::checkType($key, $value, $def['type']);
            }

            if (isset($def['constraints'])) {
                self::checkConstraints($key, $value, $def['constraints']);
            }
        }
    }

    public static function checkType($key, $value, $type)
    {
        if (gettype($value) !== $type) {
            $t = gettype($value);
            throw new InvalidFieldError("Invalid data type $key. is: $t needs: $type!");
        }
    }
    public static function checkConstraints($key, $value, $constraints)
    {
        foreach ($constraints as $c) {

            if ($c === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidFieldError("Invalid email address: $value");
            }

            if ($c === 'required' && empty($value)) {
                throw new InvalidFieldError("Constraint $c not met for: $key");
            }

            // unique constraint
            if ($c === 'unique') {
                $exists = self::getByPropertyValue($key, $value, true);

                if ($exists) {
                    throw new InvalidFieldError("Constraint $c not met for: $key");
                }
            }
        }
    }

    public function createId()
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
            mkdir($store, 0755, true);
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

    // @TODO: implement conditions
    static function get(array | callable $conditions = []): StorableList
    {
        $data = self::getAll();

        if (count($conditions)) {
            $data = self::filterByConditions($conditions, $data);
        }

        return new StorableList($data);
    }

    protected static function filterByConditions($conditions = [], $data = [])
    {
        return array_filter($data, function ($v) use ($conditions) {
            foreach ($conditions as $key => $value) {
                if ($v->{$key} !== $value) {
                    return false;
                }
            }

            return true;
        });
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

    public function save()
    {
        // run checks for field definitions
        self::runFieldChecks($this->getData());

        // execute prepare callback
        $this->prepare();

        // encode data 
        $data = json_encode($this->getData());

        // create storage
        $this->setupStorage();

        // save data to file
        file_put_contents(self::filePath($this->_id), $data);

        return $this;
    }

    public function prepare()
    {
    }

    public function getData()
    {
        return $this->data;
    }

    public static function deleteById($id = null)
    {
        if (!self::exists($id)) {
            throw new Error("Document with id $id could not be deleted! does not exist!");
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

    public function jsonSerialize()
    {
        return $this->getData();
    }


    public function __set($key = null, $val = null)
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

        if (!in_array($key, array_keys(self::getFields()))) {
            return $this;
        }

        // single string key,val
        $this->data[$key] = $val;

        return $this;
    }

    public function __get($key = null)
    {
        if (!is_string($key)) {
            throw new RuntimeException('Invalid key given to ' . static::class . '->get(): ' . $key);
        }

        return $this->data[$key] ?? null;
    }
}
