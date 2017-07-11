<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 13.06.17
 * Time: 14:32
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMSSerializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ProductRepository")
 * @ORM\Table(name="tblProductData")
 * @UniqueEntity("productCode")
 * @ORM\HasLifecycleCallbacks
 * @JMSSerializer\ExclusionPolicy("all")
 */
class Product implements  \JsonSerializable
{

    /**
     * @var int
     *
     * @ORM\Column(name="intProductDataId", type="integer", length=10)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMSSerializer\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductCode", type="string", length=10, nullable=false, unique=true)
     * @Assert\NotBlank()
     * @JMSSerializer\Expose
     */
    private $productCode;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductName", type="string", length=50, nullable=false)
     * @Assert\NotBlank()
     * @JMSSerializer\Expose
     */
    private $productName;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductDesc", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @JMSSerializer\Expose
     */
    private $productDescription;

    /**
     * @var \DateTime
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true)
     * @JMSSerializer\Expose
     */
    private $dtmAdded=null;

    /**
     * @var \DateTime
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     * @JMSSerializer\Expose
     */
    private $dtmDiscontinued=null;

    /**
     * @var \DateTime
     * @ORM\Column(name="stmTimestamp", type="datetime", nullable=false)
     * @JMSSerializer\Expose
     */
    private $stmTimestamp;

    /**
     * @var int
     * @JMSSerializer\Expose
     * @ORM\Column(name="intStock", type="integer", nullable=false, options={"unsigned"=true})
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\Expression(
     *     "this.getStockSize() >= 10 or this.getPrice()>=5",
     *     message="Products price must be greater than 5.00, if Stock size is less than 10"
     * )
     */
    private $stockSize;

    /**
     * @var float
     * @JMSSerializer\Expose
     * @ORM\Column(name="dcmPrice",
     *     precision=20,
     *     scale=2,
     *     type="decimal",
     *     nullable=false,
     *     columnDefinition="DECIMAL UNSIGNED NOT NULL")
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(0)
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
     * @return \DateTime
     */
    public function getDtmAdded() : \DateTime
    {
        return $this->dtmAdded;
    }

    /**
     * @param \DateTime $dtmAdded
     *
     * @return Product
     */
    public function setDtmAdded(\DateTime $dtmAdded) : Product
    {
        $this->dtmAdded = $dtmAdded;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDtmDiscontinued() : \DateTime
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
     * @return \DateTime
     */
    public function getStmTimestamp() : \DateTime
    {
        return $this->stmTimestamp;
    }

    /**
     * @param \DateTime $stmTimestamp
     *
     * @return Product
     */
    public function setStmTimestamp(\DateTime $stmTimestamp) : Product
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
    public function setPrice(float $price) : Product
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


    function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'product_name' => $this->productName,
            'product_code' => $this->productCode,
            'product_desc' => $this->productDescription,
            'date_add' => $this->dtmAdded,
            'date_disc' => $this->dtmDiscontinued,
            'timestamp' => $this->stmTimestamp,
            'stock' => $this->stockSize,
            'price' => $this->price,
        ];
    }

}
