<?php

/*
 *  Copyright 2016 Voll-Komm
 * 
 *  Jahnstrasse 28a, 67245 Lambsheim
 *  Fon: 06233 4592520, Fax: 06233 4592518
 *  info@voll-komm.de
 *  
 *  www.voll-komm.de
 *  
 */
require_once __DIR__ . '/../../../autoload.php';

use PHPUnit\Framework\TestCase;
use Checkers\PostboxChecker;

/**
 * Description of PostboxCheckerTest
 *
 * @author cewi <c.wichmann@gmx.de>
 */
class PostboxCheckerTest extends TestCase
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

        $this->checker = new PostboxChecker();
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
        $this->assertInstanceOf('Checkers\PostboxChecker', $this->checker);
    }

}
