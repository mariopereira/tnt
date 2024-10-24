<?php

namespace thm\tnt_ec\service\PricingService\entity;

class Insurance
{

    private float $insuranceValue;
    private float $goodsValue;

    public function __construct(float $insuranceValue, float $goodsValue)
    {
        $this->insuranceValue = $insuranceValue;
        $this->goodsValue = $goodsValue;
    }

    public function getInsuranceValue(): float
    {
        return $this->insuranceValue;
    }

    public function setInsuranceValue(float $insuranceValue): void
    {
        $this->insuranceValue = $insuranceValue;
    }

    public function getGoodsValue(): float
    {
        return $this->goodsValue;
    }

    public function setGoodsValue(float $goodsValue): void
    {
        $this->goodsValue = $goodsValue;
    }
}