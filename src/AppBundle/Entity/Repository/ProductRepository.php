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
        $prName = $filters['name'];
        $prCode = $filters['code'];
        $prDescription = $filters['description'];
        $prCostFrom = $filters['cost']['from'];
        $prCostTo = $filters['cost']['to'];
        $stockFrom = $filters['stock']['from'];
        $stockTo = $filters['stock']['to'];
        $dscFrom = $filters['discontinued']['from']['formatted'];
        $dscTo = $filters['discontinued']['to']['formatted'];
//        $discontinuedFilter = '';
//        if ( $filters['discontinued']['isOn'] ) {
//            if (!$filters['discontinued']['isDiscontinued']) {
//                return ['fuck you'];
//                $discontinuedFilter .= ' AND p.dtmDiscontinued IS NULL ';
//            } else {
//                return [$dscFrom, $dscTo];
//                $discontinuedFilter .= ' AND (p.dtmDiscontinued  >= :dscFrom or :dscFrom IS NULL) ';
//                $discontinuedFilter .= ' AND (p.dtmDiscontinued  <= :dscTo or :dscTo IS NULL) ';
//            }
//        }
//        return ['you are fucker'];
//        return $this->_em->createQuery(
//            '
//            SELECT p
//            FROM AppBundle:Product p
//            WHERE p.productCode LIKE :code
//            AND p.productName LIKE :prname
//            AND p.productDescription LIKE :description
//            AND (p.price >= :priceFrom OR :priceFrom IS NULL)
//            AND (p.price <= :priceTo OR :priceTo IS NULL)
//            AND (p.stockSize >= :stockFrom or :stockFrom IS NULL)
//            AND (p.stockSize <= :stockTo or :stockTo IS NULL)
//            CASE :dscOn THEN
//            '
//        )
//             ->setParameters([
//                 'code' => '%'.$prCode.'%',
//                 'prname' => '%'.$prName.'%',
//                 'description' => '%'.$prDescription.'%',
//                 'priceFrom' => $prCostFrom,
//                 'priceTo' => $prCostTo,
//                 'stockFrom' => $stockFrom,
//                 'stockTo' => $stockTo,
//             ])
//             ->getResult();
            $quary = $this->_em->createQueryBuilder('p')
                ->from('AppBundle:Product', p)
                ->andWhere('p.productCode LIKE :code 
                AND productName LIKE :prname')
                ->setParameters([
                    'code' => '%'.$prCode.'%',
                    'prname' => '%'.$prName.'%',
                ]);
            return $quary->getFirstResult();
    }

}