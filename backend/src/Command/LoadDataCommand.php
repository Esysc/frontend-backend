<?php

namespace App\Command;

use App\Entity\Distance;
use App\Entity\Station;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:load-data',
    description: 'Load stations and distances from JSON files',
)]
class LoadDataCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Loading stations...');

        // Look for files in project root or current directory
        $stationsFile = file_exists(__DIR__ . '/../../stations.json')
            ? __DIR__ . '/../../stations.json'
            : '/app/stations.json';
        $distancesFile = file_exists(__DIR__ . '/../../distances.json')
            ? __DIR__ . '/../../distances.json'
            : '/app/distances.json';

        if (!file_exists($stationsFile)) {
            $output->writeln('<error>stations.json not found</error>');
            return Command::FAILURE;
        }

        if (!file_exists($distancesFile)) {
            $output->writeln('<error>distances.json not found</error>');
            return Command::FAILURE;
        }

        $stationsData = json_decode(file_get_contents($stationsFile), true);
        $distancesData = json_decode(file_get_contents($distancesFile), true);

        foreach ($stationsData as $stationData) {
            $station = new Station(
                (string) $stationData['id'],
                $stationData['shortName'],
                $stationData['longName']
            );
            $this->entityManager->persist($station);
        }

        $this->entityManager->flush();
        $output->writeln(sprintf('Loaded %d stations', count($stationsData)));

        $output->writeln('Loading distances...');

        foreach ($distancesData as $lineData) {
            foreach ($lineData['distances'] as $distanceData) {
                $distance = new Distance(
                    $lineData['name'],
                    $distanceData['parent'],
                    $distanceData['child'],
                    $distanceData['distance']
                );
                $this->entityManager->persist($distance);
            }
        }

        $this->entityManager->flush();
        $output->writeln(sprintf('Loaded %d distance records', count($distancesData)));

        $output->writeln('<info>Data loaded successfully!</info>');
        return Command::SUCCESS;
    }
}
