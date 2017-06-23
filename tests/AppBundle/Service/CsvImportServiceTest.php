<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 22.06.17
 * Time: 20:11
 */

namespace Tests\AppBundle\Controller;

use AppBundle\Service\CsvImportService;
use AppBundle\Service\ProductConstructService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CsvImportServiceTest extends TestCase
{

    /**
     * @var CsvImportService
     */
    private $service;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProductConstructService
     */
    private $productConstructor;

    public function setUp()
    {
        $this->em = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->validator = $this->getMockBuilder(ValidatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;


        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->productConstructor = $this->getMockBuilder(ProductConstructService::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->service = new CsvImportService($this->em, $this->validator, $this->logger, $this->productConstructor);
    }

    public function testCsvImport()
    {
        //validates 29 times, should be 27
        $this->validator
            ->expects($this->exactly(27))
            ->method("validate")
        ;

        //persists 29 times, should be 23
        $this->em
            ->expects($this->exactly(23))
            ->method("persist")
        ;

        //test-case should never run flush
        $this->em
            ->expects($this->never())
            ->method('flush')
        ;

        $result = $this->service->readFile("%kernel.root_dir%/../src/AppBundle/Data/stock.csv", true);

        $this->assertSame([23,29], $result);
    }

}