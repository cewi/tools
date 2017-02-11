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

namespace \Cewi\Checkers;

/**
 * Checks, if an Address should be delivered to a german postbox
 *
 * @author cewi <c.wichmann@gmx.de>
 */
class PostboxChecker
{

    /**
     * Pattern to identify Postbox in an address string
     *
     *
     * @var string
     */
    protected $_pattern = '^(.*\s?)((?:Postfach|PF)\s?\d{2,6})(?:\s?)$';

    /**
     * check if address contains german postbox
     *
     * @param string $address
     * @return boolean
     */
    public function isDeliverable($address)
    {
        if (preg_match('#' . $this->_pattern . '#i', $address, $matches)) {
            return [
                'name' => trim($matches[1]),
                'street' => trim($matches[2]),
            ];
        }
        return false;
    }

}
