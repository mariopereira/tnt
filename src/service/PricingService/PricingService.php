<?php

namespace thm\tnt_ec\service\PricingService;

use thm\tnt_ec\service\AbstractService;
use thm\tnt_ec\service\HTTPHeaders;
use thm\tnt_ec\service\PricingService\entity\Address;
use thm\tnt_ec\service\PricingService\entity\ConsignmentDetails;
use thm\tnt_ec\service\PricingService\entity\Insurance;
use thm\tnt_ec\service\PricingService\entity\PieceLine;
use thm\tnt_ec\TNTException;

class PricingService extends AbstractService
{

    const VERSION = '3.2';

    /**
     * @var string
     */
    private string $appid;

    private ?string $rateId = null;

    private Address $sender;

    private Address $delivery;

    private \DateTimeInterface $collectionDateTime;

    private string $product;

    private ?Insurance $insurance = null;

    private string $currency;

    private bool $priceBreakDown = true;

    private ?ConsignmentDetails $consignmentDetails = null;

    /**
     * @var PieceLine[]
     */
    private array $pieceLines = [];
    private string $serviceId;

    /**
     * Initialise service
     *
     * @param string $userId
     * @param string $password
     * @param string $appid [optional] Default "PC"
     * @throw TNTException
     * @throws TNTException
     */
    public function __construct($userId, $password, $appid = 'PC')
    {

        parent::__construct($userId, $password);
        $this->appid = $appid;
    }

    public function getServiceUrl()
    {
        return 'https://express.tnt.com/expressconnect/pricing/getprice';
        //return 'https://iconnection.tnt.com/PriceGate.asp';
    }

    public function getAppid(): string
    {
        return $this->appid;
    }

    public function setAppid(string $appid): self
    {
        $this->appid = $appid;
        return $this;
    }

    public function getSender(): Address
    {
        return $this->sender;
    }

    public function setSender(Address $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function getDelivery(): Address
    {
        return $this->delivery;
    }

    public function setDelivery(Address $delivery): self
    {
        $this->delivery = $delivery;
        return $this;
    }

    public function getCollectionDateTime(): \DateTimeInterface
    {
        return $this->collectionDateTime;
    }

    public function setCollectionDateTime(\DateTimeInterface $collectionDateTime): self
    {
        $this->collectionDateTime = $collectionDateTime;
        return $this;
    }

    public function getProduct(): string
    {
        return $this->product;
    }

    public function setProduct(string $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getServiceId(): string
    {
        return $this->serviceId;
    }

    public function setServiceId(string $serviceId): PricingService
    {
        $this->serviceId = $serviceId;
        return $this;
    }

    public function getInsurance(): Insurance
    {
        return $this->insurance;
    }

    public function setInsurance(Insurance $insurance): self
    {
        $this->insurance = $insurance;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function isPriceBreakDown(): bool
    {
        return $this->priceBreakDown;
    }

    public function setPriceBreakDown(bool $priceBreakDown): self
    {
        $this->priceBreakDown = $priceBreakDown;
        return $this;
    }

    public function getConsignmentDetails(): ?ConsignmentDetails
    {
        return $this->consignmentDetails;
    }

    public function setConsignmentDetails(?ConsignmentDetails $consignmentDetails): self
    {
        $this->consignmentDetails = $consignmentDetails;
        return $this;
    }

    public function getPieceLines(): array
    {
        return $this->pieceLines;
    }

    public function setPieceLines(array $pieceLines): self
    {
        $this->pieceLines = $pieceLines;
        return $this;
    }

    public function getRateId(): ?string
    {
        return $this->rateId;
    }

    public function setRateId(string $rateId): PricingService
    {
        $this->rateId = $rateId;
        return $this;
    }


    public function getPrice(): PricingResponse
    {
        $this->startDocument();
        $this->endDocument();
        return new PricingResponse($this->sendRequest(), $this->getXmlContent());
    }

    /**
     * Start document
     *
     * @return void
     */
    protected function startDocument()
    {

        parent::startDocument();

        $this->xml->startElement("priceRequest");
        $this->xml->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $this->xml->writeElement('appId', $this->appid);
        $this->xml->writeElement('appVersion', self::VERSION);
        $this->xml->startElement("priceCheck");
        $this->xml->writeElement('rateId', $this->rateId ?? time());
        $this->xml->startElement('sender');
        $this->xml->writeElement('country', $this->sender->getCountry());
        $this->xml->writeElement('town', $this->sender->getTown());
        $this->xml->writeElement('postcode', $this->sender->getPostcode());
        $this->xml->endElement();
        $this->xml->startElement('delivery');
        $this->xml->writeElement('country', $this->delivery->getCountry());
        $this->xml->writeElement('town', $this->delivery->getTown());
        $this->xml->writeElement('postcode', $this->delivery->getPostcode());
        $this->xml->endElement();
        $this->xml->writeElement('collectionDateTime', $this->collectionDateTime->format('c'));
        $this->xml->startElement('product');
        $this->xml->writeElement('id', $this->serviceId);
        $this->xml->writeElement('type', $this->product);
        $this->xml->endElement();
        $this->xml->startElement('account');
        $this->xml->writeElement('accountNumber', $this->account);
        $this->xml->writeElement('accountCountry', $this->accountCountryCode);
        $this->xml->endElement();
        if ($this->insurance) {
            $this->xml->startElement('insurance');
            $this->xml->writeElement('insuranceValue', $this->insurance->getInsuranceValue());
            $this->xml->writeElement('goodsValue', $this->insurance->getGoodsValue());
            $this->xml->endElement();
        }
        $this->xml->writeElement('termsOfPayment', 'S');
        $this->xml->writeElement('currency', $this->currency);
        $this->xml->writeElement('priceBreakDown', $this->priceBreakDown ? 'true' : 'false');
        if ($this->consignmentDetails) {
            $this->xml->startElement('consignmentDetails');
            $this->xml->writeElement('totalWeight', $this->consignmentDetails->getTotalWeight());
            $this->xml->writeElement('totalVolume', $this->consignmentDetails->getTotalVolume());
            $this->xml->writeElement('totalNumberOfPieces', $this->consignmentDetails->getTotalNumberOfPieces());
            $this->xml->endElement();
        }
        if (count($this->pieceLines) > 0) {
            foreach ($this->pieceLines as $pieceLine) {
                $this->xml->startElement('pieceLine');
                $this->xml->writeElement('numberOfPieces', $pieceLine->getNumberOfPieces());
                $this->xml->startElement('pieceMeasurements');
                $this->xml->writeElement('length', $pieceLine->getPieceMeasurements()->getLength());
                $this->xml->writeElement('width', $pieceLine->getPieceMeasurements()->getWidth());
                $this->xml->writeElement('height', $pieceLine->getPieceMeasurements()->getHeight());
                $this->xml->writeElement('weight', $pieceLine->getPieceMeasurements()->getWeight());
                $this->xml->endElement();
                $this->xml->writeElement('pallet', $pieceLine->isPallet() ? 1 : 0);
                $this->xml->endElement();
            }
        }

        $this->xml->endElement();
        $this->xml->endElement();
    }

    protected function sendRequest()
    {

        $headers[] = "Content-type:  text/xml";
        $headers[] = "Authorization: Basic " . base64_encode("$this->userId:$this->password");

        $context = stream_context_create(array(
            'http' => array(
                'header' => $headers,
                'method' => 'POST',
                'content' => $this->getXmlContent()
            ),
            'ssl' => array(
                'verify_peer' => true,
                'verify_peer_name' => true)
        ));

        $output = @file_get_contents($this->getServiceUrl(), false, $context);

        // $http_response_header comes from PHP engine,
        // it's not a part of this code
        // http://php.net/manual/en/reserved.variables.httpresponseheader.php
        if (empty($http_response_header) === false) {
            HTTPHeaders::$headers = $http_response_header;
        }

        return $output;
    }
}