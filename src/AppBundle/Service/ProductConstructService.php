<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 20.06.17
 * Time: 18:50
 */

namespace AppBundle\Service;

use AppBundle\Entity\Product;
use Symfony\Component\Config\Definition\Exception\Exception;

class ProductConstructService
{

    const COLUMNS = 6;


    /**
     * @param array $data
     * @return Product
     * @throws Exception
     */
    public function constructProduct(array $data) : Product
    {
        if (count($data)!==$this::COLUMNS) {
            $message = sprintf("Wrong count of columns.\nExpected %d, given %d", $this::COLUMNS, count($data));
            throw new Exception($message);
        }

        foreach ($data as $index => $value) {
            $data[$index] = trim($value);
        }

        if (preg_match('/\W/', $data[0])) {
            throw new Exception(sprintf("Invalid product data.\nIncorrect product code: %s", $data[0]));
        }
        if (!preg_match('/^\d+$/', $data[3])) {
            throw new Exception(sprintf("Invalid product data.\nIncorrect stock size: %s", $data[3]));
        }
        if (!preg_match('/^\d{1,}(\.\d{1,2})?$/', $data[4])) {
            throw new Exception(sprintf("Invalid product data.\nIncorrect cost of product: %s", $data[4]));
        }

        $product = (new Product())
            ->setProductCode($data[0])
            ->setProductName($data[1])
            ->setProductDescription($data[2])
            ->setStockSize((int)$data[3])
            ->setPrice((float)$data[4])
            ->setDtmDiscontinued($data[5]=="yes")
        ;

        return $product;
    }

}