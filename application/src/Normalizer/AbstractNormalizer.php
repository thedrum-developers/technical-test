<?php

namespace App\Normalizer;

use App\Exception\InvalidNormalizerContextException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class AbstractNormalizer
 * @package App\Normalizer
 */
abstract class AbstractNormalizer extends GetSetMethodNormalizer
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var Serializer */
    protected $serializer;

    /**
     * Handle the initial context passed to the normalizer.
     *
     * @param string $format
     * @param array  $context
     *
     * @return array
     * @throws InvalidNormalizerContextException
     */
    protected function handleInitialContext(string $format, array $context): array
    {
        if (!isset($context['cache_key'])) {
            $context['cache_key'] = $this->getCacheKey($format, $context);
        }
        if (!isset($context['type'])) {
            $message = sprintf('"type" member must be set');
            throw new InvalidNormalizerContextException($message);
        }
        if (!isset($context['base_url'])) {
            throw new InvalidNormalizerContextException('Base URL not found in context.');
        }

        return $context;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCacheKey(?string $format, array $context)
    {
        try {
            return md5($format.serialize($context));
        } catch (\Exception $exception) {
            // The context cannot be serialized, skip the cache
            return false;
        }
    }
}