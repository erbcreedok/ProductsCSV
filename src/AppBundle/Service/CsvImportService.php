<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 19.06.17
 * Time: 18:15
 */

namespace AppBundle\Service;

use AppBundle\Entity\Product;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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



    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->logger = $logger;
    }


    /**
     * @param string $fileName
     *
     * @return array | int[]
     */
    public function readFile($fileName) : array
    {
        $productConstructor = new ProductConstructService();

        $row = $validated = 0;

        $csvHandle = fopen($fileName, "r");

        $fieldNames = fgetcsv($csvHandle); //erasing first row from handler

        while ($result = fgetcsv($csvHandle)) {
            $row++;

            //constructing Product using service helper
            try {
                $product = $productConstructor->constructProduct($result);
            } catch (Exception $e) {
                $this->logger->error(sprintf("%s\nat row %d", $e->getMessage(), $row));
                continue;
            }

            //validating Product using validation constraints
            $errors = $this->validator->validate($product);

            if (count($errors)==0) {
                $this->validProducts[$product->getProductCode()]=$product;
            } else {
                $this->logger->error(sprintf("%s\nat row: %d", (string)$errors, $row));
            }

            //flushing Products separately. After every %MAX_ROWS%
            if ($row % $this::MAX_ROWS == 0) {
                $validated += $this->flushProducts();
            }
        }

        //Control Flush
        $validated += $this->flushProducts();

        //return count of imported values, and total rows count
        return [$validated,$row];
    }

    /**
     * @return int
     */
    public function flushProducts() : int
    {
        foreach ($this->validProducts as $product) {
            $this->em->persist($product);
        }

        try {
            $this->em->flush();
            $validated = count($this->validProducts);
            $this->validProducts=[];
            return $validated;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return 0;
        }
    }
}