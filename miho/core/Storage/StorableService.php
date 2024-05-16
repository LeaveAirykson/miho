<?php

namespace Miho\Core\Storage;

class StorableService implements StorableInterface
{

    static function get($id = null): Storable
    {
        $pathToFile = self::filePath($id);

        if (file_exists($pathToFile)) {
            $content = file_get_contents($pathToFile);
            $data = json_decode($content, true);
            return new Storable($data);
        } else {
            throw new \Error('File ' . $pathToFile . ' does not exist!');
        }
    }

    static function getAll(): StorableList
    {
        $all = [];
        $files = self::getAllFiles();

        foreach ($files as $file) {
            $all[] = self::get($file);
        }

        return new StorableList($all);
    }

    static function getAllFiles(): array | false
    {
        return glob(self::storagePath() . '/*.json');
    }

    static function getByPropertyValue($prop, $value, $single = false): StorableList | Storable | null
    {
        $files = self::getAllFiles();
        $entry = [];

        foreach ($files as $file) {
            $obj = self::get($file);
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

    static function remove($id = null)
    {
        $pathToFile = self::filePath($id);
        return (file_exists($pathToFile)) ? unlink($pathToFile) : false;
    }

    static function filePath($id = null)
    {
        $id = basename($id, '.json');
        return self::storagePath() . '/' . $id . '.json';
    }

    static function storagePath()
    {
        return STORAGE_PATH . "/" . strtolower(self::getModelName());
    }

    static function create($data = [])
    {
        $entity = self::init($data);

        self::checkEntityFields($entity);

        $saved = $entity->save();

        self::updateIndex();

        return $saved;
    }

    // @TODO: sollte in User Model implementiert werden und
    // während dem construct aufgerufen werden
    protected static function checkEntityFields($entity)
    {
        $data = $entity->getData();
        $fields = $entity->getFields();
        $sanit = array_intersect_key($fields, $data);

        foreach ($sanit as $key => $value) {
            $options = $fields[$key];

            // type check
            if (isset($options['type'])) {
                self::checkType($value, $options['type'], $key);
            }

            // constraints check
            if (isset($options['constraints'])) {
                self::checkConstraints($key, $value, $options['constraints']);
            }
        }

        // overwrite raw data with sanitized data
        $entity->_set($sanit);
    }

    protected static function updateIndex()
    {
    }

    protected static function checkType($value, $type, $key)
    {
        if (gettype($value) !== $type) {
            throw new InvalidFieldError(`Invalid data type for $key!`);
        }
    }
    protected static function checkConstraints($key, $value, $constraints)
    {
        foreach ($constraints as $c) {
            if (
                $c === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL) ||
                $c === 'required' && empty($value)
            ) {
                throw new InvalidFieldError(`Constraint $c not met for: $key`);
            }

            // unique constraint
            if ($c === 'unique') {
                $exists = self::getByPropertyValue($key, $value, true);

                if ($exists) {
                    throw new InvalidFieldError(`Constraint $c not met for: $key`);
                }
            }
        }
    }

    static function init($data = [])
    {
        return new (self::getModelNamespacePath())($data);
    }


    static function exists($id)
    {
        return file_exists(self::filePath($id));
    }

    private static function getModelNamespacePath()
    {
        $modelName = self::getModelName();
        return 'Api\\Model\\' . $modelName;
    }

    private static function getModelName()
    {
        $className = (new \ReflectionClass(get_called_class()))->getShortName();
        return preg_replace('/Service$/', '', $className);
    }
}
