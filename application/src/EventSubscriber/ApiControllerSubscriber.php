<?php

namespace App\EventSubscriber;

use App\Controller\AbstractApiController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ApiControllerSubscriber
 * @package App\EventSubscriber
 */
class ApiControllerSubscriber implements EventSubscriberInterface
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * ApiControllerSubscriber constructor.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::EXCEPTION => 'onKernelException',
        );
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event): void
    {
        $controller = $event->getController();
        if (!$this->isControllerApiInstance($controller)) {
            $event->stopPropagation();

            return;
        }

        $this->decodeJsonRequestData($event);
    }

    /**
     * @param $controller
     *
     * @return bool
     */
    protected function isControllerApiInstance($controller): bool
    {
        return is_array($controller) && $controller[0] instanceof AbstractApiController;
    }

    /**
     * @param FilterControllerEvent $event
     */
    protected function decodeJsonRequestData(FilterControllerEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->getMethod() !== 'GET') {
            $this->processRequestData($request);
        }
    }

    /**
     * @param Request $request
     */
    protected function processRequestData(Request $request)
    {
        if ($request->getContentType() !== 'json') {
            throw new BadRequestHttpException('Request "Content-Type" header type is not set to JSON.');
        }
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('JSON body is invalid: '.json_last_error_msg());
        }

        $request->request->replace($data);
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $responseContent = $this->processResponseContent($event->getException());
        $response = new Response(json_encode($responseContent));
        $response->headers->set('Content-Type', 'application/json');
        $event->setResponse($response);
    }

    /**
     * @param \Exception $exception
     *
     * @return array
     */
    protected function processResponseContent(\Exception $exception): array
    {
        if (!$message = $exception->getMessage()) {
            $message = 'Exception';
        }
        $message .= ' in file '.$exception->getFile().' at line '.$exception->getLine();

        $responseContent = [
            'errors' => [
                'message' => $message,
            ],
        ];

        // Add stack trace to the output if this is a debug environment.
        if ($this->kernel->isDebug()) {
            $responseContent['errors']['trace'] = json_encode($exception->getTrace());
        }

        return $responseContent;
    }
}