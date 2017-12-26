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

use Groovili\RestUploaderBundle\Entity\File;

/**
 * Class FileRepository
 * @package Groovili\RestUploaderBundle\Repository
 */
class FileRepository extends AbstractRepository
{
    /**
     * Query limit
     */
    private const LIMIT = 100;

    /**
     * @return int
     */
    public function getItemsPerPage(): int
    {
        return self::LIMIT;
    }

    /**
     * @param int $offset
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|null
     */
    public function all(int $offset = 0): ?ArrayCollection
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('f')
            ->from('RestUploaderBundle:File', 'f')
            ->setFirstResult($offset)
            ->setMaxResults(self::LIMIT);

        return $this->paginateResult($query, self::LIMIT);
    }

    /**
     * @param int $offset
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|null
     */
    public function public(int $offset = 0): ?ArrayCollection
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('f')
            ->from('RestUploaderBundle:File', 'f')
            ->where('f.scheme := :public')
            ->setParameter(File::SCHEME['Public'])
            ->setFirstResult($offset)
            ->setMaxResults(self::LIMIT);

        return $this->paginateResult($query, self::LIMIT);
    }

    /**
     * @param int $offset
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|null
     */
    public function private(int $offset = 0): ?ArrayCollection
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('f')
            ->from('RestUploaderBundle:File', 'f')
            ->where('f.scheme := :public')
            ->setParameter(File::SCHEME['Private'])
            ->setFirstResult($offset)
            ->setMaxResults(self::LIMIT);

        return $this->paginateResult($query, self::LIMIT);
    }
}