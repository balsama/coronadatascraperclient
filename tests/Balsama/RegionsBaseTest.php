<?php

namespace Balsama;

use PHPUnit\Framework\TestCase;

class RegionsBaseTest extends TestCase
{

    /* @var $clientBase \Balsama\ClientBase */
    private $clientBase;

    /* @var $regions \Balsama\RegionsBase */
    private $regions;

    public function setUp(): void {
        $this->clientBase = new ClientBase();
        $this->regions = new RegionsBase();
        parent::setUp();
    }

    public function testGetRegions() {
        $regions = $this->regions->getRegions();

        $this->assertIsArray($regions);
        foreach ($regions as $region) {
            $this->assertInstanceOf('\Balsama\Region', $region);
        }
    }

    public function testGetCountriesStates() {
        $usStates = $this->regions->getCountrysStates('USA');

        $this->assertIsArray($usStates);
        // 51 States including Puerto Rico. Not sure if this is likely to change if Guam, for example, is added.
        $this->assertCount(51, $usStates);
        foreach ($usStates as $region) {
            $this->assertInstanceOf('\Balsama\Region', $region);
        }
    }


}