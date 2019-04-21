<?php

namespace Core\Repository;

use Core\Entity\Admin;
use Core\Helper\ListParams;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdminRepository extends EntityRepository
{
    use ListTrait;

    static private $sorting = [];

    public function buildAdminListQuery(ListParams $params = null): QueryBuilder
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('a')
            ->from(Admin::class, 'a')
            ->where('a.isDeleted = false')
            ->andWhere('a.isActive = true')
        ;
        return $this->applyListParamsOnQueryBuilder($qb, $params);
    }
}
