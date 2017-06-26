<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 23.06.17
 * Time: 19:58
 */

namespace AppBundle\Traits;

use Symfony\Component\Config\Definition\Exception\Exception;

trait ProductConstructor
{
    public function constructProduct(array $data)
    {

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

        $this->setProductCode($data[0]);
        $this->setProductName($data[1]);
        $this->setProductDescription($data[2]);
        $this->setStockSize((int)$data[3]);
        $this->setPrice((float)$data[4]);
        $this->setDtmDiscontinued($data[5]=="yes");
    }

}