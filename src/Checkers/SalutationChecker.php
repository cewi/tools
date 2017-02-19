<?php

namespace Cewi\Checkers;

/**
 * Checks, if an Address begins with a Salutation.
 *
 * @author cewi <c.wichmann@gmx.de>
 * @license https://opensource.org/licenses/MIT
 */
class SalutationChecker
{
    /**
     * Pattern to identify Salutation in address string.
     *
     * @var string
     */
    protected $_pattern = '';

    /**
     * @var array
     */
    protected $_address = [];

    /**
     * set pattern.
     *
     * @param array $options
     */
    public function __construct($options)
    {
        if (!isset($options['salutations'])) {
            $this->_pattern = '^this pattern will not be found!';
        } else {
            $this->_pattern = '^('.implode('|', $options['salutations']).')(?:\s)(.*)$';
        }
    }

    /**
     * if a salutation is found at start of string
     * remove it from name and save it in salutation field.
     *
     * @param array $address
     *
     * @return bool
     */
    public function check($address)
    {
        $this->_address = $address;

        if (preg_match('#'.$this->_pattern.'#i', $this->_address['name'], $matches)) {
            $this->_address['salutation'] = trim($matches[1]);
            $this->_address['name'] = trim($matches[2]);

            return true;
        }

        return false;
    }

    /**
     * get changed address.
     *
     * @return array
     */
    public function getAddress()
    {
        return $this->_address;
    }
}
