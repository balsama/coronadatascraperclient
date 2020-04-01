<?php

namespace Balsama;

use PHPUnit\Framework\TestCase;

class RegionTest extends TestCase
{

    /* @var $region \Balsama\Region */
    private Region $region;

    private $regionName = 'Caerbannog';
    private $type = 'state';
    private $country = 'Antioch';
    private $population = 100000;
    private $datesCount = [
        '315532800' => 0,
        '315619200' => 0,
        '315705600' => 1,
        '315792000' => 10,
        '315805600' => 100,
        '315892000' => 90000,
    ];

    public function setUp(): void
    {
        $this->createMockRegion();
        parent::setUp();
    }

    public function testGetName() {
        $name = $this->region->getName();
        $this->assertEquals($this->regionName, $name);
    }

    public function testGetCounts() {
        $datesCount = $this->region->getCounts();
        $this->assertEquals($this->datesCount, $datesCount);
    }

    public function testGetPercentages() {
        $datesPercentage = $this->region->getPercentages();
        $this->assertEquals(
            [
                '315532800' => 0.0000, // 0 / 100,000 * 100
                '315619200' => 0.0000, // 0 / 100,000 * 100
                '315705600' => 0.0010, // 1 / 100,000 * 100
                '315792000' => 0.0100, // 10 / 100,000 * 100
                '315805600' => 0.1000, // 100 / 100,000 * 100
                '315892000' => 90.0000, // 90,000 / 100,000 * 100
            ],
            $datesPercentage);
    }

    public function testGetPer100kAboveN() {
        $datesPer100kAbove0 = $this->region->getPer100kAboveN(0);

        $this->assertIsArray($datesPer100kAbove0);
        $this->assertCount(4, $datesPer100kAbove0);

        $datesPer100kAbove9 = $this->region->getPer100kAboveN(9);
        $this->assertCount(3, $datesPer100kAbove9);
    }

    public function testGetLatest() {
        $latest = $this->region->getLatestCount();
        $this->assertEquals(end($this->datesCount), $latest);
    }

    private function createMockRegion() {
        $this->region = new Region($this->regionName, $this->type, $this->country, $this->population, $this->datesCount);
    }

}
