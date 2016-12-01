<?php

namespace DiscoveryStats;

require_once( __DIR__ . '/vendor/autoload.php' );

$matrix = new SiteMatrix();

$file = "all: all\n";
foreach ( $matrix->getSites() as $dbname => $site ) {
    $file .= "{$dbname}: {$site->getFamily()}.{$site->getCode()}\n";
}

file_put_contents( __DIR__ . '/interactive/sitematrix.yaml', $file );
