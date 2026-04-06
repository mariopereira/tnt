<?php

namespace thm\tnt_ec\test\unit;

use PHPUnit\Framework\TestCase;
use thm\tnt_ec\service\PricingService\entity\Address;
use thm\tnt_ec\service\PricingService\entity\ConsignmentDetails;
use thm\tnt_ec\service\PricingService\entity\PieceLine;
use thm\tnt_ec\service\PricingService\entity\PieceMeasurements;
use thm\tnt_ec\service\PricingService\PricingService;

class PriceRequestTest extends TestCase
{

    private $ps;

    public function setUp(): void
    {

        parent::setUp();

        $this->ps = new PricingService('user', 'password');
    }

    /**
     * Is XML valid
     */
    public function testIsXmlValid()
    {

        $this->ps
            ->setRateId(1730972807)
            ->setSender((new Address('Budapest', '1101', 'HU')))
            ->setDelivery((new Address('Győr', '9000', 'HU')))
            ->setCurrency('HUF')
            ->setCollectionDateTime(new \DateTime('2024-11-07T09:46:47+00:00'))
            ->setProduct('N')
            ->setServiceId('15')
            ->setPriceBreakDown(true)
            ->setPieceLines([
                new PieceLine(1, new PieceMeasurements(10, 10, 10, 1)),
                new PieceLine(1, new PieceMeasurements(20, 20, 20, 2)),
            ])
            ->setConsignmentDetails(new ConsignmentDetails(2, 0.002, 2))
            ->setAccountNumber('94504')
            ->setAccountCountryCode('HU')
        ;
        $xml = file_get_contents(__DIR__ . '/rating_test.xml');
        $xml1 = new \SimpleXMLElement($xml);
        $xml2 = new \SimpleXMLElement($this->ps->getXmlContent());
        $this->assertEquals($xml1->asXML(), $xml2->asXML());
    }

}