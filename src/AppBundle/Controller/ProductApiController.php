<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Repository\ProductRepository;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use JMS\Serializer\Tests\Fixtures\Price;
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
     * @param Request $request
     * @return array
     * @Rest\Get("/")
     */
    public function getProducts(Request $request)
    {
//          $products = $this->getDoctrine()->getRepository('AppBundle:Product')->findBy([],['productCode'=>'ASC'],20);
//          return $products;

        $filters = json_decode($request->get('filters'), true);
        $order = json_decode($request->get('order'), true);
        $limit = json_decode($request->get('limit'), true);
        return [
            "products" => $this->getDoctrine()->getRepository('AppBundle:Product')->createFilterQuery($filters, $order, $limit),
            "count" => $this->getDoctrine()->getRepository('AppBundle:Product')->count($filters)
        ];
    }

    /**
     * @param int $id
     * @return object
     * @Rest\Get("/{id}")
     */
    public function getProduct(int $id)
    {
        $singleProduct = $this->getDoctrine()->getRepository('AppBundle:Product')->find($id);
        return $singleProduct;
    }

    /**
     * @param Request $request
     * @Rest\Post("/")
     * @return string
     *
     */
    public function addProduct(Request $request)
    {
        $productConstructor = $this->get('product.constructor');
        $product = $productConstructor->constructProduct($request->get('product'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $product;
    }

    /**
     * @param $id
     * @Rest\Delete("/{id}")
     * @return View
     */
    public function deleteProduct(int $id)
    {
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
     * @Rest\Put("/{id}")
     * @return Product
     */
    public function updateProduct(int $id, Request $request) : Product
    {
        $productConstructor = $this->get('product.constructor');

        $newProduct = $productConstructor->constructProduct($request->get('product'));
        $em = $this->getDoctrine()->getManager();
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if (empty($product)) {
            return null;
        } else {
            $product
                ->setProductName($newProduct->getProductName())
                ->setProductCode($newProduct->getProductCode())
                ->setProductDescription($newProduct->getProductDescription())
                ->setStockSize($newProduct->getStockSize())
                ->setPrice($newProduct->getPrice())
                ->setDtmDiscontinued($newProduct->isDiscontinued());
            $em->flush();
            return $product;
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\Post("/get/")
     */
    public function getProductsByOptions(Request $request)
    {
        $filters = $request->get('filters');
        $order = $request->get('order');
        $limit = $request->get('limit');
        return $this->getDoctrine()->getRepository('AppBundle:Product')->createFilterQuery($filters, $order, $limit);
    }

    /**
     * @param Request $request
     * @return int
     * @Rest\Get("/count/")
     */
    public function getCount(Request $request) : int
    {
        $filters = json_decode($request->get('filters'), true);
        return $this->getDoctrine()->getRepository('AppBundle:Product')->count($filters);
    }

    /**
     * @param Request $request
     * @return int
     * @Rest\Get("/isProductCodeFree/")
     */
    public function isProductCodeFree(Request $request): int
    {
        $productCode = $request->get('productCode');

        $product = $this->getDoctrine()->getRepository('AppBundle:Product')->findOneBy(['productCode'=>$productCode]);

        if ($product) {
            return $product->getId();
        } else {
            return -1;
        }
    }

}
