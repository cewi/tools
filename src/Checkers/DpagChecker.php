<?php

namespace Cewi\Checkers;

/**
 * Checks, if an Address could be deliverd by Dpag.
 *
 * @author cewi <c.wichmann@gmx.de>
 * @license https://opensource.org/licenses/MIT
 */
class DpagChecker implements CheckerInterface
{
    /**
     * how to deliver these envelopes.
     *
     * @var string
     */
    protected $_type = 'Error';

    /**
     * @var array
     */
    protected $_address = [];

    /**
     * @var int
     */
    protected $_homeCountryId = 9999999;

    /**
     * set type for delivery.
     *
     * @param array $options
     */
    public function __construct($options)
    {
        $this->_type = isset($options['type']) ? $options['type'] : 'Error';
        $this->_homeCountryId = isset($options['homeCountryId']) ? $options['homeCountryId'] : 999999;
    }

    /**
     * check if address contains valid german postbox string.
     *
     * @param array $address
     *
     * @return bool
     */
    public function isDeliverable($address)
    {
        $this->_address = $address;
        if (
                isset($address['zip']) &&
                isset($address['city']) &&
                isset($address['country_id']) &&
                ($address['country_id'] == $this->_homeCountryId)
        ) {
            $this->_address['type'] = $this->_type;

            return true;
        }

        return false;
    }

    /**
     * get changed Address.
     *
     * @return array
     */
    public function getAddress()
    {
        return $this->_address;
    }
}
