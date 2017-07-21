<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ProductRepository extends EntityRepository
{
    /**
     * @param $filters
     * @param $order
     * @param $limit
     * @return array
     */
    public function createFilterQuery($filters, $order, $limit) : array
    {
        $query = $this->_em->getRepository('AppBundle:Product')->createQueryBuilder('p');

        if ($filters) {
            $query = $this->filter($filters, $query);
        }
        if ($order) {
            $query = $this->order($order, $query);
        }

        if (!$limit) {
            $limit = [
                'offset' => 0,
                'limit' => 20
            ];
        }
        $query = $this->limit($limit, $query);


        return  $query->getQuery()->getResult();
    }

    public function filter(array $filters, QueryBuilder $query) : QueryBuilder
    {
        $prName = $filters['productName'];
        $prCode = $filters['productCode'];
        $prDescription = $filters['productDescription'];
        $prCostFrom = $filters['cost']['from'];
        $prCostTo = $filters['cost']['to'];
        $stockFrom = $filters['stock']['from'];
        $stockTo = $filters['stock']['to'];
        $dscFrom = $filters['discontinued']['from']['formatted'];
        $dscTo = $filters['discontinued']['to']['formatted'];
        $query
            ->where('p.productCode LIKE :code')
            ->andWhere('p.productName LIKE :prName')
            ->andWhere('p.productDescription LIKE :description')
            ->andWhere('p.price >= :priceFrom OR :priceFrom IS NULL')
            ->andWhere('p.price <= :priceTo OR :priceTo IS NULL')
            ->andWhere('p.stockSize >= :stockFrom or :stockFrom IS NULL')
            ->andWhere('p.stockSize <= :stockTo or :stockTo IS NULL')
            ->setParameters([
                'code' => '%' . $prCode . '%',
                'prName' => '%' . $prName . '%',
                'description' => '%' . $prDescription . '%',
                'priceFrom' => $prCostFrom,
                'priceTo' => $prCostTo,
                'stockFrom' => $stockFrom,
                'stockTo' => $stockTo,
            ]);
        if ($filters['discontinued']['isOn']) {
            if (!$filters['discontinued']['isDiscontinued']) {
                $query->andWhere('p.dtmDiscontinued IS NULL');
            } else {
                $query->andWhere('p.dtmDiscontinued IS NOT NULL')
                    ->andWhere('p.dtmDiscontinued  >= :dscFrom or :dscFrom IS NULL')
                    ->andWhere('p.dtmDiscontinued  <= :dscTo or :dscTo IS NULL')
                    ->setParameter('dscFrom', $dscFrom)
                    ->setParameter('dscTo', $dscTo);
            }
        }
        return $query;
    }

    public function order(array $order, QueryBuilder $query) : QueryBuilder
    {
        $query->orderBy('p.'.$order['sort'], $order['order'] ? 'ASC' : 'DESC')
        ;

        return $query;
    }

    public function limit(array $limit, QueryBuilder $query) : QueryBuilder
    {
        $query
            ->setFirstResult($limit['offset'] ? $limit['offset'] : 0)
            ->setMaxResults($limit['limit'])
        ;
        return $query;
    }

}