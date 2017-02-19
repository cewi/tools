<?php

namespace Cewi\Checkers;

/**
 * Checks, if address contains country names and modifies address array
 * one country couls be defined as home-country.
 *
 * @author cewi <c.wichmann@gmx.de>
 * @license https://opensource.org/licenses/MIT
 */
class CountryChecker implements CheckerInterface
{
    /**
     * how to deliver these envelopes.
     *
     * @var string
     */
    protected $_type = 'Error';

    /**
     * countries to check.
     *
     * @var array
     */
    protected $_countries;

    /**
     * Pattern to identify Country names in an address string.
     *
     * @var string
     */
    protected $_countryPattern = '';

    /**
     * Pattern to identify Zip-Prefixes of countries in address-string.
     * 
     * @var string
     */
    protected $_countryPrefixPattern = '';

    /**
     * country that should not be treated as "foreign".
     *  
     * @var array
     */
    protected $_homeCountry = [
        'id' => 9999999,
        'name' => 'Neverland',
        'prefix' => 'NE',
    ];

    /**
     * the Address to check.
     *
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
        $this->_type = isset($options['type']) ? $options['type'] : 'Error';

        if (isset($options['countries'])) {
            $this->_countries = $options['countries'];

            // if name of a country is found. It must be at the end of the string
            $this->_countryPattern = '^(.*)(?:\s)('.implode('|', array_column($this->_countries, 'name')).')$';

            // A country prefix must be followed by a hyphen and a zip-code (3-6 digits) and in UPPERCASE
            $this->_countryPrefixPattern = '^(.*)(?:\s)('.implode('|', array_column($this->_countries, 'prefix')).')(?:\s?\-\s?)(\d{3,6})(.*)$';
        }

        if (isset($options['homeCountry'])) {
            $this->_homeCountry = $options['homeCountry'];
        }
    }

    /**
     * check if address contains Country name or prefix
     * modify address array.
     *
     * @param array $address
     *
     * @return bool
     */
    public function isDeliverable($address)
    {
        $this->_address = $address;

        if (isset($this->_homeCountry)) {
            $this->_address['country_id'] = $this->_homeCountry['id'];
            $this->_address['country'] = $this->_homeCountry['name'];
        }

        // Is there a country-prefix (must be UPPERCASE)?
        if (preg_match('#'.$this->_countryPrefixPattern.'#', $this->_address['name'], $matches)) {
            $prefix = trim($matches[2]);
            $this->_address['name'] = $matches[1].' '.$matches[3].$matches[4];
            $country = $this->_getCountryByPrefix($prefix);
        }

        // Is there a country-name at the end of the string=
        // id found ist has precendece over any found id
        if (preg_match('#'.$this->_countryPattern.'#i', $this->_address['name'], $matches)) {
            $this->_address['name'] = $matches[1];
            $country = $this->_getCountryByName(ucfirst(strtolower(trim($matches[2]))));
        }

        // is a country was found, set it in address array
        if (isset($country)) {
            $this->_address['country'] = $country['name'];
            $this->_address['country_id'] = $country['id'];
            unset($country);
        }

        // only homeCountry is not deliverable by definition
        if ($this->_address['country_id'] !== $this->_homeCountry['id']) {
            $this->_address['type'] = $this->_type;

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

    /**
     * identity country array by prefix string.
     *
     * @param string $prefix
     *
     * @return array
     */
    protected function _getCountryByPrefix($prefix)
    {
        $country = array_filter($this->_countries, function ($c) use ($prefix) {
            return $c['prefix'] == $prefix;
        });

        return array_shift($country);
    }

    /**
     * identify country by name string.
     *
     * @param string $name
     *
     * @return array
     */
    protected function _getCountryByName($name)
    {
        $country = array_filter($this->_countries, function ($c) use ($name) {
            return $c['name'] == $name;
        });

        return array_shift($country);
    }
}
