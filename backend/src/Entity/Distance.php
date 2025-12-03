<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'distances')]
#[ORM\UniqueConstraint(name: 'unique_distance', columns: ['line_name', 'from_station_id', 'to_station_id'])]
class Distance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $lineName;

    #[ORM\Column(type: 'string', length: 10)]
    private string $fromStationId;

    #[ORM\Column(type: 'string', length: 10)]
    private string $toStationId;

    #[ORM\Column(type: 'float')]
    private float $distanceKm;

    public function __construct(string $lineName, string $fromStationId, string $toStationId, float $distanceKm)
    {
        $this->lineName = $lineName;
        $this->fromStationId = $fromStationId;
        $this->toStationId = $toStationId;
        $this->distanceKm = $distanceKm;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLineName(): string
    {
        return $this->lineName;
    }

    public function getFromStationId(): string
    {
        return $this->fromStationId;
    }

    public function getToStationId(): string
    {
        return $this->toStationId;
    }

    public function getDistanceKm(): float
    {
        return $this->distanceKm;
    }
}
