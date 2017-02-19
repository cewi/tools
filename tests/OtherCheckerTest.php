<?php

require_once __DIR__.'/../../../autoload.php';

use PHPUnit\Framework\TestCase;
use Cewi\Checkers\OtherChecker;

/**
 * PostboxCheckerTest.
 *
 * @author cewi <c.wichmann@gmx.de>
 */
class OtherCheckerTest extends TestCase
{
    public $checker;

    /**
     * setUp method.
     */
    public function setUp()
    {
        parent::setUp();

        $this->checker = new OtherChecker([
            'type' => 'Other',
            'zips' => [88, 66],
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
        $this->assertInstanceOf('Cewi\Checkers\OtherChecker', $this->checker);
    }

    /**
     * Postfach-check.
     */
    public function testIsDeliverable()
    {
        $addresses = [
            [
                'zip' => '88888',
            ],
            [
                'zip' => '88889',
            ],
            [
                'zip' => '99999', // not enough digits
            ],

        ];
        $expected = [
            ['type' => 'Other', 'zip' => '88888'],
            ['type' => 'Other', 'zip' => '88889'],
            false,
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
