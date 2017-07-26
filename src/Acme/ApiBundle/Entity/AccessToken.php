<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 26.07.17
 * Time: 17:20
 */

namespace Acme\ApiBundle\Entity;

use FOS\OAuthServerBundle\Model\AccessToken as BaseAccessToken;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("oauth2_access_tokens")
 * @ORM\Entity
 */
class AccessToken extends BaseAccessToken
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Client
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $user;

    public function __construct()
    {
        parent::__construct();
    }
}
