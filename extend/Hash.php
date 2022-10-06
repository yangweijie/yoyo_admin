<?php
class Hash
{
    protected static $handle = [];

    public static function make($value, $type = null, array $options = [])
    {
        return self::handle($type)->make($value, $options);
    }

    public static function check($value, $hashedValue, $type = null, array $options = [])
    {
        return self::handle($type)->check($value, $hashedValue, $options);
    }

    public static function handle($type)
    {
        if (is_null($type)) {
            if (PHP_VERSION_ID >= 50500) {
                $type = 'bcrypt';
            } else {
                $type = 'md5';
            }
        }
        if (empty(self::$handle[$type])) {
            $class = "\\hash\\" . ucfirst($type);
            if (!class_exists($class)) {
                throw new \ErrorException("Not found {$type} hash type!");
            }
            self::$handle[$type] = new $class();
        }
        return self::$handle[$type];
    }

}