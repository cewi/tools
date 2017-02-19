<?php

require_once __DIR__.'/../../../autoload.php';

use PHPUnit\Framework\TestCase;
use Cewi\Checkers\PostboxChecker;

/**
 * PostboxCheckerTest.
 *
 * @author cewi <c.wichmann@gmx.de>
 */
class PostboxCheckerTest extends TestCase
{
    public $checker;

    /**
     * setUp method.
     */
    public function setUp()
    {
        parent::setUp();

        $this->checker = new PostboxChecker(['type' => 'Success']);
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
        $this->assertInstanceOf('Cewi\Checkers\PostboxChecker', $this->checker);
    }

    /**
     * Postfach-check.
     */
    public function testIsDeliverable()
    {
        $addresses = [
            [
                'name' => 'Erika Mustermann Ginsterstraße 8',
            ],
            [
                'name' => 'Erika Mustermann Postfach 12345',
            ],
            [
                'name' => 'Erika Mustermann Postfach 1', // not enough digits
            ],
            [
                'name' => 'Erika Mustermann Postfach 1112222222', // too many digits
            ],
            [
                'name' => 'Erika MustermannPostfach 12345', // no whitspace before Posfach will be found either!
            ],
            [
                'name' => 'Erika Mustermann Pf 12345',
            ],
        ];
        $expected = [
            false,
            ['type' => 'Success', 'name' => 'Erika Mustermann', 'street' => 'Postfach 12345'],
            false,
            false,
            ['type' => 'Success', 'name' => 'Erika Mustermann', 'street' => 'Postfach 12345'],
            ['type' => 'Success', 'name' => 'Erika Mustermann', 'street' => 'Pf 12345'],
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
