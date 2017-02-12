<?php

require_once __DIR__ . '/../../../autoload.php';

use PHPUnit\Framework\TestCase;
use Cewi\Checkers\ZipChecker;

/**
 * SaluatationCheckerTest
 *
 * @author cewi <c.wichmann@gmx.de>
 */
class ZipCheckerTest extends TestCase
{

    public $checker;

    /**
     * setUp method
     * initialize checker
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->checker = new ZipChecker([]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->assertInstanceOf('Cewi\Checkers\ZipChecker', $this->checker);
    }

    /**
     * test if countries as found correctly
     *
     */
    public function testCheck()
    {

        $data = [
            'Frau Erika Mustermann Musterweg 8 88888 Musterstadt',
            'Frau Erika Mustermann Musterweg 8 8888 Musterstadt', // not five digits
            'Frau Erika Mustermann Musterweg 8 Musterstadt', // no zip
            'Frau Erika Mustermann Musterweg 8 888A8 Musterstadt', // broken zip
            'Frau Erika Mustermann Musterweg 688888 Musterstadt', // six digits, last five will be the zip
            'Frau Erika Mustermann Musterweg 4a88888 Musterstadt', // last five digits will be the zip
            'Frau Erika Mustermann Musterweg 8 05112 Musterstadt', // german zips may not start with 05
            'Frau Erika Mustermann Musterweg 8 43112 Musterstadt', // german zips may not start with 43
            'Frau Erika Mustermann Musterweg 8 62112 Musterstadt', // german zips may not start with 62
        ];
        $expected = [
            ['zip' => 88888, 'city' => 'Musterstadt', 'name' => 'Frau Erika Mustermann Musterweg 8'],
            ['zip' => null, 'city' => null, 'name' => 'Frau Erika Mustermann Musterweg 8 8888 Musterstadt'],
            ['zip' => null, 'city' => null, 'name' => 'Frau Erika Mustermann Musterweg 8 Musterstadt'],
            ['zip' => null, 'city' => null, 'name' => 'Frau Erika Mustermann Musterweg 8 888A8 Musterstadt'],
            ['zip' => 88888, 'city' => 'Musterstadt', 'name' => 'Frau Erika Mustermann Musterweg 6'],
            ['zip' => 88888, 'city' => 'Musterstadt', 'name' => 'Frau Erika Mustermann Musterweg 4a'],
            ['zip' => null, 'city' => null, 'name' => 'Frau Erika Mustermann Musterweg 8 05112 Musterstadt'],
            ['zip' => null, 'city' => null, 'name' => 'Frau Erika Mustermann Musterweg 8 43112 Musterstadt'],
            ['zip' => null, 'city' => null, 'name' => 'Frau Erika Mustermann Musterweg 8 62112 Musterstadt'],
        ];
        $results = [true, false, false, false, true, true, false, false, false];
        foreach ($data as $key => $address) {
            $result = $this->checker->check(['name' => $address]);
            $this->assertEquals($results[$key], $result);
            $newAddress = $this->checker->getAddress();
            if ($result) {
                $this->assertEquals($expected[$key]['zip'], $newAddress['zip']);
                $this->assertEquals($expected[$key]['city'], $newAddress['city']);
            }
            $this->assertEquals($expected[$key]['name'], $newAddress['name']);
        }
    }

}
