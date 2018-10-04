<?php

namespace App\Normalizer;

use Doctrine\Common\Collections\Collection;

/**
 * Class RelationshipNormalizer
 * @package App\Normalizer
 */
class RelationshipNormalizer extends AbstractNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Collection;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $context = $this->handleInitialContext($format, $context);
        $context['relationship_item'] = true;

        $data = [];
        foreach ($object as $entity) {
            $data[] = $this->serializer->normalize($entity, $format, $context);
        }

        if ($this->isRelationshipsContext($context)) {
            return $this->formatRelationshipContextOutput($data, $context);
        }

        return $data;
    }

    /**
     * Check the context to see if the output should be formatted for a relationships object.
     *
     * @param array $context
     *
     * @return bool
     */
    protected function isRelationshipsContext(array $context): bool
    {
        return isset($context['relationships']) && $context['relationships'] === true;
    }

    /**
     * Format the output for a relationships object.
     *
     * @param array $data
     * @param array $context
     *
     * @return array
     */
    protected function formatRelationshipContextOutput(array $data, array $context): array
    {
        return [
            'data' => $data,
            'links' => [
                'self' => $context['base_url'],
                'related' => str_replace('/relationships', '', $context['base_url']),
            ],
        ];
    }
}