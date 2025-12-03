<?php

namespace App\DTO;

class RouteRequest
{
    public function __construct(
        public string $fromStationId,
        public string $toStationId,
        public string $analyticCode,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['fromStationId'] ?? throw new \InvalidArgumentException('fromStationId is required'),
            $data['toStationId'] ?? throw new \InvalidArgumentException('toStationId is required'),
            $data['analyticCode'] ?? throw new \InvalidArgumentException('analyticCode is required'),
        );
    }
}
