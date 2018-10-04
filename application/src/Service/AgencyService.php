<?php

namespace App\Service;

use App\Entity\Agency;
use App\Entity\EntityInterface;
use App\Entity\Service;

/**
 * Class AgencyService
 * @package App\Service
 */
class AgencyService extends AbstractService
{
    /**
     * {@inheritdoc}
     */
    protected function populateRelationships(array $relationships, EntityInterface $agency): EntityInterface
    {
        /** @var Agency $agency */
        foreach ($relationships as $type => $relationship) {
            if ($type === 'services') {
                $agency = $this->populateServices($relationship['data'], $agency);
            }
        }

        return $agency;
    }

    /**
     * @param array           $data
     * @param EntityInterface $agency
     *
     * @return EntityInterface
     */
    protected function populateServices(array $data, EntityInterface $agency): EntityInterface
    {
        /**
         * @var Agency  $agency
         * @var Service $service
         */
        $relatedIds = array_column($data, 'id');
        foreach ($relatedIds as $relatedId) {
            $service = $this->entityManager->getRepository(Service::class)->find($relatedId);
            $agency->addService($service);
        }

        return $this->removeFormerlyRelatedServices($relatedIds, $agency);
    }

    /**
     * @param array           $relatedIds
     * @param EntityInterface $agency
     *
     * @return EntityInterface
     */
    protected function removeFormerlyRelatedServices(array $relatedIds, EntityInterface $agency): EntityInterface
    {
        /** @var Agency $agency */
        $formerRelations = $this->entityManager
            ->getRepository(Service::class)
            ->findFormerlyRelatedEntities($relatedIds);

        foreach ($formerRelations as $formerRelation) {
            $agency->removeService($formerRelation);
        }

        return $agency;
    }
}