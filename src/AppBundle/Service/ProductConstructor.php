<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 23.06.17
 * Time: 19:58
 */

namespace AppBundle\Service;

use AppBundle\Entity\Product;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Yaml\Yaml;

class ProductConstructor
{
    public function constructProduct(array $data) : Product
    {

        $columnTitles = Yaml::parse(file_get_contents(__DIR__."/../Data/ColumnNames.yml"));

        $productCode = $data[$columnTitles['product_code']];
        $productName = $data[$columnTitles['product_name']];
        $productDesc = $data[$columnTitles['product_description']];
        $stockSize = $data[$columnTitles['stock_size']];
        $price = $data[$columnTitles['price']];
        $discontinued = $data[$columnTitles['discontinued']];


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
            ->setDtmDiscontinued($discontinued=="yes")
        ;

        return $product;
    }

}