<?php

namespace Balsama;

use DateTime;
use Exception;

class Region
{
    protected string $name;
    protected string $type;
    protected string $country;
    protected int $population;
    protected array $cumulativeCases;
    protected array $cumulativeDeaths;
    protected array $discharged;
    protected array $dayCases;
    protected string $fips;

    public function __construct(
        string $name,
        string $type,
        string $country,
        int $population,
        array $cumulativeCases,
        array $cumulativeDeaths,
        array $discharged,
        array $dayCases,
        string $fips = ''
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->country = $country;
        $this->population = $population;
        $this->validateCounts($cumulativeCases);
        $this->cumulativeCases = $cumulativeCases;
        $this->validateCounts($cumulativeDeaths);
        $this->cumulativeDeaths = $cumulativeDeaths;
        $this->validateCounts($discharged);
        $this->discharged = $discharged;
        $this->validateCounts($dayCases);
        $this->dayCases = $dayCases;
        $this->fips = $fips;
    }

    /**
     * @return string
     *   The name of the Region (city, county, state, or country).
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int[]
     *   An array of positive tests counts keyed by the timestamp of the day.
     */
    public function getCumulativeCases()
    {
        return $this->cumulativeCases;
    }

    /**
     * @return int[]
     *   An array of new positive test counts each day keyed by the timestamp of the day.
     */
    public function getDayCases()
    {
        return $this->dayCases;
    }

    /**
     * @return int[]
     *   An array of death counts keyed by the timestamp of the day.
     */
    public function getCumulativeDeaths()
    {
        return $this->cumulativeDeaths;
    }

    /**
     * @return int[]
     *   An array of Discharged counts keyed by the timestamp of the day.
     */
    public function getDischarged()
    {
        return $this->discharged;
    }

    /**
     * @return string
     *   The FIPS code for the region if available.
     */
    public function getFips()
    {
        return $this->fips;
    }

    /**
     * @param  string $type
     *   One of 'cumulativeCases' or 'cumulativeDeaths'.
     * @return float[]
     *   An array of the percentages of the population that had tested positive keyed by the timestamp of the day.
     */
    public function getPercentages($type = 'cumulativeCases')
    {
        $percentages = [];
        foreach ($this->$type as $timestamp => $count) {
            $percentages[$timestamp] = number_format(
                ($count / $this->population) * 100,
                10,
                '.',
                ''
            );
        }
        return $percentages;
    }

    /**
     * @return int
     *   The population of the region.
     */
    public function getPopulation()
    {
        return $this->population;
    }

    /**
     * @param  $n
     *   The minimum number of people infected per 100k for a day to be returned.
     * @return float[]
     *   An array of the number of people infected per 100k of the populations above `$n` per day keyed by the timestamp
     *   of the day.
     */
    public function getPer100kAboveN($n)
    {
        $per100kAboveN = [];
        foreach ($this->cumulativeCases as $timestamp => $count) {
            $per100k = number_format(($count / $this->population) * 100000);
            if ($per100k > $n) {
                $per100kAboveN[$timestamp] = $per100k;
            }
        }
        return $per100kAboveN;
    }

    /**
     * @return string
     *   The type of the region (city, county, state, or country)
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return integer
     *   The most recent count for the region.
     */
    public function getLatestCount()
    {
        return end($this->cumulativeCases);
    }

    /**
     * @return integer
     *   The most recent death count for the region.
     */
    public function getLatestDeaths()
    {
        return end($this->cumalitiveDeaths);
    }

    /**
     * @return string
     *   The country that that region is in, or the country that the region represents if the region is a country.
     */
    public function getCountry()
    {
        return $this->country;
    }

    private function validateCounts($counts)
    {
        if (!is_array($counts)) {
            throw new \InvalidArgumentException('$counts must be an array.');
        }
        if (count($counts) == 1) {
            return;
        }
        foreach ($counts as $timestamp => $dateCount) {
            if (!$this->isTimestamp($timestamp)) {
                throw new \InvalidArgumentException('$counts array must be keyed with timestamps.');
            }
            if (!is_int($dateCount)) {
                throw new \InvalidArgumentException('$counts array values must be integers.');
            }
        }
    }

    /**
     * @param  string $string
     * @return bool
     */
    private function isTimestamp($string)
    {
        try {
            new DateTime('@' . $string);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}
