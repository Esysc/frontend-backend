<?php
// src/Controller/StationController.php
namespace App\Controller;

use App\Repository\StationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class StationController extends AbstractController
{
    #[Route('/api/v1/stations', name: 'api_stations_list', methods: ['GET'])]
    public function listStations(StationRepository $stationRepo): JsonResponse
    {
        // ✅ CAMELCASE - matches your entity properties
        $stations = $stationRepo->findBy([], ['shortName' => 'ASC']);

        $data = array_map(fn($station) => [
            'id' => $station->getId(),
            'shortName' => $station->getShortName(),
            'longName' => $station->getLongName()
        ], $stations);

        return $this->json(['stations' => $data]);
    }
}
