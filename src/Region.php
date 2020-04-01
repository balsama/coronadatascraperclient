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
    protected array $counts;

    public function __construct(string $name, string $type, string $country, int $population, array $datesCount) {
        $this->name = $name;
        $this->type = $type;
        $this->country = $country;
        $this->population = $population;
        $this->validateCounts($datesCount);
        $this->counts = $datesCount;
    }

    /**
     * @return string
     *   The name of the Region (city, county, state, or country).
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return int[]
     *   An array of positive tests counts keyed by the timestamp of the day.
     */
    public function getCounts() {
        return $this->counts;
    }

    /**
     * @return float[]
     *   An array of the percentages of the population that had tested positive keyed by the timestamp of the day.
     */
    public function getPercentages() {
        $percentages = [];
        foreach ($this->counts as $timestamp => $count) {
            $percentages[$timestamp] = number_format(
                ($count / $this->population) * 100, 4, '.', ''
            );
        }
        return $percentages;
    }

    /**
     * @param $n
     *   The minimum number of people infected per 100k for a day to be returned.
     * @return float[]
     *   An array of the number of people infected per 100k of the populations above `$n` per day keyed by the timestamp
     *   of the day.
     */
    public function getPer100kAboveN($n) {
        $per100kAboveN = [];
        foreach ($this->counts as $timestamp => $count) {
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
    public function getType() {
        return $this->type;
    }

    /**
     * @return integer
     *   The most recent count for the region.
     */
    public function getLatestCount() {
        return end($this->counts);
    }

    /**
     * @return string
     *   The country that that region is in, or the country that the region represents if the region is a country.
     */
    public function getCountry() {
        return $this->country;
    }

    private function validateCounts($counts) {
        if (!is_array($counts)) {
            throw new \InvalidArgumentException('$counts must be an array.');
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
     * @param string $string
     * @return bool
     */
    private function isTimestamp($string) {
        try {
            new DateTime('@' . $string);
        } catch(Exception $e) {
            return false;
        }
        return true;
    }

}