<?php

namespace Tsterker\Flextable;

trait IsFlextable
{
    protected static $fakeConnection;

    public function getConnectionName()
    {
        return static::$fakeConnection ?? $this->connection;
    }

    public static function setDefaultConnection($connectionName)
    {
        static::$fakeConnection = $connectionName;
    }

    public static function clearDefaultConnection()
    {
        static::$fakeConnection = null;
    }

    public static function on($connection = null)
    {
        if (static::$fakeConnection && static::$fakeConnection !== $connection) {
            throw new \RuntimeException("Cannot change connection when faking.");
        }

        return parent::on($connection);
    }
}
