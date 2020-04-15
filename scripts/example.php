#!/usr/bin/env php
<?php

include_once 'vendor/autoload.php';

use Balsama\RegionsBase;
use Balsama\Utilities;

$regions = new RegionsBase();

/**
 * @example Gather the latest numbers for US States.
 */
$usStates = $regions->getCountrysStates('USA');

$latestUsStateCounts = [];
foreach ($usStates as $stateName => $state) {
    $latestUsStateCounts[$stateName] = $state->getLatestCount();
}
print_r($latestUsStateCounts);


/**
 * @example Gather the cases per 100k for US States since reaching 10 in 100k.
 */
$usStates = $regions->getCountrysStates('USA');
$per100k = [];
foreach ($usStates as $stateName => $state) {
    $per100k[$stateName] = $state->getPer100kAboveN(10);
}
print_r($per100k);

/**
 * @example Export csv to a CSV suitable for graphing.
 */
$usStates = $regions->getCountrysStates('USA');
$counts = [];
$per100k = [];
foreach ($usStates as $stateName => $state) {
    $per100k[$stateName] = $state->getPer100kAboveN(1);
}
// writeCsvTableFromData() expects an multidimensional array keyed by region name, each containing an array of counts.
Utilities::writeCsvTableFromData($per100k, 'per100k', true);
