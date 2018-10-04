<?php

namespace App\Service;

use App\Entity\EntityInterface;
use App\Validator\ServiceValidator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class AbstractService
 * @package App\Service
 */
abstract class AbstractService implements ServiceInterface
{
    /** @var RouterInterface */
    protected $router;

    /** @var SerializerInterface|DenormalizerInterface */
    protected $serializer;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var ServiceValidator */
    protected $validator;

    /**
     * AbstractService constructor.
     *
     * @param RouterInterface        $router
     * @param SerializerInterface    $serializer
     * @param EntityManagerInterface $entityManager
     * @param ServiceValidator       $validator
     */
    public function __construct(
        RouterInterface $router,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ServiceValidator $validator
    ) {
        $this->router = $router;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @param array           $relationships
     * @param EntityInterface $service
     *
     * @return EntityInterface
     */
    abstract protected function populateRelationships(array $relationships, EntityInterface $service): EntityInterface;

    /**
     * {@inheritdoc}
     */
    public function getEntityIndex(
        string $slugAttribute,
        string $route,
        string $entityClass,
        string $resourceType
    ): JsonResponse {
        $response = $this->findAll($entityClass);
        $context = [
            'type' => $resourceType,
            'base_url' => $this->generateBaseUrl($route),
            'slug_attribute' => $slugAttribute,
            'index_format' => true,
        ];

        return $this->json($response, 200, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getSingleEntity(
        string $slug,
        string $slugAttribute,
        string $route,
        string $entityClass,
        string $resourceType
    ): JsonResponse {
        $response = $this->findOneBy(
            $entityClass,
            [$slugAttribute => $slug],
            $resourceType
        );
        $context = [
            'type' => $resourceType,
            'base_url' => $this->generateBaseUrl($route, [$slugAttribute => $slug]),
            'slug_attached' => true,
        ];

        return $this->json($response, 200, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityRelationshipIndex(
        string $slug,
        string $slugAttribute,
        ?string $relationSlug,
        string $resource,
        string $route,
        string $entityClass,
        string $resourceType
    ): JsonResponse {
        $result = $this->findOneBy(
            $entityClass,
            [$slugAttribute => $slug],
            $resourceType
        );
        $relationship = $this->getRelationship($result, $resource, $resourceType);
        $context = [
            'type' => $resource,
            'base_url' => $this->generateBaseUrl($route, [$slugAttribute => $slug, 'resource' => $resource]),
            'index_format' => true,
            'related_url_strip' => $resourceType.'/'.$slug,
            'relation_slug' => $relationSlug,
        ];

        return $this->json($relationship->getValues(), 200, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getIndividualEntityRelationships(
        string $slug,
        string $slugAttribute,
        string $resource,
        string $route,
        string $entityClass,
        string $resourceType
    ): JsonResponse {
        $entity = $this->findOneBy(
            $entityClass,
            [$slugAttribute => $slug],
            $resourceType
        );
        $context = [
            'type' => $resource,
            'base_url' => $this->generateBaseUrl($route, [$slugAttribute => $slug, 'resource' => $resource]),
            'relationships' => true,
        ];
        // If no relationship resource is specified return all relationship objects.
        if (!$resource) {
            $response = $this->getAllRelationships($entity);
        } else {
            $response = $this->getRelationship($entity, $resource, $resourceType);
        }

        return $this->json($response, 200, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function createEntities(
        array $requestData,
        string $slugAttribute,
        string $route,
        string $entityClass,
        string $resourceType
    ): JsonResponse {
        $response = $this->buildEntitiesFromRequestData($requestData, $entityClass, $resourceType, 'POST');
        $baseUrl = $this->generateBaseUrl($route);
        $context = [
            'type' => $resourceType,
            'base_url' => $baseUrl,
            'newly_persisted' => true,
            'slug_attribute' => $slugAttribute,
            'index_format' => true,
        ];
        $headers['Location'] = $baseUrl;

        $this->entityManager->flush();

        return $this->json($response, 201, $context, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function updateEntities(
        array $requestData,
        string $slug,
        string $slugAttribute,
        string $route,
        string $entityClass,
        string $resourceType
    ): JsonResponse {
        $this->validator->ensureUpdatingWithSlugOnlyUpdatesOneEntity($requestData, $slug);
        $entities = $this->buildEntitiesFromRequestData($requestData, $entityClass, $resourceType, 'PUT');
        $baseUrl = $this->generateBaseUrl($route, [$slugAttribute => $slug]);
        // Strip the old slug from the base URL.
        if ($slug) {
            $baseUrl = str_replace('/'.$slug, '', $baseUrl);
        }
        $context = [
            'type' => $resourceType,
            'base_url' => $baseUrl,
            'newly_persisted' => true,
            'slug_attribute' => $slugAttribute,
        ];
        $headers['Location'] = $baseUrl;

        $this->entityManager->flush();

        return $this->json($entities, 201, $context, $headers);
    }

    /**
     * @param array  $requestData
     * @param string $entityClassName
     * @param string $resourceType
     * @param string $requestMethod
     *
     * @return array
     */
    protected function buildEntitiesFromRequestData(
        array $requestData,
        string $entityClassName,
        string $resourceType,
        string $requestMethod
    ): array {
        $entities = [];
        foreach ($requestData as $requestDatum) {
            $this->validator->validateRequestData($requestDatum, $resourceType, $requestMethod);
            $entity = $this->initialiseEntity($requestDatum, $entityClassName);
            $this->populateEntity($requestDatum, $entity);
            $this->validator->validate($entity);
            $entities[] = $entity;
        }

        return $entities;
    }

    /**
     * @param array  $data
     * @param string $entityClassName
     *
     * @return EntityInterface
     */
    protected function initialiseEntity(array $data, string $entityClassName): EntityInterface
    {
        if (isset($data['id'])) {
            return $this->find($entityClassName, $data['id'], $data['type']);
        } else {
            $entity = new $entityClassName();
            $this->entityManager->persist($entity);

            return $entity;
        }
    }

    /**
     * @param array           $requestData
     * @param EntityInterface $entity
     */
    protected function populateEntity(array $requestData, EntityInterface $entity): void
    {
        if(isset($requestData['attributes'])  && $requestData['attributes']) {
            $this->populateEntityAttributes($requestData['attributes'], $entity);
        }
        if (isset($requestData['relationships']) && $requestData['relationships']) {
            $this->populateRelationships($requestData['relationships'], $entity);
        }
    }

    /**
     * @param array           $data
     * @param EntityInterface $entity
     */
    protected function populateEntityAttributes(array $data, EntityInterface $entity): void
    {
        $this->serializer->denormalize(
            $data,
            get_class($entity),
            'json',
            ['object_to_populate' => $entity]
        );
    }

    /**
     * @param string $entityClassName
     *
     * @return array
     */
    protected function findAll(string $entityClassName): array
    {
        return $this->entityManager->getRepository($entityClassName)->findAll();
    }

    /**
     * @param string $entityClassName
     * @param int    $id
     * @param string $type
     *
     * @return EntityInterface
     */
    protected function find(string $entityClassName, int $id, string $type): EntityInterface
    {
        /** @var EntityInterface $entity */
        $entity = $this->entityManager->getRepository($entityClassName)->find($id);

        return $this->validator->ensureEntityFound($entity, $id, $type);
    }

    /**
     * @param string $entityClassName
     * @param array  $criteria
     * @param string $type
     *
     * @return EntityInterface
     */
    protected function findOneBy(string $entityClassName, array $criteria, string $type): EntityInterface
    {
        /** @var EntityInterface $entity */
        $entity = $this->entityManager->getRepository($entityClassName)->findOneBy($criteria);

        return $this->validator->ensureEntityFoundByCriteria($entity, $criteria, $type);
    }

    /**
     * @param EntityInterface $entity
     *
     * @return array|null
     */
    protected function getAllRelationships(EntityInterface $entity): ?array
    {
        $relationships = [];
        $associatedData = $this->entityManager->getClassMetadata(get_class($entity))->getAssociationMappings();
        foreach ($associatedData as $associatedDatum) {
            $relationships[] = call_user_func([$entity, 'get'.ucfirst($associatedDatum['fieldName'])]);
        }

        return $relationships;
    }

    /**
     * @param EntityInterface $entity
     * @param string          $resource
     * @param string          $resourceType
     *
     * @return PersistentCollection
     */
    protected function getRelationship(
        EntityInterface $entity,
        string $resource,
        string $resourceType
    ): PersistentCollection {
        $this->validator->ensureRelationshipExists($entity, $resource, $resourceType);
        $collection = call_user_func([$entity, 'get'.ucfirst($resource)]);
        $this->validator->ensureRelationshipsPopulated($collection, $resource, $resourceType);

        return $collection;
    }

    /**
     * @param string     $route
     * @param array|null $parameters
     *
     * @return string
     */
    protected function generateBaseUrl(string $route, ?array $parameters = []): string
    {
        return $this->router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param EntityInterface|array $entities
     * @param int                   $responseCode
     * @param array                 $context
     * @param array                 $headers
     *
     * @return JsonResponse
     */
    protected function json($entities, int $responseCode, array $context, $headers = []): JsonResponse
    {
        $encodeOptions = ['json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS];
        $context = array_merge($encodeOptions, $context);
        $json = $this->serializer->serialize($entities, 'json', $context);

        return new JsonResponse($json, $responseCode, $headers, true);
    }
}