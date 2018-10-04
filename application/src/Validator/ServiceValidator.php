<?php

namespace App\Validator;

use App\Entity\EntityInterface;
use App\Exception\InvalidDataException;
use App\Exception\RequestedDataNotFoundException;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ServiceValidator
 * @package App\Validator
 */
class ServiceValidator
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * ServiceValidator constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $requestData
     * @param       $slug
     */
    public function ensureUpdatingWithSlugOnlyUpdatesOneEntity(array $requestData, $slug)
    {
        if (isset($requestData[0]) && is_array($requestData[0]) && count($requestData) > 1 && $slug) {
            $message = 'When using the \'PUT\' method to update more than one item do not use a slug.';
            throw new BadRequestHttpException($message, null, 6);
        }
    }

    /**
     * @param        $requestData
     * @param string $type
     * @param string $requestMethod
     */
    public function validateRequestData($requestData, string $type, string $requestMethod): void
    {
        if (!is_array($requestData)) {
            $message = 'Request data is not formed correctly. Please ensure the contents of the "data" key is an array';
            throw new BadRequestHttpException($message, null, 1);
        }
        if (isset($requestData['id']) && $requestMethod === 'POST') {
            $message = 'Please use the \'PUT\' request method when updating an existing entity.';
            throw new BadRequestHttpException($message, null, 2);
        }
        if (!isset($requestData['type'])) {
            $message = '"type" member must be set in request body.';
            throw new BadRequestHttpException($message, null, 3);
        }
        if ($requestData['type'] !== $type) {
            $message = '%s "type" member should be "%s" for this resource endpoint.';
            throw new BadRequestHttpException(sprintf($message, $requestData['type'], $type), null, 4);
        }
        if (!isset($requestData['attributes']) && $requestMethod === 'POST') {
            $message = '"attributes" object must be set to create a new entity.';
            throw new BadRequestHttpException($message, null, 5);
        }
    }

    /**
     * @param EntityInterface $entity
     * @param string          $id
     * @param string          $type
     *
     * @return EntityInterface|null
     */
    public function ensureEntityFound(?EntityInterface $entity, string $id, string $type): ?EntityInterface
    {
        if (!$entity) {
            $message = sprintf('Could not find type "%s" with id: %s', $type, $id);
            throw new RequestedDataNotFoundException($message, null, [], 1);
        }

        return $entity;
    }

    /**
     * @param EntityInterface $entity
     * @param array           $criteria
     * @param string          $type
     *
     * @return EntityInterface|null
     */
    public function ensureEntityFoundByCriteria(
        ?EntityInterface $entity,
        array $criteria,
        string $type
    ): ?EntityInterface {
        if (!$entity) {
            $readableCriteria = '{ ';
            foreach ($criteria as $key => $value) {
                $readableCriteria .= sprintf('%s: %s, ', $key, $value);
            }
            $readableCriteria .= '}';
            $message = sprintf('No entity of type %s found using criteria %s.', $type, $readableCriteria);
            throw new RequestedDataNotFoundException($message, null, [], 3);
        }

        return $entity;
    }

    /**
     * @param EntityInterface $entity
     * @param string          $resource
     * @param string          $resourceType
     */
    public function ensureRelationshipExists(EntityInterface $entity, string $resource, string $resourceType): void
    {
        if (!property_exists($entity, $resource)) {
            $message = 'This entity of type "%s" does not have a relationship with resource "%s".';
            throw new RequestedDataNotFoundException(sprintf($message, $resourceType, $resource), null, 4);
        }
    }

    /**
     * @param PersistentCollection $collection
     * @param string               $resource
     * @param string               $resourceType
     */
    public function ensureRelationshipsPopulated(
        PersistentCollection $collection,
        string $resource,
        string $resourceType
    ): void {
        if (!$collection) {
            $message = 'This entity of type "%s" has a relationship with resource "%s", but it is empty.';
            throw new RequestedDataNotFoundException(sprintf($message, $resourceType, $resource), null, 5);
        }
    }

    /**
     * @param EntityInterface $entity
     */
    public function validate(EntityInterface $entity): void
    {
        $errors = $this->validator->validate($entity);
        if ($errors->count() > 0) {
            throw new InvalidDataException((string)$errors, null, [], 1);
        }
    }
}