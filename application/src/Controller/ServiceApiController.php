<?php

namespace App\Controller;

use App\Entity\Service;
use App\Service\ServiceService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ServiceApiController
 * @package App\Controller\Api
 */
class ServiceApiController extends AbstractApiController
{
    /**
     * The resource type of the related entity.
     */
    const RESOURCE_TYPE = 'services';

    /**
     * The related entity's class name.
     */
    const ENTITY_CLASS = Service::class;

    /**
     * Attribute used as a slug by the related entity.
     */
    const SLUG_ATTRIBUTE = 'slug';

    /**
     * ServiceApiController constructor.
     *
     * @param ServiceService  $service
     * @param RequestStack    $request
     * @param RouterInterface $router
     * @param LoggerInterface $logger
     */
    public function __construct(
        ServiceService $service,
        RequestStack $request,
        RouterInterface $router,
        LoggerInterface $logger
    ) {
        parent::__construct($service, $request, $router, $logger);
    }

    /**
     * @Route("/api/services",
     *     methods={"GET"},
     *     name="api_services_index",
     *     defaults={"route" = "api_services_index"})
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
     * @Route("/api/services/{slug}",
     *     methods={"GET"},
     *     name="api_services_item",
     *     defaults={"route" = "api_services_item"})
     *
     * @param string $route
     * @param string $slug
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function readIndividualService(string $route, string $slug): JsonResponse
    {
        return $this->service->getSingleEntity(
            $slug,
            self::SLUG_ATTRIBUTE,
            $route,
            self::ENTITY_CLASS,
            self::RESOURCE_TYPE
        );
    }

    /**
     * @Route("/api/services/{slug}/{resource}/{id}",
     *     methods={"GET"},
     *     name="api_services_relationship_index",
     *     requirements={"resource" = "^(?!relationships)[^\/]+", "id" = "\d+"},
     *     defaults={"route" = "api_services_relationship_index", "id" = 0})
     *
     * @param string $slug
     * @param string $resource
     * @param string $id
     * @param string $route
     *
     * @return Response
     */
    public function relationshipIndex(string $slug, string $resource, string $id, string $route): Response
    {
        if ($id) {
            return $this->redirectToRoute('api_agencies_item', ['id' => $id]);
        }

        return $this->service->getEntityRelationshipIndex(
            $slug,
            self::SLUG_ATTRIBUTE,
            null,
            $resource,
            $route,
            self::ENTITY_CLASS,
            self::RESOURCE_TYPE
        );
    }

    /**
     * @Route("/api/services/{slug}/relationships/{resource}/{id}",
     *     methods={"GET"},
     *     name="api_services_relationships_object",
     *     requirements={"id" = "\d+"},
     *     defaults={"route" = "api_services_relationships_object", "resource" = "", "id" = 0})
     *
     * @param string $slug
     * @param string $resource
     * @param string $id
     * @param string $route
     *
     * @return Response
     */
    public function readIndividualServiceRelationships(
        string $slug,
        string $resource,
        string $id,
        string $route
    ): Response {
        if ($id) {
            return $this->redirectToRoute('api_agencies_item', ['id' => $id]);
        }

        return $this->service->getIndividualEntityRelationships(
            $slug,
            self::SLUG_ATTRIBUTE,
            $resource,
            $route,
            self::ENTITY_CLASS,
            self::RESOURCE_TYPE
        );
    }

    /**
     * @Route("/api/services",
     *     methods={"POST"},
     *     name="api_services_create",
     *     defaults={"route" = "api_services_create"})
     *
     * @param string $route
     *
     * @return JsonResponse
     */
    public function createServices(string $route): JsonResponse
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
     * @Route("/api/services/{slug}",
     *     methods={"PUT"},
     *     name="api_services_update",
     *     defaults={"route" = "api_services_update", "slug" = ""}))
     *
     * @param string $slug
     * @param string $route
     *
     * @return JsonResponse
     */
    public function updateService(string $slug, string $route): JsonResponse
    {
        return $this->service->updateEntities(
            $this->request->request->get('data'),
            $slug,
            self::SLUG_ATTRIBUTE,
            $route,
            self::ENTITY_CLASS,
            self::RESOURCE_TYPE
        );
    }
}
