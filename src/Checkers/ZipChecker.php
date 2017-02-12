<?php

namespace Cewi\Checkers;

/**
 * Checks, if address contains country names and modifies address array
 * one country couls be defined as home-country
 *
 * @author cewi <c.wichmann@gmx.de>
 * @license https://opensource.org/licenses/MIT
 */
class ZipChecker
{

    /**
     * how to deliver these envelopes
     *
     * @var string
     */
    protected $_type = 'Error';

    /**
     * PLZ-Pattern
     *
     * regexp for german zips (postleitzahlen)
     * see http://www.regexlib.com
     *
     * @var string
     */
    protected $_pattern = "((?:0[1-46-9]\d{3})|(?:[1-357-9]\d{4})|(?:[4][0-24-9]\d{3})|(?:[6][013-9]\d{3}))";

    /**
     * the Address to check
     *
     * @var array
     */
    protected $_address = [];

    /**
     * set type
     * @param array $options
     */
    public function __construct($options)
    {

        $this->_type = isset($options['type']) ? $options['type'] : 'Error';
    }

    /**
     * check if address-string contains zip
     * modify address array
     *
     * @param array $address
     * @return boolean
     */
    public function check($address)
    {
        $this->_address = $address;

        if (preg_match('#^(.*)' . $this->_pattern . '(.*)$#', $this->_address['name'], $matches)) {
            $this->_address['name'] = trim($matches[1]);
            $this->_address['zip'] = trim($matches[2]);
            $this->_address['city'] = trim($matches[3]);
            return true;
        }
        $this->_address['type'] = $this->_type;
        return false;
    }

    /**
     * get changed address
     *
     * @return array
     */
    public function getAddress()
    {
        return $this->_address;
    }

}
