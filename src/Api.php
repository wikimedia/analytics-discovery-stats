<?php

namespace DiscoveryStats;

class Api {
    public static function get( $url, $params ) {
        $params['format'] = 'json';
        $params['formatversion'] = 2;

        return json_decode( file_get_contents( "{$url}/w/api.php?" . http_build_query( $params ) ) );
    }
}

ini_set( 'user_agent', 'Discovery team statistics' );
