<?php

namespace AppBundle\Controller;
use AppBundle\Entity\ProductTest;
use AppBundle\Entity\Stock;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class GenusController extends Controller
{
    /**
     * @Route("/genus")
     */
    public function showAction()
    {

        $product = new ProductTest();

        $product->name=3;

        $validator = $this->get("validator");


        $errors = $validator->validate($product);

        // replace this example code with whatever you need
        return new Response("errors: ".((string) $errors));
    }

}
