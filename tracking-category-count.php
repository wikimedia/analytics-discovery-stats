<?php

namespace DiscoveryStats;

require_once( 'vendor/autoload.php' );

$wikiBlacklist = [
    'ukwikimedia', // redirected
];

$debug = in_array( '--debug', $argv );

$config = json_decode( file_get_contents( __DIR__ . '/config.json' ) );
$config->categories = (array)$config->categories;
$categoryKeys = array_keys( $config->categories );

function recordToGraphite( $wiki, $metric, $count ) {
    global $config, $debug;
    static $timestamp;

    if ( !$config->graphiteHost || !$config->graphitePort ) {
        return;
    }
    if ( !$timestamp ) {
        $timestamp = time();
    }


    $key = str_replace( '%WIKI%', $wiki, $config->categories[$metric] );
    $packet = "$key $count $timestamp";
    $nc = "nc -q0 {$config->graphiteHost} {$config->graphitePort}";
    $command = "echo \"$packet\" | $nc";

    if ( $debug ) {
        echo "$command\n";
    }
    exec( $command );
}

$matrix = new SiteMatrix;

$totalCounts = array_fill_keys( $categoryKeys, 0 );
foreach ( $matrix->getSites() as $site ) {
    if ( $site->isPrivate() || in_array( $site->getDbName(), $wikiBlacklist ) ) {
        continue;
    }
    $siteKey = $site->getFamily() . '.' . $site->getCode();
    $tracking = new TrackingCategories( $site );

    $counts = $tracking->getCounts( $categoryKeys );
    foreach ( $counts as $metric => $count ) {
        $totalCounts[$metric] += $count;
        recordToGraphite( $siteKey, $metric, $count );
    }
    if ( $debug ) {
        echo "{$site->getDbName()} "; var_dump($counts);
    }
}

foreach ( $totalCounts as $metric => $count ) {
    recordToGraphite( 'total', $metric, $count );
}

if ( $debug ) {
    var_dump($totalCounts);
}
