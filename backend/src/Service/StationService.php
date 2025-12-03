<?php

namespace App\Service;

use App\Repository\StationRepository;

class StationService
{
    public function __construct(private StationRepository $stationRepository)
    {
    }

    public function getAllStations(): array
    {
        return $this->stationRepository->findAll();
    }

    public function getStationById(string $id)
    {
        return $this->stationRepository->findById($id);
    }
}
