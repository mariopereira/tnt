<?php

/**
 * TNT Address for shipping service.
 *
 * Note that this class may looks very similar to other address class
 * but functions referrer to another XML elements therefore it cannot be extended.
 * Also some address elements maybe different then in other service.
 *
 * @author Wojciech Brozyna <http://vobro.systems>
 * @license https://github.com/200MPH/tnt/blob/master/LICENCE MIT
 */

namespace thm\tnt_ec\service\PricingService\entity;

class Address
{

    private string $town;

    private string $postcode;

    private string $country;

    /**
     * @param string $town
     * @param string $postcode
     * @param string $country
     */
    public function __construct(string $town, string $postcode, string $country)
    {
        $this->town = $town;
        $this->postcode = $postcode;
        $this->country = $country;
    }


    public function getTown(): string
    {
        return $this->town;
    }

    public function setTown(string $town): void
    {
        $this->town = $town;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): void
    {
        $this->postcode = $postcode;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }


}
