<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\ApiDocGenerator;
use function Symfony\Component\Debug\Tests\testHeader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class ProductApiController
 * @package AppBundle\Controller
 *
 */
class ProductApiController extends FOSRestController
{


    /**
     * @return array
     * @Rest\Get("/products")
     */

    public function cgetAction() {
          $products = $this->getDoctrine()->getRepository('AppBundle:Product')->findAll();
          return $products;
//          return $this->get('crv.doctrine_entity_repository.product')->createFindAllQuery()->getResult();

    }


    /**
     * @param int $id
     * @return array
     * @Rest\Get("/products/{id}")
     */
    public function idAction(int $id) {
        $singleProduct = $this->getDoctrine()->getRepository('AppBundle:Product')->find($id);
        return $singleProduct;

    }


}