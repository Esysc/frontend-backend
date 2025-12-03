<?php

namespace App\Tests\Algorithm;

use App\Algorithm\DijkstraAlgorithm;
use PHPUnit\Framework\TestCase;

class DijkstraAlgorithmTest extends TestCase
{
    private DijkstraAlgorithm $algorithm;

    protected function setUp(): void
    {
        $this->algorithm = new DijkstraAlgorithm();
    }

    public function testFindShortestPathSimple(): void
    {
        $graph = [
            'A' => ['B' => 1, 'C' => 4],
            'B' => ['A' => 1, 'C' => 2, 'D' => 5],
            'C' => ['A' => 4, 'B' => 2, 'D' => 1],
            'D' => ['B' => 5, 'C' => 1],
        ];

        $result = $this->algorithm->findShortestPath($graph, 'A', 'D');

        $this->assertEquals(4, $result['distance']);
        $this->assertEquals(['A', 'B', 'C', 'D'], $result['path']);
    }

    public function testFindShortestPathSameStation(): void
    {
        $graph = [
            'A' => ['B' => 1],
            'B' => ['A' => 1],
        ];

        $result = $this->algorithm->findShortestPath($graph, 'A', 'A');

        $this->assertEquals(0, $result['distance']);
        $this->assertEquals(['A'], $result['path']);
    }

    public function testFindShortestPathInvalidStart(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $graph = [
            'A' => ['B' => 1],
            'B' => ['A' => 1],
        ];

        $this->algorithm->findShortestPath($graph, 'X', 'A');
    }

    public function testFindShortestPathDisconnected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("No path found");

        $graph = [
            'A' => ['B' => 1],
            'B' => ['A' => 1],
            'C' => ['D' => 1],
            'D' => ['C' => 1],
        ];

        $this->algorithm->findShortestPath($graph, 'A', 'C');
    }
}
