<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 13.06.17
 * Time: 14:32
 */

namespace AppBundle\Entity;

use AppBundle\Traits\ProductConstructor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="tblProductData")
 * @UniqueEntity("productCode")
 * @ORM\HasLifecycleCallbacks
 */
class Product
{

    use ProductConstructor;

    /**
     * @var int
     *
     * @ORM\Column(name="intProductDataId", type="integer", length=10)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductCode", type="string", length=10, nullable=false, unique=true)
     * @Assert\NotBlank()
     */
    private $productCode;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductName", type="string", length=50, nullable=false)
     * @Assert\NotBlank()
     */
    private $productName;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductDesc", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $productDescription;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true)
     */
    private $dtmAdded=null;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     */
    private $dtmDiscontinued=null;

    /**
     * @var DateTime
     *
     *
     * @ORM\Column(name="stmTimestamp", type="datetime", nullable=false)
     */
    private $stmTimestamp;

    /**
     * @var int
     *
     * @ORM\Column(name="intStock", type="integer", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Expression(
     *     "this.getStockSize() >= 10 or this.getPrice()>=5",
     *     message="Products price must be greater than 5.00, if Stock size is less than 10"
     * )
     */
    private $stockSize;

    /**
     * @var float
     *
     * @ORM\Column(name="dcmPrice", precision=9, scale=2 ,type="decimal", nullable=false)
     * @Assert\NotBlank()
     * @Assert\LessThanOrEqual(1000)
     */
    private $price;


    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getProductCode() : string
    {
        return $this->productCode;
    }

    /**
     * @param string $productCode
     *
     * @return Product
     */
    public function setProductCode(string $productCode) : Product
    {
        $this->productCode = $productCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductName() : string
    {
        return $this->productName;
    }

    /**
     * @param string $productName
     *
     * @return Product
     */
    public function setProductName(string $productName) : Product
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductDescription() : string
    {
        return $this->productDescription;
    }

    /**
     * @param string $productDescription
     *
     * @return Product
     */
    public function setProductDescription(string $productDescription) : Product
    {
        $this->productDescription = $productDescription;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDtmAdded() : DateTime
    {
        return $this->dtmAdded;
    }

    /**
     * @param DateTime $dtmAdded
     *
     * @return Product
     */
    public function setDtmAdded(DateTime $dtmAdded) : Product
    {
        $this->dtmAdded = $dtmAdded;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDtmDiscontinued() : DateTime
    {
        return $this->dtmDiscontinued;
    }

    /**
     * @param bool $boolDiscontinued
     *
     * @return Product
     */
    public function setDtmDiscontinued(bool $boolDiscontinued) : Product
    {
        $this->dtmDiscontinued = $boolDiscontinued ? new \DateTime() : null;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStmTimestamp() : DateTime
    {
        return $this->stmTimestamp;
    }

    /**
     * @param DateTime $stmTimestamp
     *
     * @return Product
     */
    public function setStmTimestamp(DateTime $stmTimestamp) : Product
    {
        $this->stmTimestamp = $stmTimestamp;

        return $this;
    }

    /**
     * @return int
     */
    public function getStockSize() : int
    {
        return $this->stockSize;
    }

    /**
     * @param int $stockSize
     *
     * @return Product
     */
    public function setStockSize(int $stockSize) : Product
    {
        $this->stockSize = $stockSize;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice() : float
    {
        return $this->price;
    }

    /**
     * @param float $price
     *
     * @return Product
     */
    public function setPrice($price) : Product
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamp()
    {
        $this->stmTimestamp = new \DateTime("now");
    }


}
