<?php

namespace App\Normalizer;

use App\Entity\EntityInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Class AttributeNormalizer
 * @package App\Normalizer
 */
class AttributeNormalizer extends AbstractNormalizer
{
    /**
     * @var
     */
    protected $slug;

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof EntityInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        $context = $this->handleInitialContext($format, $context);
        $data = [
            'type' => $context['type'],
            'id' => (string)$this->getAttributeValue($object, 'id'),
        ];

        return $this->contextuallyProcess($data, $object, $format, $context);
    }

    /**
     * Process data depending on data context.
     *
     * @param array           $data
     * @param EntityInterface $object
     * @param string|null     $format
     * @param array           $context
     *
     * @return array
     */
    protected function contextuallyProcess(array $data, EntityInterface $object, ?string $format, array $context): array
    {
        // If this is a relationship item then return only the id and type.
        if ($this->isRelationshipItemContext($context)) {
            return $data;
        }

        $data = array_merge($data, $this->iterateAttributeValues($object, $format, $context));
        $data = $this->attachResourceLinks($data, $context);

        // If this is an index of multiple items return the attribute values as they are for wrapping elsewhere.
        if ($this->isMultipleItemsContext($context)) {
            return $data;
        }

        // If this is neither then wrap it for output as an API resource.
        return [
            'data' => [$data],
        ];
    }

    /**
     * Iterate and process all an entity's attribute values.
     *
     * @param EntityInterface $entity
     * @param string|null     $format
     * @param array           $context
     *
     * @return array
     */
    protected function iterateAttributeValues(EntityInterface $entity, ?string $format, array $context): array
    {
        $data = [];
        foreach ($this->filterIdFromAttributes($entity) as $attribute) {
            $value = $this->getAttributeValue($entity, $attribute);
            if (!$value instanceof Collection) {
                $data['attributes'][$attribute] = $value;
            } else {
                if (!$this->isSlugAttachedContext($context)) {
                    $this->setSlugValue($entity, $context);
                }
                $data = $this->processCollection($data, $value, $attribute, $format, $context);
            }
        }

        return $data;
    }

    /**
     * Process a collection of entities.
     *
     * @param array       $data
     * @param Collection  $value
     * @param string      $attribute
     * @param null|string $format
     * @param array       $context
     *
     * @return array
     */
    protected function processCollection(
        array $data,
        Collection $value,
        string $attribute,
        ?string $format,
        array $context
    ): array {

        if ($this->isNoRelationshipsContext($context)) {
            return $this->attachResourceLinks($data, $context);
        } elseif ($relationships = $this->processRelationshipObjects($value, $attribute, $format, $context)) {
            $data['relationships'][$attribute] = $relationships;
        }

        return $data;
    }

    /**
     * Process an item's relationships.
     *
     * @param Collection  $value
     * @param string      $attribute
     * @param string|null $format
     * @param array       $context
     *
     * @return array|null
     */
    protected function processRelationshipObjects(
        Collection $value,
        string $attribute,
        ?string $format,
        array $context
    ): ?array {
        if ($data['data'] = $this->serializer->normalize($value, $format, $context)) {
            $context['type'] = $attribute;
            if ($data['data'] = $this->serializer->normalize($value, $format, $context)) {
                return $this->attachRelationshipLinks($data, $context, $attribute);
            }
        }

        return null;
    }

    /**
     * Get the value of the slug to use when generating links.
     *
     * @param EntityInterface $entity
     * @param array           $context
     *
     * @return void
     */
    protected function setSlugValue(EntityInterface $entity, array $context): void
    {
        if (isset($context['slug_attribute']) && $context['slug_attribute']) {
            if ($slug = $this->getAttributeValue($entity, $context['slug_attribute'])) {
                $this->slug = $slug;

                return;
            }
        }
        $this->slug = $this->getAttributeValue($entity, 'id');

    }

    /**
     * Attach links to a resource object.
     *
     * @param array $data
     * @param array $context
     *
     * @return array
     */
    protected function attachResourceLinks(array $data, array $context): array
    {
        $suffix = null;
        if ($this->hasRelationSlugContext($context)) {
            $this->slug = $data['attributes'][$context['relation_slug']];
        }
        if ($this->slug) {
            $suffix = '/'.$this->slug;
        }
        $data['links'] = [
            'self' => $context['base_url'].$suffix,
        ];

        // If the context's 'related_url_strip' attribute is set then it should be extracted from
        // the base URL and the result attached as a related link.
        if ($this->isRelatedUrlStripContext($context)) {
            $strippedBaseUrl = str_replace('/'.$context['related_url_strip'], '', $context['base_url']);
            $data['links']['related'] = $strippedBaseUrl.'/'.$this->slug;
        }

        return $data;
    }

    /**
     * Attach links to a relationships resource.
     *
     * @param array  $data
     * @param array  $context
     * @param string $attribute
     *
     * @return array
     */
    protected function attachRelationshipLinks(array $data, array $context, string $attribute): array
    {
        $slugPath = '/';
        if ($this->slug) {
            $slugPath .= $this->slug.'/';
        }
        $data['links'] = [
            'self' => $context['base_url'].$slugPath.'relationships/'.$attribute,
            'related' => $context['base_url'].$slugPath.$attribute,
        ];

        return $data;
    }

    /**
     * Check the context to see if this is a relationship item.
     *
     * @param array $context
     *
     * @return bool
     */
    protected function isRelationshipItemContext(array $context): bool
    {
        return isset($context['relationship_item']) && $context['relationship_item'] === true;
    }

    /**
     * Check the context to see if relationships should be excluded.
     *
     * @param array $context
     *
     * @return bool
     */
    protected function isNoRelationshipsContext(array $context): bool
    {
        return isset($context['no_relationships']) && $context['no_relationships'] === true;
    }

    /**
     * Check context to see if output should be formatted as an index.
     *
     * @param array $context
     *
     * @return bool
     */
    protected function isMultipleItemsContext(array $context): bool
    {
        return isset($context['multiple_items']) && $context['multiple_items'] === true;
    }

    /**
     * Check the context to see if the base url already has a slug attached.
     *
     * @param array $context
     *
     * @return bool
     */
    protected function isSlugAttachedContext(array $context): bool
    {
        return isset($context['slug_attached']) && $context['slug_attached'] = true;
    }

    /**
     * Check the context to see if a parent slug that should be stripped is specified.
     *
     * @param array $context
     *
     * @return bool
     */
    protected function isRelatedUrlStripContext(array $context): bool
    {
        return isset($context['related_url_strip']) && $context['related_url_strip'];
    }

    /**
     * Check the context to see if a related slug is specified.
     *
     * @param array $context
     *
     * @return bool
     */
    protected function hasRelationSlugContext(array $context): bool
    {
        return isset($context['relation_slug']) && $context['relation_slug'];
    }

    /**
     * Filter an entities ID from the rest of the attributes array.
     *
     * @param EntityInterface $entity
     *
     * @return array
     */
    protected function filterIdFromAttributes(EntityInterface $entity): array
    {
        $attributes = $this->extractAttributes($entity);
        $idKey = array_search('id', $attributes);
        unset($attributes[$idKey]);

        return $attributes;
    }
}