<?php

namespace thm\tnt_ec\test\unit;

use PHPUnit\Framework\TestCase;
use thm\tnt_ec\service\PricingService\entity\Address;
use thm\tnt_ec\service\PricingService\entity\ConsignmentDetails;
use thm\tnt_ec\service\PricingService\entity\Insurance;
use thm\tnt_ec\service\PricingService\entity\PieceLine;
use thm\tnt_ec\service\PricingService\entity\PieceMeasurements;
use thm\tnt_ec\service\PricingService\PricingService;

class PriceRequestTest extends TestCase
{

    private $ps;

    public function setUp(): void
    {

        parent::setUp();

        $this->ps = new PricingService('', '');
    }

    /**
     * Is XML valid
     */
    public function testIsXmlValid()
    {

        $this->ps
            ->setSender((new Address('Budapest', '1101', 'HU')))
            ->setDelivery((new Address('Győr', '9000', 'HU')))
            ->setCurrency('HUF')
            ->setCollectionDateTime(new \DateTime())
            ->setProduct('N')
            ->setServiceId('15')
            ->setPriceBreakDown(false)
            //->setInsurance(new Insurance(100, 100))
            ->setPieceLines([
                new PieceLine(1, new PieceMeasurements(10, 10, 10, 1)),
                new PieceLine(1, new PieceMeasurements(10, 10, 10, 1)),
            ])
            ->setConsignmentDetails(new ConsignmentDetails(2, 0.002, 2))
            ->setAccountNumber('94504')
            ->setAccountCountryCode('HU')
        ;

        $response = $this->ps->getPrice();
        $state = simplexml_load_string($response->getRequestXml());

        $assert = ($state === false) ? false : true;

        $this->assertTrue($assert);
    }

}