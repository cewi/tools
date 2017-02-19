<?php

namespace Cewi\Checkers;

/**
 * Checks, if an Address could be deliverd by other party identfied by zip-regions
 *
 * @author cewi <c.wichmann@gmx.de>
 * @license https://opensource.org/licenses/MIT
 */
class OtherChecker implements CheckerInterface
{

    /**
     * how to deliver these envelopes
     *
     * @var string
     */
    protected $_type = 'Error';

    /**
     * Pattern to identify zip-regions
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
        $this->_pattern = isset($options['zips']) ? '^' . join('|', $options['zips']) : '';
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

        if (preg_match('#' . $this->_pattern . '#i', $this->_address['zip'])) {

            $this->_address = array_merge($this->_address, [
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
