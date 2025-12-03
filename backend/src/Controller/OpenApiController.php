<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Yaml\Yaml;

#[Route('/api/v1')]
class OpenApiController extends AbstractController
{
    #[Route('/doc.json', name: 'api_openapi', methods: ['GET'])]
    public function doc(Request $request): Response
    {
        $specPath = $this->getParameter('kernel.project_dir') . '/openapi.yml';

        if (!file_exists($specPath)) {
            return new Response('openapi.yml not found', Response::HTTP_NOT_FOUND);
        }

        try {
            $parsed = Yaml::parseFile($specPath);
            $json = json_encode($parsed, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            return new Response($json, Response::HTTP_OK, ['Content-Type' => 'application/json']);
        } catch (\Throwable $e) {
            return new Response('Failed to parse openapi.yml: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
