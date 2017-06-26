<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 22.06.17
 * Time: 20:11
 */

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Product;
use AppBundle\Service\CsvImportService;
use AppBundle\Service\ProductConstructService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Debug\Exception\ContextErrorException;
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

        $this->productConstructor = $this->getMockBuilder(ProductConstructService::class)
            ->getMock()
        ;

        $this->service = new CsvImportService($this->em, $this->validator, $this->logger, 100);
    }


    //Test with default CSV File
    public function testDefaultCsvImport()
    {
        //Validator should work 26 time, because there is 4 problem lines from 29
        $this->validator
            ->expects($this->exactly(25))
            ->method("validate")
        ;

        //test-case should never run flush
        $this->em
            ->expects($this->never())
            ->method('flush')
        ;

        $result = $this->service->readFile(__DIR__."/../../../src/AppBundle/Data/stock.csv", true);

        //only 2 lines of CSV are bad parsing, wrong count of columns
        $this->assertSame(2, $result["errors"]['parse_errors']);

        //only 2 lines has bad entity constructor(P0007->empty stock count, P0015->Price is not number)
        $this->assertSame(2, $result["errors"]['construct_errors']);

        //Should not be effects at validation in Mock test
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

        //should be no constructor errors
        $this->assertSame(0, $result["errors"]['construct_errors']);

        //Should not be effects at validation in Mock test
        $this->assertSame(0, $result["errors"]['validate_errors']);

        //Should be 25 validated lines
        $this->assertSame(25, $result["validated"]);

        //Should be 25 total lines
        $this->assertSame(25, $result["total"]);

        //All rows from total should be validated
        $this->assertSame($result["validated"], $result["total"]);
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