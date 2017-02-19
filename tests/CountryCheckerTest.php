<?php

require_once __DIR__.'/../../../autoload.php';

use PHPUnit\Framework\TestCase;
use Cewi\Checkers\CountryChecker;

/**
 * SaluatationCheckerTest.
 *
 * @author cewi <c.wichmann@gmx.de>
 */
class CountryCheckerTest extends TestCase
{
    public $checker;

    /**
     * setUp method
     * initialize checker.
     */
    public function setUp()
    {
        parent::setUp();

        $options = [
            'countries' => [
                0 => [
                    'id' => 1,
                    'name' => 'Deutschland',
                    'prefix' => 'D',
                ],
                1 => [
                    'id' => 2,
                    'name' => 'Frankreich',
                    'prefix' => 'F',
                ],
                2 => [
                    'id' => 3,
                    'name' => 'Italien',
                    'prefix' => 'I',
                ],
                3 => [
                    'id' => 4,
                    'name' => 'Schweiz',
                    'prefix' => 'CH',
                ],
            ],
            'homeCountry' => [
                'id' => 1,
                'name' => 'Deutschland',
                'prefix' => 'D',
            ],
        ];

        $this->checker = new CountryChecker($options);
    }

    /**
     * tearDown method.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Test initial setup.
     */
    public function testInitialization()
    {
        $this->assertInstanceOf('Cewi\Checkers\CountryChecker', $this->checker);
    }

    /**
     * test if countries as found correctly.
     */
    public function testIsDeliverable()
    {
        $data = [
            'Frau Erika Mustermann Testweg 777 88888 Musterstadt Deutschland',
            'Frau Erika Mustermann Testweg 777 88888 Musterstadt ITALIEN', //uppercase is ok
            'Frau Erika Mustermann Testweg 777 88888 Musterstadt Frankreich',
            'Frau Erika Mustermann Testweg 777 88888 Musterstadt', // no valid country name found
            'Frau Erika Mustermann Testweg 777 I-88888 Musterstadt',
            'Frau Erika Mustermann Testweg 777 D -88888 Musterstadt',
            'Frau Erika Mustermann Testweg 777 CH- 88888 Musterstadt',
            'Frau Erika Mustermann Testweg 777 CH - 88888 Musterstadt',
            'Frau Erika Mustermann Testweg 777 f-88888 Musterstadt', // Code must be uppercase
            'Frau Erika Mustermann Testweg 777 F-88888 Musterstadt Frankreich', // prefix and country will be removed
            'Frau Erika Mustermann Testweg 777 CH- 88888 Musterstadt Italien', // Country name has precedence!!
        ];
        $expected = [
            ['country' => 'Deutschland', 'name' => 'Frau Erika Mustermann Testweg 777 88888 Musterstadt'],
            ['country' => 'Italien', 'name' => 'Frau Erika Mustermann Testweg 777 88888 Musterstadt'],
            ['country' => 'Frankreich', 'name' => 'Frau Erika Mustermann Testweg 777 88888 Musterstadt'],
            ['country' => 'Deutschland', 'name' => 'Frau Erika Mustermann Testweg 777 88888 Musterstadt'],
            ['country' => 'Italien', 'name' => 'Frau Erika Mustermann Testweg 777 88888 Musterstadt'],
            ['country' => 'Deutschland', 'name' => 'Frau Erika Mustermann Testweg 777 88888 Musterstadt'],
            ['country' => 'Schweiz', 'name' => 'Frau Erika Mustermann Testweg 777 88888 Musterstadt'],
            ['country' => 'Schweiz', 'name' => 'Frau Erika Mustermann Testweg 777 88888 Musterstadt'],
            ['country' => 'Deutschland', 'name' => 'Frau Erika Mustermann Testweg 777 f-88888 Musterstadt'], // wrong prefix could not be removed
            ['country' => 'Frankreich', 'name' => 'Frau Erika Mustermann Testweg 777 88888 Musterstadt'],
            ['country' => 'Italien', 'name' => 'Frau Erika Mustermann Testweg 777 88888 Musterstadt'],
        ];
        $results = [false, true, true, false, true, false, true, true, false, true, true];
        foreach ($data as $key => $address) {
            $this->assertEquals($results[$key], $this->checker->isDeliverable(['name' => $address]));
            $result = $this->checker->getAddress();
            $this->assertEquals($expected[$key]['country'], $result['country']);
            $this->assertEquals($expected[$key]['name'], $result['name']);
        }
    }
}
