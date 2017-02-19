<?php

namespace Cewi\Checkers;

/**
 * Checks, if an Address can be delivered by ourselves.
 *
 * @author cewi <c.wichmann@gmx.de>
 * @license https://opensource.org/licenses/MIT
 */
class OwnChecker implements CheckerInterface
{
    /**
     * type if address ist not deliverable.
     *
     * @var string
     */
    protected $_type_error = 'Error';

    /**
     * how to deliver these envelopes.
     *
     * @var string
     */
    protected $_type_success = 'Error';

    /**
     * cities where we deliver ourselves.
     *
     * @var array
     */
    protected $_zipCities = [];

    /**
     * streets in thta cities.
     *
     * @var array
     */
    protected $_zipStreets = [];

    /**
     * @var array
     */
    protected $_address = [];

    /**
     * set type for delivery.
     *
     * @param array $options
     */
    public function __construct($options)
    {
        // set types
        $this->_type_success = isset($options['type_success']) ? $options['type_success'] : 'Error';
        $this->_type_error = isset($options['type_error']) ? $options['type_error'] : 'Error';

        // set arrays with patterns
        $this->_zipCities = isset($options['zipCities']) ? $options['zipCities'] : [];
        $this->_zipStreets = isset($options['zipStreets']) ? $options['zipStreets'] : [];
        //dd($this->_zipStreets);
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
        $this->_address['type'] = $this->_type_error;

        if (in_array($this->_address['zip'], array_keys($this->_zipCities))) {

            // we can deliver!
            $city = $this->_generateNormalized($this->_address['city']);
            $name = $this->_generateNormalized($this->_address['name']);

            // is the found city ok?
            if (preg_match('#'.$this->_zipCities[$this->_address['zip']]['pattern'].'#i', $city, $matches)) {

                // set id for city
                $this->_address['city_id'] = $this->_zipCities[$this->_address['zip']]['id'];

                $streets = $this->_zipStreets[$this->_address['zip']];

                foreach ($streets as $street) {
                    if (preg_match('#'.$street['pattern'].'\s?(.*)?$#i', $name, $matches, PREG_OFFSET_CAPTURE)) {
                        $this->_address['street_id'] = $street['id'];
                        $this->_address['region_id'] = $street['region_id'];
                        $this->_address['street'] = $street['name'];
                        $this->_address['name'] = trim(substr($this->_address['name'], 0, $matches[0][1]));
                        if (preg_match('#^(\d+)(.*)$#', $matches[1][0], $matches)) {
                            $this->_address['number'] = (int) $matches[1];
                            $this->_address['number_letter'] = trim($matches[2]);
                        }
                        $this->_address['type'] = $this->_type_success;

                        return true;
                    }
                }

                return true;
            }
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

    /**
     * normalizes Strings:
     * - any type of whitspace to a single space
     * - german umlauts to diphtongs
     * - all lower case.
     *
     * @since 15.11.2016
     *
     * @param string $string String to normalize
     *
     * @return string normalized String
     */
    protected function _generateNormalized($string = null)
    {
        // change whitespace to single space letter
        $stringWithoutSpaces = preg_replace('#[\s]#', ' ', $string);
        // change german umlauts to diphtongs
        $stringWithoutUmlauts = str_replace(['-', 'ß', 'ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', "'"], [' ', 'ss', 'ae', 'oe', 'ue', 'AE', 'OE', 'UE'], $stringWithoutSpaces);
        // all to lowercase
        $stringLowerCase = mb_strtolower($stringWithoutUmlauts, 'UTF-8');
        // trim the string again
        return trim($stringLowerCase);
    }
}
