<?php

require_once __DIR__.'/../../../autoload.php';

use PHPUnit\Framework\TestCase;
use Cewi\Checkers\OwnChecker;

/**
 * PostboxCheckerTest.
 *
 * @author cewi <c.wichmann@gmx.de>
 */
class OwnCheckerTest extends TestCase
{
    public $checker;

    /**
     * setUp method.
     */
    public function setUp()
    {
        parent::setUp();

        $this->checker = new OwnChecker([
            'type_error' => 'Error',
            'type_success' => 'Success',
            'zipCities' => [
                88888 => [
                    'pattern' => 'musterstadt|klein(?: )?musterstadt',
                    'id' => 1,
                ],
                88889 => [
                    'pattern' => 'musterheim|musterhausen',
                    'id' => 2,
                ],
            ],
            'zipStreets' => [
                88888 => [
                    0 => [
                        'pattern' => 'muster(?: )?strasse',
                        'id' => 1,
                        'region_id' => 1,
                        'length' => 13,
                        'name' => 'Musterstraße',
                    ],
                    1 => [
                        'pattern' => 'master(?: )?strasse',
                        'id' => 1,
                        'region_id' => 1,
                        'length' => 13,
                        'name' => 'Musterstraße',
                    ],
                    2 => [
                        'pattern' => 'am(?: )?weg',
                        'id' => 2,
                        'region_id' => 1,
                        'length' => 6,
                        'name' => 'Am Weg',
                    ],
                    3 => [
                        'pattern' => 'im(?: )?weg',
                        'id' => 2,
                        'region_id' => 1,
                        'length' => 6,
                        'name' => 'Am Weg',
                    ],
                ],
                88889 => [
                    0 => [
                        'pattern' => 'muster(?: )?weg',
                        'id' => 3,
                        'region_id' => 2,
                        'length' => 11,
                        'name' => 'Musterweg',
                    ],
                    1 => [
                        'pattern' => 'master(?: )?weg',
                        'id' => 3,
                        'region_id' => 2,
                        'length' => 11,
                        'name' => 'Musterweg',
                    ],
                ],
            ],
        ]);
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
        $this->assertInstanceOf('Cewi\Checkers\OwnChecker', $this->checker);
    }

    /**
     * address -check.
     */
    public function testIsDeliverable()
    {
        $addresses = [
            [
                'name' => 'Erika Mustermann Musterstraße 88 // 2',
                'zip' => 88888,
                'city' => 'Musterstadt',
            ],
            [
                'name' => 'Erika Mustermann Musterweg 8a',
                'zip' => 88889,
                'city' => 'Musterheim',
            ],
            [
                'name' => 'Erika Mustermann Musterstraße 8',
                'zip' => 88888,
                'city' => 'Musterheim',
            ],
            [
                'name' => 'Erika Mustermann NoStreet 1',
                'zip' => 88888,
                'city' => 'Musterstadt',
            ],
        ];
        $expected = [
            [
                'type' => 'Success',
                'name' => 'Erika Mustermann',
                'street' => 'Musterstraße',
                'street_id' => 1,
                'region_id' => 1,
                'number' => 88,
                'number_letter' => '// 2',
                'zip' => 88888,
                'city' => 'Musterstadt',
                'city_id' => 1,
            ],
            [
                'type' => 'Success',
                'name' => 'Erika Mustermann',
                'street' => 'Musterweg',
                'street_id' => 3,
                'region_id' => 2,
                'number' => 8,
                'number_letter' => 'a',
                'zip' => 88889,
                'city' => 'Musterheim',
                'city_id' => 2,
            ],
            false, // city and zip doesn't match
            [
                'type' => 'Error',
                'name' => 'Erika Mustermann NoStreet 1',
                'zip' => 88888,
                'city' => 'Musterstadt',
                'city_id' => 1,
            ], // street not found
        ];
        foreach ($addresses as $key => $address) {
            if ($this->checker->isDeliverable($address)) {
                $this->assertEquals($expected[$key], $this->checker->getAddress());
            } else {
                $this->assertFalse($expected[$key]);
            }
        }
    }
}
