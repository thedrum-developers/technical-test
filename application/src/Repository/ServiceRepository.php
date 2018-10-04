<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends ServiceEntityRepository
{
    /**
     * ServiceRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Service::class);
    }

    /**
     * @param array $relatedIds
     *
     * @return array
     */
    public function findFormerlyRelatedEntities(array $relatedIds): array
    {
        $queryBuilder = $this->createQueryBuilder('service');
        $expression = $queryBuilder->expr()->notIn('service.id',':relatedIds');
        $results = $queryBuilder->select('service')
            ->where($expression)
            ->setParameter('relatedIds', $relatedIds)
            ->getQuery()
            ->getResult();

        return $results;
    }
}
