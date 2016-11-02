<?php

namespace DiscoveryStats;

require_once( __DIR__ . '/vendor/autoload.php' );

$config = json_decode( file_get_contents( __DIR__ . '/config.json' ) );
$wikiBlacklist = [
    'labswiki',
    'labtestwiki',
];

$matrix = new SiteMatrix();
$db = Mysql::connect( '/etc/mysql/conf.d/analytics-research-client.cnf',
    'analytics-store.eqiad.wmnet'
);
$graphite = new Graphite( $config );

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

    $res = query( 'SELECT count(*) AS num FROM geo_tags WHERE gt_primary=1' );
    if ( $res && ( $row = $res->fetch() ) ) {
        $graphite->record( "geodata.pages.$siteKey.hourly", $row['num'] );
    }

    $ns = isset( $config->geoCoordinates->contentNamespaces->$dbName )
        ? $config->geoCoordinates->contentNamespaces->$dbName
        : $config->geoCoordinates->contentNamespaces->default;
    $ns = implode( ', ', $ns );
    $res = query( 'SELECT count(*) AS num FROM geo_tags, page WHERE page_id=gt_page_id '
        . "AND page_namespace IN ($ns) AND gt_primary=1"
    );
    if ( $res && ( $row = $res->fetch() ) ) {
        $graphite->record( "geodata.content.$siteKey.hourly", $row['num'] );
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
