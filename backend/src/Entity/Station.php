<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'stations')]
class Station
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 10)]
    private string $id;

    #[ORM\Column(type: 'string', length: 100)]
    private string $shortName;

    #[ORM\Column(type: 'string', length: 255)]
    private string $longName;

    public function __construct(string $id, string $shortName, string $longName)
    {
        $this->id = $id;
        $this->shortName = $shortName;
        $this->longName = $longName;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getLongName(): string
    {
        return $this->longName;
    }
}
