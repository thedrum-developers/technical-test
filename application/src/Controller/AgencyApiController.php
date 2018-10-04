<?php

namespace App\Controller;

use App\Entity\Agency;
use App\Service\AgencyService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AgencyApiController
 * @package App\Controller\Api
 */
class AgencyApiController extends AbstractApiController
{
    /**
     * The resource type of the related entity.
     */
    const RESOURCE_TYPE = 'agencies';

    /**
     * The related entity's class name.
     */
    const ENTITY_CLASS = Agency::class;

    /**
     * Attribute used as a slug by the related entity.
     */
    const SLUG_ATTRIBUTE = 'id';

    /**
     * ServiceApiController constructor.
     *
     * @param AgencyService   $service
     * @param RequestStack    $request
     * @param RouterInterface $router
     * @param LoggerInterface $logger
     */
    public function __construct(
        AgencyService $service,
        RequestStack $request,
        RouterInterface $router,
        LoggerInterface $logger
    ) {
        parent::__construct($service, $request, $router, $logger);
    }

    /**
     * @Route("/api/agencies",
     *     methods={"GET"},
     *     name="api_agencies_index",
     *     defaults={"route" = "api_agencies_index"})
     *
     * @param string $route
     *
     * @return JsonResponse
     */
    public function index(string $route): JsonResponse
    {
        return $this->service->getEntityIndex(
            self::SLUG_ATTRIBUTE,
            $route,
            self::ENTITY_CLASS,
            self::RESOURCE_TYPE
        );
    }

    /**
     * @Route("/api/agencies/{id}",
     *     methods={"GET"},
     *     name="api_agencies_item",
     *     requirements={"id" = "\d+"},
     *     defaults={"route" = "api_agencies_item"})
     *
     * @param string $route
     * @param string $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function readIndividualAgency(string $route, string $id): JsonResponse
    {
        return $this->service->getSingleEntity(
            $id,
            self::SLUG_ATTRIBUTE,
            $route,
            self::ENTITY_CLASS,
            self::RESOURCE_TYPE
        );
    }

    /**
     * @Route("/api/agencies/{id}/{resource}/{slug}",
     *     methods={"GET"},
     *     name="api_agencies_relationship_index",
     *     requirements={"id" = "\d+", "resource" = "^(?!relationships)[^\/]+"},
     *     defaults={"route" = "api_agencies_relationship_index", "slug" = ""})
     *
     * @param string    $id
     * @param string $resource
     * @param string $slug
     * @param string $route
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function relationshipIndex(string $id, string $resource, string $slug, string $route): Response
    {
        if ($slug) {
            return $this->redirectToRoute('api_services_item', ['slug' => $slug]);
        }

        return $this->service->getEntityRelationshipIndex(
            $id,
            self::SLUG_ATTRIBUTE,
            'slug',
            $resource,
            $route,
            self::ENTITY_CLASS,
            self::RESOURCE_TYPE
        );
    }

    /**
     * @Route("/api/agencies/{id}/relationships/{resource}/{slug}",
     *     methods={"GET"},
     *     name="api_agencies_relationships_object",
     *     requirements={"id" = "\d+"},
     *     defaults={"route" = "api_agencies_relationships_object", "resource" = "", "slug" = ""})
     *
     * @param string    $id
     * @param string $resource
     * @param string $slug
     * @param string $route
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function readIndividualServiceRelationships(
        string $id,
        string $resource,
        string $slug,
        string $route
    ): Response {
        if ($slug) {
            return $this->redirectToRoute('api_services_item', ['slug' => $slug]);
        }

        return $this->service->getIndividualEntityRelationships(
            $id,
            self::SLUG_ATTRIBUTE,
            $resource,
            $route,
            self::ENTITY_CLASS,
            self::RESOURCE_TYPE
        );
    }

    /**
     * @Route("/api/agencies",
     *     methods={"POST"},
     *     name="api_agencies_create",
     *     defaults={"route" = "api_agencies_create"})
     *
     * @param string $route
     *
     * @return JsonResponse
     */
    public function createAgencies(string $route): JsonResponse
    {
        return $this->service->createEntities(
            $this->request->request->get('data'),
            self::SLUG_ATTRIBUTE,
            $route,
            self::ENTITY_CLASS,
            self::RESOURCE_TYPE
        );
    }

    /**
     * @Route("/api/agencies/{id}",
     *     methods={"PUT"},
     *     name="api_agencies_update",
     *     requirements={"id" = "\d+"},
     *     defaults={"route" = "api_agencies_update", "id" = ""}))
     *
     * @param string $id
     * @param string $route
     *
     * @return JsonResponse
     */
    public function updateAgency(string $id, string $route): JsonResponse
    {
        return $this->service->updateEntities(
            $this->request->request->get('data'),
            $id,
            self::SLUG_ATTRIBUTE,
            $route,
            self::ENTITY_CLASS,
            self::RESOURCE_TYPE
        );
    }
}
