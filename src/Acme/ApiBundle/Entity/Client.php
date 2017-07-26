<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 26.07.17
 * Time: 17:16
 */

namespace Acme\ApiBundle\Entity;

use FOS\OAuthServerBundle\Model\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Client
 * @package Acme\ApiBundle\Entity
 *
 * @ORM\Table("oauth2_clients")
 * @ORM\Entity
 */
class Client extends BaseClient
{

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }
}
