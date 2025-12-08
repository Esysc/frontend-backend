<?php

namespace App\Algorithm;

class DijkstraAlgorithm
{
    /**
     * Calculate shortest path between two nodes using Dijkstra's algorithm
     * @param array<string, array<string, float>> $graph Adjacency list with distances
     * @param string $start Starting node
     * @param string $end Ending node
     * @return array{distance: float, path: array<string>}
     * @throws \InvalidArgumentException
     */
    public function findShortestPath(array $graph, string $start, string $end): array
    {
        if (!isset($graph[$start])) {
            throw new \InvalidArgumentException("Start station '$start' not found in graph");
        }

        if (!isset($graph[$end])) {
            throw new \InvalidArgumentException("End station '$end' not found in graph");
        }

        $distances = [];
        $previous = [];
        $unvisited = [];

        foreach (array_keys($graph) as $node) {
            $distances[$node] = PHP_FLOAT_MAX;
            $previous[$node] = null;
            $unvisited[$node] = true;
        }

        $distances[$start] = 0;

        while (!empty($unvisited)) {
            $current = null;
            $minDistance = PHP_FLOAT_MAX;

            foreach ($unvisited as $node => $visited) {
                if ($distances[$node] < $minDistance) {
                    $current = $node;
                    $minDistance = $distances[$node];
                }
            }

            if ($current === null || $distances[$current] === PHP_FLOAT_MAX) {
                throw new \InvalidArgumentException("No path found between '$start' and '$end'");
            }

            unset($unvisited[$current]);

            foreach ($graph[$current] as $neighbor => $weight) {
                if (isset($unvisited[$neighbor])) {
                    $alt = $distances[$current] + $weight;
                    if ($alt < $distances[$neighbor]) {
                        $distances[$neighbor] = $alt;
                        $previous[$neighbor] = $current;
                    }
                }
            }
        }

        if ($distances[$end] === PHP_FLOAT_MAX) {
            throw new \InvalidArgumentException("No path found between '$start' and '$end'");
        }

        $path = $this->reconstructPath($previous, $start, $end);

        return [
            'distance' => $distances[$end],
            'path' => $path,
        ];
    }

    /**
     * @param array<string, ?string> $previous
     * @param string $start
     * @param string $end
     * @return array<string>
     */
    private function reconstructPath(array $previous, string $start, string $end): array
    {
        $path = [];
        $current = $end;

        while ($current !== null) {
            array_unshift($path, $current);
            $current = $previous[$current];
        }

        return $path;
    }
}
