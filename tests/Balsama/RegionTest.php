<?php

namespace Balsama;

use PHPUnit\Framework\TestCase;

class RegionTest extends TestCase
{

    /* @var $region \Balsama\Region */
    private Region $region;

    private string $regionName = 'Caerbannog';
    private string $type = 'state';
    private string $country = 'Antioch';
    private int $population = 100000;
    private array $cumulativeCases = [
        '315532800' => 0,
        '315619200' => 0,
        '315705600' => 1,
        '315792000' => 10,
        '315805600' => 100,
        '315892000' => 90000,
    ];
    private array $cumalitiveDeaths = [
        '315532800' => 0,
        '315619200' => 0,
        '315705600' => 0,
        '315792000' => 1,
        '315805600' => 10,
        '315892000' => 90,
    ];
    private array $recovered = [
        '315532800' => 0,
        '315619200' => 0,
        '315705600' => 0,
        '315792000' => 0,
        '315805600' => 2,
        '315892000' => 7,
    ];
    private array $dayCases = [
        '315792000' => 1,
        '315805600' => 9,
        '315892000' => 998,
    ];

    public function setUp(): void
    {
        $this->createMockRegion();
        parent::setUp();
    }

    public function testGetName()
    {
        $name = $this->region->getName();
        $this->assertEquals($this->regionName, $name);
    }

    public function testGetCases()
    {
        $cases = $this->region->getCumulativeCases();
        $this->assertEquals($this->cumulativeCases, $cases);
    }

    public function testGetDeaths()
    {
        $deaths = $this->region->getCumalitiveDeaths();
        $this->assertEquals($this->cumalitiveDeaths, $deaths);
    }

    public function testGetRecovered()
    {
        $recovered = $this->region->getDischarged();
        $this->assertEquals($this->recovered, $recovered);
    }

    public function testGetPercentages()
    {
        $casesPercentages = $this->region->getPercentages();
        $this->assertEquals(
            [
                '315532800' => 0.0000, // 0 / 100,000 * 100
                '315619200' => 0.0000, // 0 / 100,000 * 100
                '315705600' => 0.0010, // 1 / 100,000 * 100
                '315792000' => 0.0100, // 10 / 100,000 * 100
                '315805600' => 0.1000, // 100 / 100,000 * 100
                '315892000' => 90.0000, // 90,000 / 100,000 * 100
            ],
            $casesPercentages
        );
    }

    public function testGetPercentagesDeaths()
    {
        $deathsPercentages = $this->region->getPercentages('deaths');
        $this->assertEquals(
            [
                '315532800' => 0.0000, // 0 / 100,000 * 100
                '315619200' => 0.0000, // 0 / 100,000 * 100
                '315705600' => 0.0000, // 0 / 100,000 * 100
                '315792000' => 0.0010, // 1 / 100,000 * 100
                '315805600' => 0.0100, // 10 / 100,000 * 100
                '315892000' => 0.0900, // 90 / 100,000 * 100
            ],
            $deathsPercentages
        );
    }

    public function testGetPer100kAboveN()
    {
        $datesPer100kAbove0 = $this->region->getPer100kAboveN(0);

        $this->assertIsArray($datesPer100kAbove0);
        $this->assertCount(4, $datesPer100kAbove0);

        $datesPer100kAbove9 = $this->region->getPer100kAboveN(9);
        $this->assertCount(3, $datesPer100kAbove9);
    }

    public function testGetLatest()
    {
        $latest = $this->region->getLatestCount();
        $this->assertEquals(end($this->cumulativeCases), $latest);
    }

    public function testGetDayCases()
    {
        $dayCases = $this->region->getDayCases();
        $this->assertEquals($this->dayCases, $dayCases);
    }

    private function createMockRegion()
    {
        $this->region = new Region(
            $this->regionName,
            $this->type,
            $this->country,
            $this->population,
            $this->cumulativeCases,
            $this->cumalitiveDeaths,
            $this->recovered,
            $this->dayCases,
        );
    }
}
