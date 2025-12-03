<?php

namespace App\Service;

use App\Algorithm\DijkstraAlgorithm;
use App\Entity\Route;
use App\Repository\DistanceRepository;
use App\Repository\RouteRepository;
use Doctrine\ORM\EntityManagerInterface;

class RouteService
{
    public function __construct(
        private DijkstraAlgorithm $dijkstra,
        private DistanceRepository $distanceRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function calculateRoute(string $fromStationId, string $toStationId, string $analyticCode): Route
    {
        $graph = $this->buildGraph();

        $result = $this->dijkstra->findShortestPath($graph, $fromStationId, $toStationId);

        $route = new Route(
            $fromStationId,
            $toStationId,
            $analyticCode,
            $result['distance'],
            $result['path']
        );

        $this->entityManager->persist($route);
        $this->entityManager->flush();

        return $route;
    }

    private function buildGraph(): array
    {
        $distances = $this->distanceRepository->findAll();
        $graph = [];

        foreach ($distances as $distance) {
            $from = $distance->getFromStationId();
            $to = $distance->getToStationId();
            $dist = $distance->getDistanceKm();

            if (!isset($graph[$from])) {
                $graph[$from] = [];
            }
            if (!isset($graph[$to])) {
                $graph[$to] = [];
            }

            $graph[$from][$to] = $dist;
            $graph[$to][$from] = $dist;
        }

        return $graph;
    }
}
