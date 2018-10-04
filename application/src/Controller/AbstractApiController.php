<?php

namespace App\Controller;

use App\Entity\EntityInterface;
use App\Service\ServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AbstractApiController
 * @package App\Controller\Api
 */
abstract class AbstractApiController extends AbstractController
{
    /** @var EntityInterface */
    protected $entity;

    /** @var ServiceInterface */
    protected $service;

    /** @var null|Request */
    protected $request;

    /** @var RouterInterface */
    protected $router;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * AbstractApiController constructor.
     *
     * @param ServiceInterface $service
     * @param RequestStack     $requestStack
     * @param RouterInterface  $router
     * @param LoggerInterface  $logger
     */
    public function __construct(
        ServiceInterface $service,
        RequestStack $requestStack,
        RouterInterface $router,
        LoggerInterface $logger
    ) {
        $this->service = $service;
        $this->request = $requestStack->getCurrentRequest();
        $this->router = $router;
        $this->logger = $logger;
    }
}