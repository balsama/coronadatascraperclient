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
     *
     * @return Region[]
     */
    public function getRegions()
    {
        return $this->regions;
    }

    /**
     * @param  $regionName
     * @return Region
     */
    public function getRegion($regionName)
    {
        return $this->regions[$regionName];
    }

    /**
     * Get's all of a given country's state RegionsBase.
     *
     * @param  $country string
     *                  The three-character region country code.
     * @return Region[]
     */
    public function getCountrysStates($country)
    {
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
    private function setRegions()
    {
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
            $fips = $this->findFips($rawRegion);
            $regions[$name] = new Region($name, $type, $country, $population, $cases, $deaths, $fips);
        }
        $this->regions = $regions;
    }

    /**
     * @param  $rawRegion
     *   A raw regions object from the coronadatascrapter.com timeseries-byLocation.json file.
     * @param  $name
     * @return string
     */
    private function getTypeFromRegion($rawRegion, $name)
    {
        return $rawRegion->level;
    }

    /**
     * @param  $region
     * @param  string $type
     *   One of "cases", "deaths", or "recovered".
     * @return mixed
     */
    protected function isolateDates($region, $type)
    {
        foreach ($region->dates as $date => $numbers) {
            if (!property_exists($numbers, $type)) {
                $cases = 0;
            } else {
                $cases = $numbers->$type;
            }
            $timestamp = strtotime($date);
            $dates[$timestamp] = $cases;
        }
        return $dates;
    }

    /**
     * Finds and returns a US county FIPS code if it exists in the raw region data.
     *
     * @param  $rawRegion
     * @return false|string
     */
    protected function findFips($rawRegion)
    {
        if ($rawRegion->level == 'county') {
            if (strpos($rawRegion->countyId, 'fips:') === 0) {
                return substr($rawRegion->countyId, 5);
            }
        }
        return '';
    }
}
