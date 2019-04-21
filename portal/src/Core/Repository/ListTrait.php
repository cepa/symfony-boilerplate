<?php

namespace Core\Repository;

use Core\Helper\ListParams;
use Doctrine\ORM\QueryBuilder;

trait ListTrait
{
    protected function applyListParamsOnQueryBuilder(QueryBuilder $qb, ListParams $params = null): QueryBuilder
    {
        if (isset($params)) {
            foreach ($params->getSortings() as $field => $order) {
                if (isset(self::$sortings[$field])) {
                    $qb->addOrderBy(self::$sortings[$field], $order);
                } else {
                    throw new \InvalidArgumentException("Unknown sorting key: " . $field);
                }
            }
            $qb->setFirstResult($params->getPage() * $params->getSize());
            $qb->setMaxResults($params->getSize());
        }
        return $qb;
    }
}
