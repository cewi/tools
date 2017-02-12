<?php

namespace Cewi\Checkers;

/**
 * Checks, if an Address should be delivered to a german postbox
 *
 * @author cewi <c.wichmann@gmx.de>
 * @license https://opensource.org/licenses/MIT
 */
class PostboxChecker implements CheckerInterface
{

    /**
     * how to deliver these envelopes
     *
     * @var string
     */
    protected $_type = 'Error';

    /**
     * Pattern to identify Postbox in an address string
     *
     * @var string
     */
    protected $_pattern = '^(.*\s?)((?:Postfach|PF)\s?\d{2,6})(?:\s?)$';

    /**
     *
     * @var array
     */
    protected $_address = [];

    /**
     * set type for delivery
     *
     * @param array $options
     */
    public function __construct($options)
    {
        $this->_type = isset($options['type']) ? $options['type'] : 'Error';
    }

    /**
     * check if address contains valid german postbox string
     *
     * @param array $address
     * @return boolean
     */
    public function isDeliverable($address)
    {
        $this->_address = $address;

        if (preg_match('#' . $this->_pattern . '#i', $this->_address['name'], $matches)) {

            $this->_address = array_merge($this->_address, [
                'name' => trim($matches[1]),
                'street' => trim($matches[2]),
                'type' => $this->_type,
            ]);
            return true;
        }
        return false;
    }

    /**
     * get changed Address
     *
     * @return array
     */
    public function getAddress()
    {
        return $this->_address;
    }

}
