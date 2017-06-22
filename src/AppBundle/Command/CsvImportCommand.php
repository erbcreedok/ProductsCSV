<?php
namespace AppBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CsvImportCommand extends ContainerAwareCommand
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    const COLUMNS = 6;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setName('csv:import')
            ->setDescription('Imports the mock CSV data file to MySQL.')
            ->addArgument(
                'file',
                InputArgument::OPTIONAL,
                "Enter path to CSV file",
                "%kernel.root_dir%/../src/AppBundle/Data/stock.csv"
            )
            ->addOption(
                'test',
                null,
                InputArgument::OPTIONAL,
                "Is this test mode",
                false

            )
            ->addOption(
                'clear',
                null,
                InputArgument::OPTIONAL,
                "Clear DB before import",
                false
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

        $io->success(sprintf("CSV successfully imported %d out of %d values", $log[0], $log[1]));
    }

}
