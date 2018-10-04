<?php

namespace App\Normalizer;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

/**
 * Class RelationshipNormalizer
 * @package App\Normalizer
 */
class CollectionNormalizer extends AbstractNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_array($data) && isset($data[0]) && $data[0] instanceof Collection;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        $context = $this->handleInitialContext($format, $context);

        $context['relationship_item'] = true;

        $data = [];
        foreach ($object as $collection) {
            $data = array_merge($data, $this->processItems($collection, $format, $context));
        }

        return [
            'data' => $data,
            'links' => [
                'self' => $context['base_url'],
                'related' => str_replace('/relationships', '', $context['base_url']),
            ],
        ];
    }

    /**
     * Process all entities in a collection.
     *
     * @param PersistentCollection $collection
     * @param string               $format
     * @param array                $context
     *
     * @return array
     */
    protected function processItems(PersistentCollection $collection, string $format, array $context): array
    {
        $processedItems = [];
        foreach ($collection as $entity) {
            $processedItem = $this->serializer->normalize($entity, $format, $context);
            $processedItem['type'] = $collection->getMapping()['fieldName'];
            $processedItems[] = $processedItem;
        }
        return $processedItems;
    }
}