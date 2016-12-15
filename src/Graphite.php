<?php

namespace DiscoveryStats;

class Graphite {
    /** @var string */
    private $host;
    /** @var int */
    private $port;
    /* @var int */
    private $timestamp;

    public function __construct( $config ) {
        $this->host = $config->graphiteHost;
        $this->port = $config->graphitePort;
        $this->timestamp = time();
    }

    public function record( $metric, $value, $timestamp = null ) {
        if ( $timestamp === null ) {
            $timestamp = $this->timestamp;
        }
        $packet = "{$metric} {$value} {$timestamp}";
        $nc = "nc -q0 {$this->host} {$this->port}";
        $command = "echo \"$packet\" | $nc";

        exec( $command );
    }
}
