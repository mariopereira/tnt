<?php

namespace thm\tnt_ec\service\PricingService\entity;

class PieceMeasurements
{
    private float $length;
    public float $width;
    public float $height;
    public float $weight;

    /**
     * @param float $length
     * @param float $width
     * @param float $height
     * @param float $weight
     */
    public function __construct(float $length, float $width, float $height, float $weight)
    {
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->weight = $weight;
    }


    public function getLength(): float
    {
        return $this->length;
    }

    public function setLength(float $length): void
    {
        $this->length = $length;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function setWidth(float $width): void
    {
        $this->width = $width;
    }

    public function getHeight(): float
    {
        return $this->height;
    }

    public function setHeight(float $height): void
    {
        $this->height = $height;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }
}