<?php
/**
 *
 *  * This file is part of the RestUploaderBundle package.
 *  * (c) groovili
 *  * For the full copyright and license information, please view the LICENSE
 *  * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace Groovili\RestUploaderBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AbstractRepository extends EntityRepository
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $query
     * @param int $limit
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|null
     */
    public function paginateResult(QueryBuilder $query, int $limit): ?ArrayCollection
    {
        $result = new Paginator($query, true);

        return new ArrayCollection([
            'items' => $result->getQuery()->getResult(),
            'count' => $result->count(),
            'per_page' => $limit
        ]);
    }
}