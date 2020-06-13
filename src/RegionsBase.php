<?php

namespace Balsama;

use function GuzzleHttp\Psr7\str;

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
     * @param string $nameFragment
     *   Substring of a regions name
     *
     * @return array []region
     */
    public function getAmbiguousRegions($nameFragment)
    {
        $possibleMatches = [];
        foreach ($this->regions as $regionName => $region) {
            /* @var $region region */
            if (strpos(strtolower($regionName), strtolower($nameFragment)) !== false) {
                $possibleMatches[$regionName] = $region;
            }
        }
        return $possibleMatches;
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
            $points = ['cases', 'deaths', 'discharged'];
            foreach ($points as $point) {
                $dataPoints[$point] = $this->isolateDates($rawRegion, $point);
                ksort($dataPoints[$point]);
            }
            $dataPoints['new_cases'] = $this->extractNewCases($rawRegion);
            $fips = $this->findFips($rawRegion);
            $regions[$name] = new Region(
                $name,
                $type,
                $country,
                $population,
                $dataPoints['cases'],
                $dataPoints['deaths'],
                $dataPoints['discharged'],
                $dataPoints['new_cases'],
                $fips,
            );
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
     *   One of "cases", "deaths", or "discharged".
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

    private function extractNewCases($rawRegion)
    {
        $dayCases = [];
        foreach ($rawRegion->dates as $date => $numbers) {
            if (!property_exists($numbers, 'cases')) {
                continue;
            }
            if (empty($previous)) {
                $previous = $numbers->cases;
            }
            $dayCases[strtotime($date)] = ($numbers->cases - $previous);
            $previous = $numbers->cases;
        }
        return $dayCases;
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
