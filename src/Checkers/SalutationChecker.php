<?php

namespace Cewi\Checkers;

/**
 * Checks, if an Address begins with a Salutation
 *
 * @author cewi <c.wichmann@gmx.de>
 * @license https://opensource.org/licenses/MIT
 */
class SalutationChecker
{

    /**
     * Pattern to identify Postbox in an address string
     *
     * @var string
     */
    protected $_pattern = '';

    /**
     *
     * @var array
     */
    protected $_address = [];

    /**
     * set pattern
     *
     * @param array $options
     */
    public function __construct($options)
    {
        if (!isset($options['salutations'])) {
            $this->_pattern = '^this pattern will not be found!';
        }
        $this->_pattern = '^(' . implode('|', $options['salutations']) . ')(?:\s)(.*)$';
    }

    /**
     * find a saluattaion at start of string
     * remove it and save in database
     *
     * @param string $address
     * @return string
     */
    public function check($address)
    {
        $this->_address = $address;

        if (preg_match('#' . $this->_pattern . '#i', $address['name'], $matches)) {

            $this->_address = array_merge($this->_address, [
                'salutation' => trim($matches[1]),
                'name' => trim($matches[2]),
            ]);

            return true;
        }
        return false;
    }

    /**
     *
     * @return array
     */
    public function getAddress()
    {
        return $this->_address;
    }

}
