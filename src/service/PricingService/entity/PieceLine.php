<?php

namespace thm\tnt_ec\service\PricingService\entity;

class PieceLine
{

    public int $numberOfPieces;
    public bool $isPallet;
    public PieceMeasurements $pieceMeasurements;

    public function __construct(int $numberOfPieces, PieceMeasurements $pieceMeasurements, bool $isPallet = false)
    {
        $this->numberOfPieces = $numberOfPieces;
        $this->pieceMeasurements = $pieceMeasurements;
        $this->isPallet = $isPallet;
    }

    public function getNumberOfPieces(): int
    {
        return $this->numberOfPieces;
    }

    public function setNumberOfPieces(int $numberOfPieces): void
    {
        $this->numberOfPieces = $numberOfPieces;
    }

    public function getPieceMeasurements(): PieceMeasurements
    {
        return $this->pieceMeasurements;
    }

    public function setPieceMeasurements(PieceMeasurements $pieceMeasurements): void
    {
        $this->pieceMeasurements = $pieceMeasurements;
    }

    public function isPallet(): bool
    {
        return $this->isPallet;
    }

    public function setIsPallet(bool $isPallet): PieceLine
    {
        $this->isPallet = $isPallet;
        return $this;
    }
}