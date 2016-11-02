<?php

namespace DiscoveryStats;

use Exception;
use PDO;

class Mysql {
    /**
     *
     */
    public static function connect( $config, $host ) {
        $ini = parse_ini_file( $config );

        if ( !$ini ) {
            throw new Exception( "Error opening mysql config $config" );
        }

        return new PDO( "mysql:host=$host",
            $ini['user'],
            $ini['password']
        );
    }
}
