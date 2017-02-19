<?php

require_once __DIR__ . '/../../../autoload.php';

use PHPUnit\Framework\TestCase;
use Cewi\Checkers\DpagChecker;

/**
 * DpagCheckerTest
 *
 * @author cewi <c.wichmann@gmx.de>
 */
class DpagCheckerTest extends TestCase
{

    public $checker;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->checker = new DpagChecker(['type' => 'Success', 'homeCountryId' => 1]);
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
        $this->assertInstanceOf('Cewi\Checkers\DpagChecker', $this->checker);
    }

    /**
     * Postfach-check
     */
    public function testIsDeliverable()
    {
        $addresses = [
            [
                'name' => 'Erika Mustermann',
                'zip' => 88888,
                'city' => 'Musterstadt',
                'country_id' => 1
            ],
            [
                'name' => 'Erika Mustermann',
                'city' => 'Musterheim',
                'country_id' => 1
            ],
            [
                'name' => 'Erika Mustermann',
                'zip' => 88888,
                'city' => 'Musterheim',
                'country_id' => 2
            ],
            [
                'name' => 'Erika Mustermann',
                'zip' => 88888,
                'city' => 'Musterstadt'
            ],
        ];
        $expected = [
            [
                'type' => 'Success',
                'name' => 'Erika Mustermann',
                'zip' => 88888,
                'city' => 'Musterstadt',
                'country_id' => 1
            ],
            false, // zip missing
            false, // country_id not home country
            false, // country_id is missing
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
