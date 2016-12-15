<?php

namespace DiscoveryStats;

require_once( __DIR__ . '/vendor/autoload.php' );

$config = json_decode( file_get_contents( __DIR__ . '/config.json' ) );
$wikiBlacklist = [
    'labswiki',
    'labtestwiki',
];

$matrix = new SiteMatrix();
$db = Mysql::connect( '/etc/mysql/conf.d/discovery-stats-client.cnf',
    'analytics-store.eqiad.wmnet'
);
$graphite = new Graphite( $config );

// Start of today
$timestamp = mktime( 0, 0, 0 );

foreach ( $matrix->getSites() as $site ) {
    $dbName = $site->getDbName();
    // Can't quote it, have to validate
    if ( !preg_match( '/^[a-z0-9_]+$/', $dbName ) ) {
        throw new \Exception( "Invalid database '$dbName'" );
    }
    if ( $site->isPrivate() || in_array( $dbName, $wikiBlacklist ) ) {
        continue;
    }

    query( "USE $dbName" );
    $siteKey = $site->getFamily() . '.' . $site->getCode();

    $res = query( "SELECT count(*) AS num FROM page_props WHERE pp_propname='jsonconfig_getdata'" );
    if ( $res && ( $row = $res->fetch() ) && $row['num'] ) {
        $graphite->record( "daily.structured-data.client.pagecount.$siteKey",
            $row['num'],
            $timestamp
        );
    }
}

function query( $sql ) {
    global $db;

    $res = $db->query( $sql );
    if ( !$res ) {
        $err = $db->errorInfo();
        throw new \Exception( "{$err[0]}: {$err[2]}" );
    }

    return $res;
}
