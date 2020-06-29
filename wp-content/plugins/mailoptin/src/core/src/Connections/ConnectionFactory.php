<?php

namespace MailOptin\Core\Connections;

class ConnectionFactory
{
    /**
     * Return instance of a connection object.
     *
     * @param string $connection
     *
     * @return ConnectionInterface
     */
    public static function make($connection)
    {
        /** @var ConnectionInterface $connectClass */
        $connectClass = self::get_fqn_class($connection);

        return $connectClass::get_instance();
    }

    /**
     * @param $connection
     *
     * @return ConnectionInterface|string
     */
    public static function get_fqn_class($connection)
    {
        return "MailOptin\\$connection\\Connect";
    }

}