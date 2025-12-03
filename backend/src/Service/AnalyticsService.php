<?php

namespace App\Service;

use App\Repository\RouteRepository;
use DateTime;

class AnalyticsService
{
    public function __construct(private RouteRepository $routeRepository)
    {
    }

    /**
     * @return array<array{analyticCode: string, totalDistanceKm: float, periodStart: ?string, periodEnd: ?string, group: ?string}>
     */
    public function getDistancesByAnalyticCode(?DateTime $from, ?DateTime $to, ?string $groupBy = null): array
    {
        $routes = $this->routeRepository->findAllByDateRange($from, $to);

        $aggregated = [];

        foreach ($routes as $route) {
            $code = $route->getAnalyticCode();
            $distance = $route->getDistanceKm();
            $createdAt = $route->getCreatedAt();

            $groupKey = $this->getGroupKey($groupBy, $createdAt);

            $key = $code . '|' . $groupKey;

            if (!isset($aggregated[$key])) {
                $aggregated[$key] = [
                    'analyticCode' => $code,
                    'totalDistanceKm' => 0,
                    'periodStart' => $from?->format('Y-m-d'),
                    'periodEnd' => $to?->format('Y-m-d'),
                    'group' => $groupKey !== '' ? $groupKey : null,
                ];
            }

            $aggregated[$key]['totalDistanceKm'] += $distance;
        }

        return array_values($aggregated);
    }

    private function getGroupKey(?string $groupBy, DateTime $date): string
    {
        if ($groupBy === null || $groupBy === 'none') {
            return '';
        }

        return match ($groupBy) {
            'day' => $date->format('Y-m-d'),
            'month' => $date->format('Y-m'),
            'year' => $date->format('Y'),
            default => '',
        };
    }
}
