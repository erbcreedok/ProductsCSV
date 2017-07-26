<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 26.07.17
 * Time: 18:50
 */

namespace Acme\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;

class DemoController extends FOSRestController
{
    public function getDemosAction()
    {
        return new Response("ad");

        $data = array("hello" => "world");
        $view = $this->view($data);
        return $this->handleView($view);
    }
}