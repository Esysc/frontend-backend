<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

#[ORM\Entity]
#[ORM\Table(name: 'routes')]
class Route
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 10)]
    private string $fromStationId;

    #[ORM\Column(type: 'string', length: 10)]
    private string $toStationId;

    #[ORM\Column(type: 'string', length: 100)]
    private string $analyticCode;

    #[ORM\Column(type: 'float')]
    private float $distanceKm;

    #[ORM\Column(type: 'json')]
    private array $path;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    public function __construct(string $fromStationId, string $toStationId, string $analyticCode, float $distanceKm, array $path)
    {
        $this->fromStationId = $fromStationId;
        $this->toStationId = $toStationId;
        $this->analyticCode = $analyticCode;
        $this->distanceKm = $distanceKm;
        $this->path = $path;
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromStationId(): string
    {
        return $this->fromStationId;
    }

    public function getToStationId(): string
    {
        return $this->toStationId;
    }

    public function getAnalyticCode(): string
    {
        return $this->analyticCode;
    }

    public function getDistanceKm(): float
    {
        return $this->distanceKm;
    }

    public function getPath(): array
    {
        return $this->path;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
