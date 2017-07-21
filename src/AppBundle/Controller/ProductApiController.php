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

    public function getProducts(Request $request) {
//          $products = $this->getDoctrine()->getRepository('AppBundle:Product')->findBy([],['productCode'=>'ASC'],20);
//          return $products;

        $filters = json_decode($request->get('filters'), true);
        $order = json_decode($request->get('order'), true);
        $limit = json_decode($request->get('limit'), true);
        return $this->getDoctrine()->getRepository('AppBundle:Product')->createFilterQuery($filters, $order, $limit);
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
        $productConstructor = $this->get('product.constructor');

        $newProduct = $productConstructor->constructProduct($request->get('product'));
        $em = $this->getDoctrine()->getManager();
        $product = $this->getDoctrine()->getRepository('AppBundle:Product')->find($id);

        if (empty($product)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, "Product not founded");
        } else {
            $product
                ->setProductName($newProduct->getProductName())
                ->setProductCode($newProduct->getProductCode())
                ->setProductDescription($newProduct->getProductDescription())
                ->setStockSize($newProduct->getStockSize())
                ->setPrice($newProduct->getPrice())
                ->setDtmDiscontinued($newProduct->isDiscontinued());
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
        $order = $request->get('order');
        $limit = $request->get('limit');
        return $this->getDoctrine()->getRepository('AppBundle:Product')->createFilterQuery($filters, $order, $limit);


    }



}