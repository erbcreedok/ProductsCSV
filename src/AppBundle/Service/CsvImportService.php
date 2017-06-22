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

    const MAX_ROWS = 100;

    /**
     * @var bool
     */
    private $isTest;

    /**
     * @var ProductConstructService
     */
    private $productConstructor;

    public function __construct(
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        LoggerInterface $logger,
        ProductConstructService $productConstructor
    ) {
        $this->em = $em;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->productConstructor = $productConstructor;
    }


    /**
     * @param string $fileName
     * @param bool $isTest
     *
     * @return array | int[]
     */
    public function readFile($fileName, $isTest = false) : array
    {
        $this->isTest = $isTest;

        $row = 0;

        $csvHandle = fopen($fileName, "r");

        $fieldNames = fgetcsv($csvHandle); //erasing first row from handler

        while ($result = fgetcsv($csvHandle)) {
            $row++;

            //constructing Product using service helper
            try {
                $product = $this->productConstructor->constructProduct($result);
            } catch (Exception $e) {
                $this->logger->error(sprintf("%s\nat row %d", $e->getMessage(), $row));
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
            }

            //flushing Products separately. After every %MAX_ROWS%
            if ($row % $this::MAX_ROWS == 0) {
                $this->flushProducts();
            }
        }

        //Control Flush
        $this->flushProducts();

        //return count of imported values, and total rows count
        return [count($this->validProducts),$row];
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