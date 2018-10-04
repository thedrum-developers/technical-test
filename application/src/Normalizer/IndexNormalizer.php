<?php

namespace App\Normalizer;

use App\Entity\EntityInterface;

/**
 * Class IndexNormalizer
 * @package App\Normalizer
 */
class IndexNormalizer extends AbstractNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_array($data) && isset($data[0]) && $data[0] instanceof EntityInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($entities, $format = null, array $context = [])
    {
        $context = $this->handleInitialContext($format, $context);
        $context['multiple_items'] = true;

        if (!$this->isNewlyPersistedContext($context)) {
            $context['no_relationships'] = true;
        }

        $data = [];
        foreach ($entities as $entity) {
            $data[] = $this->serializer->normalize($entity, $format, $context);
        }

        return $this->formatOutput($data, $context);
    }

    /**
     * Format the response output.
     *
     * @param array $data
     * @param array $context
     *
     * @return array
     */
    protected function formatOutput(array $data, array $context): array
    {
        $data = ['data' => $data];

        if (!$this->isIndexFormatContext($context)) {
            return $data;
        }

        $data['links'] = [
            'self' => $context['base_url'],
        ];

        return $data;
    }

    /**
     * Check the context to see if entities have been newly written to the database.
     *
     * @param array $context
     *
     * @return bool
     */
    protected function isNewlyPersistedContext(array $context): bool
    {
        return isset($context['newly_persisted']) && $context['newly_persisted'];
    }

    /**
     * Check the context to see if output should be formatted for an index context.
     *
     * @param array $context
     *
     * @return bool
     */
    protected function isIndexFormatContext(array $context): bool
    {
        return isset($context['index_format']) && $context['index_format'];
    }
}