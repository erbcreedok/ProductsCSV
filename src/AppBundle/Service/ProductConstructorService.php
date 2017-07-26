<?php
/**
 * Created by PhpStorm.
 * User: Assanali
 * Date: 7/20/17
 * Time: 14:06
 */

namespace AppBundle\Service;

use AppBundle\Entity\Product;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Yaml\Yaml;

class ProductConstructorService
{
    public function constructProduct(array $data) : Product
    {
        $productName = $data['productName'];
        $productCode = $data['productCode'];
        $productDesc = $data['productDescription'];
        $stockSize = $data['stock'];
        $price = $data['cost'];
        $discontinued = $data['isDiscontinued'];


        foreach ($data as $index => $value) {
            $data[$index] = trim($value);
        }

        if (preg_match('/\W/', $productCode)) {
            throw new Exception(sprintf("Invalid product data.\nIncorrect product code: %s", $productCode));
        }
        if (!preg_match('/^\d+$/', $stockSize)) {
            throw new Exception(sprintf("Invalid product data.\nIncorrect stock size: %s", $stockSize));
        }
        if (!preg_match('/^\d{1,}(\.\d{1,2})?$/', $price)) {
            throw new Exception(sprintf("Invalid product data.\nIncorrect cost of product: %s", $price));
        }

        $product = (new Product())
            ->setProductCode($productCode)
            ->setProductName($productName)
            ->setProductDescription($productDesc)
            ->setStockSize((int)$stockSize)
            ->setPrice((float)$price)
            ->setDtmDiscontinued($discontinued)
            ->setDtmAdded(new \DateTime());

        return $product;
    }
}
