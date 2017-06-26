<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 19.06.17
 * Time: 18:15
 */

namespace AppBundle\Service;

use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CsvImportService
{

    /**
     * @var EntityManagerInterface
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
     * @var array|Product[]
     */
    private $validProducts = [];

    /**
     * @var bool
     */
    private $isTest;

    /**
     * @var int
     */
    private $maxRows;

    public function __construct(
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        LoggerInterface $logger,
        $maxRows
    ) {
        $this->em = $em;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->maxRows = $maxRows;
    }


    /**
     * @param string $fileName
     * @param bool $isTest
     *
     * @return array | null
     */
    public function readFile($fileName, $isTest = false)
    {
        $this->isTest = $isTest;

        $row = 0;

        if (file_exists($fileName)) {
            $csvHandle = fopen($fileName, "r");
        } else {
            $message = sprintf("fopen(%s): failed to open stream: No such file or directory", $fileName);
            $this->logger->error($message);
            return null;
        }

        $columnsCount = count(fgetcsv($csvHandle)); //erasing first row from handler

        $log = ["parse_errors"=>0, "validate_errors"=>0, "construct_errors"=>0];

        while ($result = fgetcsv($csvHandle)) {
            $row++;

            //constructing Product using service helper
            if (count($result)!==$columnsCount) {
                $message = sprintf("Wrong count of columns.\nExpected %d, given %d", $columnsCount, count($result));
                $log["parse_errors"]+=1;
                $this->logger->error($message);
                continue;
            }

            try {
                ($product = new Product())->constructProduct($result);
            } catch (Exception $e) {
                $this->logger->error(sprintf("%s\nat row %d", $e->getMessage(), $row));
                $log["construct_errors"]+=1;
                continue;
            }

            //validating Product using validation constraints
            $errors = $this->validator->validate($product);

            if (count($errors)==0) {
                if (array_key_exists($product->getProductCode(), $this->validProducts)) {
                    $this->em->remove($this->validProducts[$product->getProductCode()]);
                }
                $this->validProducts[$product->getProductCode()]=$product;
                $this->em->persist($product);
            } else {
                $this->logger->error(sprintf("%s\nat row: %d", (string)$errors, $row));
                $log["validate_errors"]+=1;
            }

            //flushing Products separately. After every %MAX_ROWS%
            if ($row % $this->maxRows == 0) {
                $this->flushProducts();
            }
        }

        //Control Flush
        $this->flushProducts();

        //return count of imported values, and total rows count
        return ["validated"=>count($this->validProducts), "total"=>$row, "errors"=>$log];
    }


    public function flushProducts()
    {
        if ($this->isTest) {
            return;
        }

        try {
            $this->em->flush();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function clearProducts()
    {
        return $this->em
            ->createQuery("DELETE AppBundle:Product p")
            ->getResult()
        ;
    }
}