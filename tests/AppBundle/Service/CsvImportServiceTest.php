<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 22.06.17
 * Time: 20:11
 */

namespace Tests\AppBundle\Service;

use AppBundle\Service\CsvImportService;
use AppBundle\Service\ProductConstructor;
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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $em;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $validator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $logger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
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

        $this->productConstructor = $this->getMockBuilder(ProductConstructor::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->service = new CsvImportService(
            $this->em,
            $this->validator,
            $this->logger,
            $this->productConstructor,
            100
        );
    }


    //Test with default CSV File
    public function testDefaultCsvImport()
    {
        //Constructor should work 27 time, because there is 2 problem lines from 29
        $this->productConstructor
            ->expects($this->exactly(27))
            ->method("constructProduct")
        ;

        //test-case should never run flush
        $this->em
            ->expects($this->never())
            ->method('flush')
        ;

        $result = $this->service->readFile(__DIR__."/../../../src/AppBundle/Data/stock.csv", true);

        //only 2 lines of CSV are bad parsing, wrong count of columns
        $this->assertSame(2, $result["errors"]['parse_errors']);

        //Should not have effects at validation in Mock test, no responsibility
        $this->assertSame(0, $result["errors"]['construct_errors']);

        //Should not have effects at validation in Mock test, no responsibility
        $this->assertSame(0, $result["errors"]['validate_errors']);
    }

    //Test with CSV file with no errors
    public function testClearCsvImport()
    {
        //test-case should never run flush
        $this->em
            ->expects($this->never())
            ->method('flush')
        ;

        //Clear CSV should not have any error messages
        $this->logger
            ->expects($this->never())
            ->method("error")
        ;

        $result = $this->service->readFile(__DIR__."/../../../src/AppBundle/Data/stock-good.csv", true);

        //should be no Parsing errors
        $this->assertSame(0, $result["errors"]['parse_errors']);

        //Should not have effects at validation in Mock test, no responsibility
        $this->assertSame(0, $result["errors"]['construct_errors']);

        //Should not have effects at validation in Mock test, no responsibility
        $this->assertSame(0, $result["errors"]['validate_errors']);
    }

    public function testFileNotFound()
    {
        $file = __DIR__."/../../../src/AppBundle/Data/stock-unknown.csv";

        //test-case should never run flush
        $this->em
            ->expects($this->never())
            ->method('flush')
        ;


        //File not found error message should be logged
        $this->logger
            ->expects($this->once())
            ->method("error")
            ->with($this->identicalTo(sprintf("fopen(%s): failed to open stream: No such file or directory", $file)))
        ;

        $result = $this->service->readFile($file, true);

        //return has to be null
        $this->assertSame(null, $result);
    }
}