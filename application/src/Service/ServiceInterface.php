<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Interface ServiceInterface
 * @package App\Service
 */
interface ServiceInterface
{
    /**
     * Get an index of all entities.
     *
     * @param string $slugAttribute
     * @param string $route
     * @param string $entityClass
     * @param string $resourceType
     *
     * @return JsonResponse
     */
    public function getEntityIndex(
        string $slugAttribute,
        string $route,
        string $entityClass,
        string $resourceType
    ): JsonResponse;

    /**
     * Get a single entity to pass as a JSON response to the controller.
     *
     * @param string $slug
     * @param string $slugAttribute
     * @param string $route
     * @param string $entityClass
     * @param string $resourceType
     *
     * @return JsonResponse
     */
    public function getSingleEntity(
        string $slug,
        string $slugAttribute,
        string $route,
        string $entityClass,
        string $resourceType
    ): JsonResponse;

    /**
     * Get the index of a single entity's relationships to pass as a JSON response to the controller.
     *
     * @param string      $slug
     * @param string      $slugAttribute
     * @param null|string $relationSlug
     * @param string      $resource
     * @param string      $route
     * @param string      $entityClass
     * @param string      $resourceType
     *
     * @return JsonResponse
     */
    public function getEntityRelationshipIndex(
        string $slug,
        string $slugAttribute,
        ?string $relationSlug,
        string $resource,
        string $route,
        string $entityClass,
        string $resourceType
    ): JsonResponse;

    /**
     * Get an individual entity's relationship objects to pass as a JSON response to the controller.
     *
     * @param string $slug
     * @param string $slugAttribute
     * @param string $resource
     * @param string $route
     * @param string $entityClass
     * @param string $resourceType
     *
     * @return JsonResponse
     */
    public function getIndividualEntityRelationships(
        string $slug,
        string $slugAttribute,
        string $resource,
        string $route,
        string $entityClass,
        string $resourceType
    ): JsonResponse;

    /**
     * Create entities and pass the resultant output as a JSON response to the controller.
     *
     * @param array  $requestData
     * @param string $slugAttribute
     * @param string $route
     * @param string $entityClass
     * @param string $resourceType
     *
     * @return JsonResponse
     */
    public function createEntities(
        array $requestData,
        string $slugAttribute,
        string $route,
        string $entityClass,
        string $resourceType
    ): JsonResponse;

    /**
     * Update entities and pass the resultant output as a JSON response to the controller.
     *
     * @param array  $requestData
     * @param string $slug
     * @param string $slugAttribute
     * @param string $route
     * @param string $entityClass
     * @param string $resourceType
     *
     * @return JsonResponse
     */
    public function updateEntities(
        array $requestData,
        string $slug,
        string $slugAttribute,
        string $route,
        string $entityClass,
        string $resourceType
    ): JsonResponse;
}