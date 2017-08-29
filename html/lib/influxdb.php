<?php

define(__ROOT__, __DIR__ . "/..");

require_once __ROOT__ . '/vendor/autoload.php';

class InfluxClient {

    function reset_points() {
        $this->points = array();
    }

    function __construct($dsn, $retention) {
        $this->database = \InfluxDB\Client::fromDSN($dsn);
        $this->reset_points();
    }

    function null_to_str($input) {
        if (empty($input))
            return "null";
        else
            return $input;
    }

    function add($path, $name, $value, $tags, $metrics) {
        $this->points[] = new InfluxDB\Point(
            $path . $this->null_to_str($name),
            $value, $tags, $metrics,
            date('U') * 1000000000 // Fake nanosecond precision
        );

        return $this;
    }

    function write() {
        $this->database->writePoints(
            $this->points
        );
    }

}

?>
