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
use Cewi\Checkers\SalutationChecker;

/**
 * Description of PostboxCheckerTest
 *
 * @author cewi <c.wichmann@gmx.de>
 */
class SalutationCheckerTest extends TestCase
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

        $salutations = [
            'Herrn?(?:\sund\sFrau)?',
            'Frau(?:\sund\sHerrn?)?',
            'Firma'
        ];

        $this->checker = new SalutationChecker(['salutations' => $salutations]);
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
        $this->assertInstanceOf('Cewi\Checkers\SalutationChecker', $this->checker);
    }

    public function testCheck()
    {
        $addresses = [
            ['name' => 'Frau Erika Mustermann Musterstraße 8 D-88888 Musterstadt'],
            ['name' => 'Herrn Max Mustermann Musterstraße 8 88888 Musterstadt'],
            ['name' => 'Firma Mustermann Musterstraße 8 88888 Musterstadt'],
            ['name' => 'Herr Max Mustermann Musterstraße 8 88888 Musterstadt'], // 'Herr' will be found, too
            ['name' => 'Herrn und Frau Max und Erika Mustermann Musterstraße 8 88888 Musterstadt'],
            ['name' => 'Frau und Herrn Erika und Max Mustermann Musterstraße 8 88888 Musterstadt'],
            ['name' => 'Max und Erika Mustermann Musterstraße 8 88888 Musterstadt'], // no salutation
        ];
        $expected = [
            [
                'name' => 'Erika Mustermann Musterstraße 8 D-88888 Musterstadt',
                'salutation' => 'Frau'
            ],
            [
                'name' => 'Max Mustermann Musterstraße 8 88888 Musterstadt',
                'salutation' => 'Herrn'
            ],
            [
                'name' => 'Mustermann Musterstraße 8 88888 Musterstadt',
                'salutation' => 'Firma'
            ],
            [
                'name' => 'Max Mustermann Musterstraße 8 88888 Musterstadt',
                'salutation' => 'Herr'
            ],
            [
                'name' => 'Max und Erika Mustermann Musterstraße 8 88888 Musterstadt',
                'salutation' => 'Herrn und Frau'
            ],
            [
                'name' => 'Erika und Max Mustermann Musterstraße 8 88888 Musterstadt',
                'salutation' => 'Frau und Herrn'
            ],
            false,
        ];
        foreach ($addresses as $key => $address) {
            if ($this->checker->check($address)) {
                $result = $this->checker->getAddress();
                $this->assertEquals($expected[$key]['name'], $result['name']);
                $this->assertEquals($expected[$key]['salutation'], $result['salutation']);
            } else {
                $this->assertFalse($expected[$key]);
            }
        }
    }

}
