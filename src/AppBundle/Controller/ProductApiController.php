<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Repository\ProductRepository;
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
use Symfony\Component\HttpKernel\Exception\HttpException;
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

    public function getProducts() {
          $products = $this->getDoctrine()->getRepository('AppBundle:Product')->findAll();
          return $products;
//          return $this->get('crv.doctrine_entity_repository.product')->createFindAllQuery()->getResult();

    }


    /**
     * @param int $id
     * @return object
     * @Rest\Get("/products/{id}")
     */
    public function getProduct(int $id) {
        $singleProduct = $this->getDoctrine()->getRepository('AppBundle:Product')->find($id);
        return $singleProduct;

    }


    /**
     * @param Request $request
     * @Rest\Post("/products/")
     * @return string
     *
     */
    public function addProduct(Request $request) {
//        $prName = $request->get('product_name');
//        $prDesck = $request->get('product_description');
//        $prCode = $request->get('product_code');
////        $dtmAdded = $request->get('dtm_added');
////        $timestamp = $request->get('stm_timestamp');
//        $stock = $request->get('stock_size');
//        $price = $request->get('price');
//
//        $product->setProductDescription($prDesck);
//        $product->setProductCode($prCode);
//        $product->setDtmAdded(new \DateTime());
////        $product->setDtmDiscontinued($dtmDiscontinued);
//        $product->setStmTimestamp(new \DateTime());
//        $product->setStockSize($stock);
//        $product->setPrice($price);
//        $product->setProductName($prName);
//
//        $em = $this->getDoctrine()->getManager();
//        $em->persist($product);
//        $em->flush();

        $productConstructor = $this->get('product.constructor');
        $product = $productConstructor->constructProduct($request->get('product'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();


        return $product;


    }

    /**
     * @param $id
     * @Rest\Delete("/products/{id}")
     * @return View
     */
    public function deleteProduct(int $id)
    {
      $data = new Product();
      $em = $this->getDoctrine()->getManager();
      $product = $this->getDoctrine()->getRepository('AppBundle:Product')->find($id);
      if (empty($product)) {
          throw new HttpException(Response::HTTP_NOT_FOUND, "Product not founded");
      } else {
        $em->remove($product);
        $em->flush();
      }

    }
    /**
     * @param $id
     * @param Request $request
     * @Rest\Put("/products/{id}")
     *
     */
    public function updateProduct(int $id, Request $request)
    {
        $data = new Product();

        $prName = $request->get('product_name');
        $prCode = $request->get('product_code');
        $prDesck = $request->get('product_description');
        //        $dtmAdded = $request->get('dtm_added');
        //        $timestamp = $request->get('stm_timestamp');
        $stock = $request->get('stock_size');
        $price = $request->get('price');

        $em = $this->getDoctrine()->getManager();
        $product = $this->getDoctrine()->getRepository('AppBundle:Product')->find($id);

        if (empty($product)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, "Product not founded");
        } else {
            $product->setProductName($prName);
            $product->setProductDescription($prDesck);
            $product->setProductCode($prCode);
//        $product->setDtmDiscontinued($dtmDiscontinued);
            $product->setStockSize($stock);
            $product->setPrice($price);
            $em->flush();
            throw new HttpException(Response::HTTP_OK, "Product Updated");
        }


    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\Post("/products/get/")
     */
    public function getProductsByOptions(Request $request)
    {

        $filters = $request->get('filters');
        return $this->getDoctrine()->getRepository('AppBundle:Product')->createFilterQuery($filters);


    }



}