<?php

namespace Balsama;

class RegionsBase
{

    /* @var $clientBase ClientBase */
    private $clientBase;

    /* @var $regions Region[] */
    private $regions;

    public function __construct()
    {
        $this->clientBase = new ClientBase();
        $this->setRegions();
    }

    /**
     * Get's all of the RegionsBase provided in timeseries-byLocation.json.
     * @return Region[]
     */
    public function getRegions() {
        return $this->regions;
    }

    /**
     * @param $regionName
     * @return Region
     */
    public function getRegion($regionName) {
        return $this->regions[$regionName];
    }

    /**
     * Get's all of a given country's state RegionsBase.
     * @param $country string
     *   The three-character region country code.
     * @return Region[]
     */
    public function getCountrysStates($country) {
        $countryStates = [];
        foreach ($this->regions as $region) {
            if (($country == $region->getCountry()) && ($region->getType() == 'state')) {
                $countryStates[$region->getName()] = $region;
            }
        }
        return $countryStates;
    }

    /**
     * Creates a new Region object for each region provided in the timeseries-byLocation.json file.
     */
    private function setRegions() {
        $regions = [];
        $rawRegions = $this->clientBase->getAllRawData();
        foreach ($rawRegions as $name => $rawRegion) {
            $name = str_replace(',', '/', $name);
            $type = $this->getTypeFromRegion($rawRegion, $name);
            $country = $rawRegion->country;
            if (!property_exists($rawRegion, 'population')) {
                continue;
            }
            $population = $rawRegion->population;
            $cases = $this->isolateDates($rawRegion, 'cases');
            ksort($cases);
            $deaths = $this->isolateDates($rawRegion, 'deaths');
            ksort($deaths);
            $regions[$name] = new Region($name, $type, $country, $population, $cases, $deaths);
        }
        $this->regions = $regions;
    }

    /**
     * @param $rawRegion
     *   A raw regions object from the coronadatascrapter.com timeseries-byLocation.json file.
     * @param $name
     * @return string
     */
    private function getTypeFromRegion($rawRegion, $name){
        if (property_exists($rawRegion, 'city')) {
            return 'city';
        }
        if (property_exists($rawRegion, 'county')) {
            return 'county';
        }
        if (property_exists($rawRegion, 'state')) {
            return 'state';
        }
        if (strlen($name) == 3) {
            return 'country';
        }
        return 'unknown';
    }

    /**
     * @param $region
     * @param string $type
     *   One of "cases", "deaths", or "recovered".
     * @return mixed
     */
    protected function isolateDates($region, $type) {
        foreach ($region->dates as $date => $numbers) {
            if (!property_exists($numbers, $type)) {
                $cases = 0;
            }
            else {
                $cases = $numbers->$type;
            }
            $timestamp = strtotime($date);
            $dates[$timestamp] = $cases;
        }
        return $dates;
    }

}