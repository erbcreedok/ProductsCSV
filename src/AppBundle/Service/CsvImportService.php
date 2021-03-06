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
     * @var ProductConstructor
     */
    private $productConstructor;

    /**
     * @var int
     */
    private $maxRows;

    /**
     * @var array|Product[]
     */
    private $validProducts = [];

    /**
     * @var bool
     */
    private $isTest;

    public function __construct(
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        LoggerInterface $logger,
        ProductConstructor $productConstructor,
        int $maxRows
    ) {
        $this->em = $em;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->productConstructor = $productConstructor;
        $this->maxRows = $maxRows;
    }


    /**
     * @param string $fileName
     * @param bool $isTest
     *
     * @return array | null
     */
    public function readFile(string $fileName, bool $isTest = false): ?array
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

        $columnsTitles = fgetcsv($csvHandle); //reading titles of CSV file
        $columnsCount = count($columnsTitles); //counting columns

        $log = ["parse_errors"=>0, "validate_errors"=>0, "construct_errors"=>0];

        while ($result = fgetcsv($csvHandle)) {
            $row++;

            //Check for columns count
            if (count($result)!==$columnsCount) {
                $message = sprintf("Wrong count of columns.\nExpected %d, given %d", $columnsCount, count($result));
                $log["parse_errors"]+=1;
                $this->logger->error($message);
                continue;
            }

            //rename indexes by names of CSV columns
            foreach ($result as $key => $column) {
                $result[$columnsTitles[$key]] = $result[$key];
                unset($result[$key]);
            }

            //constructing Product using service helper
            try {
                $product = $this->productConstructor->constructProduct($result);
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

    public function clearProducts()
    {
        $this->em
            ->createQuery("DELETE AppBundle:Product p")
            ->getResult();
    }
}