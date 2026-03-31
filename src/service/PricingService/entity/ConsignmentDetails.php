<?php

namespace thm\tnt_ec\service\PricingService\entity;

class ConsignmentDetails
{

    private float $totalWeight;
    private float $totalVolume;
    private int $totalNumberOfPieces;

    public function __construct(float $totalWeight, float $totalVolume, int $totalNumberOfPieces)
    {
        $this->totalWeight = $totalWeight;
        $this->totalVolume = $totalVolume;
        $this->totalNumberOfPieces = $totalNumberOfPieces;
    }

    public function getTotalWeight(): float
    {
        return $this->totalWeight;
    }

    public function setTotalWeight(float $totalWeight): void
    {
        $this->totalWeight = $totalWeight;
    }

    public function getTotalVolume(): float
    {
        return $this->totalVolume;
    }

    public function setTotalVolume(float $totalVolume): void
    {
        $this->totalVolume = $totalVolume;
    }

    public function getTotalNumberOfPieces(): int
    {
        return $this->totalNumberOfPieces;
    }

    public function setTotalNumberOfPieces(int $totalNumberOfPieces): void
    {
        $this->totalNumberOfPieces = $totalNumberOfPieces;
    }
}