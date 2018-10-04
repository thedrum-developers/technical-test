<?php

namespace App\Service;

use App\Entity\Agency;
use App\Entity\EntityInterface;
use App\Entity\Service;

/**
 * Class ServiceService
 * @package App\Service
 */
class ServiceService extends AbstractService
{
    /**
     * {@inheritdoc}
     */
    protected function populateRelationships(array $relationships, EntityInterface $service): EntityInterface
    {
        /** @var Service $service */
        foreach ($relationships as $type => $relationship) {
            if ($type === 'agencies') {
                $service = $this->populateAgencies($relationship['data'], $service);
            }
        }

        return $service;
    }

    /**
     * @param array           $data
     * @param EntityInterface $service
     *
     * @return EntityInterface
     */
    protected function populateAgencies(array $data, EntityInterface $service): EntityInterface
    {
        /**
         * @var Service $service
         * @var Agency  $agency
         */
        $relatedIds = array_column($data, 'id');
        foreach ($relatedIds as $relatedId) {
            $agency = $this->entityManager->getRepository(Agency::class)->find($relatedId);
            $service->addAgency($agency);
        }

        return $this->removeFormerlyRelatedAgencies($relatedIds, $service);
    }

    /**
     * @param array           $relatedIds
     * @param EntityInterface $service
     *
     * @return EntityInterface
     */
    protected function removeFormerlyRelatedAgencies(array $relatedIds, EntityInterface $service): EntityInterface
    {
        /** @var Service $service */
        $formerRelations = $this->entityManager
            ->getRepository(Agency::class)
            ->findFormerlyRelatedEntities($relatedIds);

        foreach ($formerRelations as $formerRelation) {
            $service->removeAgency($formerRelation);
        }

        return $service;
    }
}