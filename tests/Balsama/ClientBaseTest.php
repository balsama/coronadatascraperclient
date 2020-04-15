<?php

namespace Balsama;

use PHPUnit\Framework\TestCase;

class ClientBaseTest extends TestCase
{
    protected ClientBase $clientBase;

    public function setUp(): void
    {
        $this->clientBase = new ClientBase();
        parent::setUp();
    }

    public function testGetAllRawData()
    {
        $rawData = $this->clientBase->getAllRawData();
        $this->assertGreaterThan(3900, (array) $rawData);
    }
}
