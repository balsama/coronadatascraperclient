<?php

namespace Balsama;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

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
        $usStates = $this->regions->getCountrysStates('United States');

        $this->assertIsArray($usStates);
        // 56 == 50 states + Puerto Rico, Virgin Islands, Guam, American Samoa, and Northern Mariana Islands.
        $this->assertCount(56, $usStates);
        foreach ($usStates as $region) {
            $this->assertInstanceOf('\Balsama\Region', $region);
        }
    }

    public function testFindFips() {
        $rawRegion = [
            'level' => 'county',
            'countyId' => 'fips:01234',
        ];
        $fips = $this->invokeMethod($this->regions, 'findFips', [(object) $rawRegion]);
        $this->assertEquals('01234', $fips);

        $rawRegion = [
            'level' => 'county',
            'countyId' => 'iso:01234',
        ];
        $fips = $this->invokeMethod($this->regions, 'findFips', [(object) $rawRegion]);
        $this->assertEmpty($fips);
    }

    /**
     * Invokes an object's private method.
     * @param $object
     *   The object to instantiate.
     * @param $methodName
     *   The methos to invoke.
     * @param array $parameters
     * @return mixed
     *   Param to pass to the method.
     * @throws \ReflectionException
     */
    public function invokeMethod(&$object, $methodName, array $parameters = []) {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

}