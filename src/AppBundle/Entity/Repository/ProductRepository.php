<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;


class ProductRepository extends EntityRepository
{
    public function createFindAllQuery()
    {
         return $this->_em->createQuery(
            "
            SELECT *
            FROM AppBundle:Product
            "
        );
    }

}