<?php

namespace App\Controller;

use App\DTO\RouteRequest;
use App\Service\RouteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1')]
class RouteController extends AbstractController
{
    public function __construct(private RouteService $routeService)
    {
    }

    #[Route('/routes', name: 'create_route', methods: ['POST'])]
    public function createRoute(Request $request): JsonResponse
    {
        try {
            // authentication enforced via Symfony security (JWT)
            $data = json_decode($request->getContent(), true) ?? [];

            $routeRequest = RouteRequest::fromArray($data);

            $route = $this->routeService->calculateRoute(
                $routeRequest->fromStationId,
                $routeRequest->toStationId,
                $routeRequest->analyticCode
            );

            return new JsonResponse([
                'id' => $route->getId(),
                'fromStationId' => $route->getFromStationId(),
                'toStationId' => $route->getToStationId(),
                'analyticCode' => $route->getAnalyticCode(),
                'distanceKm' => $route->getDistanceKm(),
                'path' => $route->getPath(),
                'createdAt' => $route->getCreatedAt()->format('c'),
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
                'code' => 'INVALID_REQUEST',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'An error occurred',
                'code' => 'INTERNAL_ERROR',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
