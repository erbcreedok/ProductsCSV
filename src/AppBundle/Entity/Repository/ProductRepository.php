<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    /**
     * @param array $filters
     * @return array
     */
    public function createFilterQuery(array $filters) : array
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

        $query = $this->_em->getRepository('AppBundle:Product')->createQueryBuilder('p')
            ->where('p.productCode LIKE :code')
            ->andWhere('p.productName LIKE :prName')
            ->andWhere('p.productDescription LIKE :description')
            ->andWhere('p.price >= :priceFrom OR :priceFrom IS NULL')
            ->andWhere('p.price <= :priceTo OR :priceTo IS NULL')
            ->andWhere('p.stockSize >= :stockFrom or :stockFrom IS NULL')
            ->andWhere('p.stockSize <= :stockTo or :stockTo IS NULL')
            ->setParameters([
                'code' => '%'.$prCode.'%',
                'prName' => '%'.$prName.'%',
                'description' => '%'.$prDescription.'%',
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

        return  $query->getQuery()->getResult();
    }

}