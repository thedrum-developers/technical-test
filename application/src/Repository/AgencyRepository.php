<?php

namespace App\Repository;

use App\Entity\Agency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Agency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agency[]    findAll()
 * @method Agency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgencyRepository extends ServiceEntityRepository
{
    /**
     * AgencyRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Agency::class);
    }

    /**
     * @param array $relatedIds
     *
     * @return array
     */
    public function findFormerlyRelatedEntities(array $relatedIds): array
    {
        $queryBuilder = $this->createQueryBuilder('agency');
        $expression = $queryBuilder->expr()->notIn('agency.id',':relatedIds');
        $results = $queryBuilder->select('agency')
            ->where($expression)
            ->setParameter('relatedIds', $relatedIds)
            ->getQuery()
            ->getResult();

        return $results;
    }
}
