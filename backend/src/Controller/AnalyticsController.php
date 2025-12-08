<?php

namespace App\Controller;

use App\Service\AnalyticsService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1')]
class AnalyticsController extends AbstractController
{
    public function __construct(private AnalyticsService $analyticsService)
    {
    }

    #[Route('/stats/distances', name: 'get_analytics', methods: ['GET'])]
    public function getAnalytics(Request $request): JsonResponse
    {
        try {
            $from = $request->query->get('from');
            $to = $request->query->get('to');
            $groupBy = $request->query->get('groupBy', 'none');

            $fromDate = $from ? DateTime::createFromFormat('Y-m-d', $from) : null;
            $toDate = $to ? DateTime::createFromFormat('Y-m-d', $to) : null;

            if ($from && !$fromDate) {
                return new JsonResponse([
                    'message' => 'Invalid from date format (expected Y-m-d)',
                    'code' => 'INVALID_DATE_FORMAT',
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($to && !$toDate) {
                return new JsonResponse([
                    'message' => 'Invalid to date format (expected Y-m-d)',
                    'code' => 'INVALID_DATE_FORMAT',
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($fromDate && $toDate && $fromDate > $toDate) {
                return new JsonResponse([
                    'message' => 'from date cannot be after to date',
                    'code' => 'INVALID_DATE_RANGE',
                ], Response::HTTP_BAD_REQUEST);
            }

            $items = $this->analyticsService->getDistancesByAnalyticCode($fromDate, $toDate, $groupBy);

            return new JsonResponse([
                'from' => $fromDate?->format('Y-m-d'),
                'to' => $toDate?->format('Y-m-d'),
                'groupBy' => $groupBy,
                'items' => $items,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'An error occurred',
                'code' => 'INTERNAL_ERROR',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
