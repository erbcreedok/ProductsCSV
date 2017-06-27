<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CsvImportCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('csv:import')
            ->setDescription('Imports the mock CSV data file to MySQL.')
            ->addArgument(
                'file',
                InputArgument::OPTIONAL,
                "Enter path to CSV file",
                __DIR__."/../Data/stock.csv"
            )
            ->addOption(
                'test',
                null,
                InputOption::VALUE_NONE,
                "Is this test mode"
            )
            ->addOption(
                'clear',
                null,
                InputOption::VALUE_NONE,
                "Clear DB before import"
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $io = new SymfonyStyle($input, $output);

        $csvImportService = $this->getContainer()->get('csvimporter');

        if ($input->getOption('clear')) {
            $csvImportService->clearProducts();
        }

        $log = $csvImportService->readFile($input->getArgument('file'), $input->getOption("test"));

        if ($log) {
            $io->success(sprintf("CSV successfully imported %d out of %d values", $log["validated"], $log["total"]));
        } else {
            $io->error("Error on importing file: null returned");
        }
    }

}
