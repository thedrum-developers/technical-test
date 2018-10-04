<?php

namespace App\Tests\Unit\Normalizer;

use App\Entity\EntityInterface;
use Doctrine\Common\Collections\Collection;
use Prophecy\Argument;

/**
 * Class AttributeNormalizerTest
 * @package App\Tests\Unit\Normalizer
 */
class AttributeNormalizerTest extends NormalizerTestCase
{
    public function setUp()
    {
        $this->className = 'App\Normalizer\AttributeNormalizer';
    }

    /**
     * Ensure `supportsNormalization()` returns true when the passed object is an instance of
     * `EntityInterface`.
     */
    public function testSupportsNormalizationReturnsTrueWhenClassIsInstanceOfEntityInterface()
    {
        $mockEntity = $this->prophesize('App\Entity\EntityInterface')->reveal();
        $normaliser = $this->buildNormaliser();
        $this->assertEquals(true, $normaliser->supportsNormalization($mockEntity));
    }

    /**
     * Ensure `supportsNormalization()` returns false when the passed object is not an instance of
     * `EntityInterface`.
     */
    public function testSupportsNormalizationReturnsFalseWhenClassIsNotInstanceOfEntityInterface()
    {
        $normaliser = $this->buildNormaliser();
        $this->assertEquals(false, $normaliser->supportsNormalization(new \stdClass()));
    }

    /**
     * Ensure the normalization output is formatted correctly when normalising a single item object.
     */
    public function testNormalizeOutputsCorrectlyWhenNormalizingSingleItem()
    {
        $mockCollection = $this->buildMockCollection();
        $mockSerializer = $this->prophesize('Symfony\Component\Serializer\Serializer');

        $normaliser = $this->buildNormaliser($mockSerializer->reveal());
        $mockEntity = $this->buildMockEntity(1, $mockCollection);

        $context = [
            'type' => 'test-type',
            'base_url' => 'http://baseurl.com',
        ];
        $expected = [
            'data' => [
                [
                    'id' => '1',
                    'type' => 'test-type',
                    'attributes' => [
                        'name' => 'Test Name',
                        'slug' => 'test-slug',
                    ],
                    'links' => [
                        'self' => 'http://baseurl.com/1',
                    ],
                ],
            ],
        ];
        $actual = $normaliser->normalize($mockEntity, 'json', $context);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Ensure the normalization output is formatted correctly when normalising an index.
     */
    public function testNormalizeOutputsCorrectlyWhenNormalizingIndex()
    {
        $mockCollection = $this->buildMockCollection();
        $mockSerializer = $this->prophesize('Symfony\Component\Serializer\Serializer');

        $normaliser = $this->buildNormaliser($mockSerializer->reveal());
        $mockEntity = $this->buildMockEntity(1, $mockCollection);

        $context = [
            'type' => 'test-type',
            'base_url' => 'http://baseurl.com',
            'multiple_items' => true,
            'slug_attribute' => 'slug',
        ];
        $expected = [
            'id' => '1',
            'type' => 'test-type',
            'attributes' => [
                'name' => 'Test Name',
                'slug' => 'test-slug',
            ],
            'links' => [
                'self' => 'http://baseurl.com/test-slug',
            ],
        ];
        $actual = $normaliser->normalize($mockEntity, 'json', $context);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Ensure the normalization output is formatted correctly when the 'slug_attribute' element
     * is set in the context. This should result in the 'slug_attribute' value being appended to the URL.
     */
    public function testNormalizeAttachesCorrectLinksWhenSlugAttributeIsSetInContext()
    {
        $mockCollection = $this->buildMockCollection();
        $mockSerializer = $this->prophesize('Symfony\Component\Serializer\Serializer');

        $normaliser = $this->buildNormaliser($mockSerializer->reveal());
        $mockEntity = $this->buildMockEntity(1, $mockCollection);

        $context = [
            'type' => 'test-type',
            'base_url' => 'http://baseurl.com',
            'multiple_items' => true,
            'slug_attribute' => 'slug',
        ];
        $expected = [
            'id' => '1',
            'type' => 'test-type',
            'attributes' => [
                'name' => 'Test Name',
                'slug' => 'test-slug',
            ],
            'links' => [
                'self' => 'http://baseurl.com/test-slug',
            ],
        ];
        $actual = $normaliser->normalize($mockEntity, 'json', $context);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Ensure the normalization output is formatted correctly when the 'slug_attribute' element
     * is not set in the context. This should result in the entity's ID being appended to the URL.
     */
    public function testNormalizeAttachesCorrectLinksWhenSlugAttributeIsNotSetInContext()
    {
        $mockCollection = $this->buildMockCollection();
        $mockSerializer = $this->prophesize('Symfony\Component\Serializer\Serializer');

        $normaliser = $this->buildNormaliser($mockSerializer->reveal());
        $mockEntity = $this->buildMockEntity(1, $mockCollection);

        $context = [
            'type' => 'test-type',
            'base_url' => 'http://baseurl.com',
            'multiple_items' => true,
        ];
        $expected = [
            'id' => '1',
            'type' => 'test-type',
            'attributes' => [
                'name' => 'Test Name',
                'slug' => 'test-slug',
            ],
            'links' => [
                'self' => 'http://baseurl.com/1',
            ],
        ];
        $actual = $normaliser->normalize($mockEntity, 'json', $context);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Ensure the normalization output is formatted correctly when the 'related_url_strip' element
     * is set in the context. This ensures parent slug tokens are removed when creating "related" links.
     */
    public function testNormalizeAttachesCorrectLinksWhenIsRelatedUrlStripContext()
    {
        $mockCollection = $this->buildMockCollection();
        $mockSerializer = $this->prophesize('Symfony\Component\Serializer\Serializer');

        $normaliser = $this->buildNormaliser($mockSerializer->reveal());
        $mockEntity = $this->buildMockEntity(1, $mockCollection);

        $context = [
            'type' => 'test-type',
            'base_url' => 'http://baseurl.com/slug-to-strip',
            'multiple_items' => true,
            'related_url_strip' => 'slug-to-strip',
        ];
        $expected = [
            'id' => '1',
            'type' => 'test-type',
            'attributes' => [
                'name' => 'Test Name',
                'slug' => 'test-slug',
            ],
            'links' => [
                'self' => 'http://baseurl.com/slug-to-strip/1',
                'related' => 'http://baseurl.com/1',
            ],
        ];
        $actual = $normaliser->normalize($mockEntity, 'json', $context);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Ensure the normalization output is formatted correctly when the 'no_relationships' element
     * is set in the context. This should prevent "relationships" objects being attached to the
     * output.
     */
    public function testNormalizeAttachesCorrectLinksWhenIsNoRelationshipsContext()
    {
        $mockCollection = $this->buildMockCollection();
        $mockSerializer = $this->prophesize('Symfony\Component\Serializer\Serializer');

        $normaliser = $this->buildNormaliser($mockSerializer->reveal());
        $mockEntity = $this->buildMockEntity(1, $mockCollection);

        $context = [
            'type' => 'test-type',
            'base_url' => 'http://baseurl.com',
            'multiple_items' => true,
            'no_relationships' => true,
        ];
        $expected = [
            'id' => '1',
            'type' => 'test-type',
            'attributes' => [
                'name' => 'Test Name',
                'slug' => 'test-slug',
            ],
            'links' => [
                'self' => 'http://baseurl.com/1',
            ],
        ];
        $actual = $normaliser->normalize($mockEntity, 'json', $context);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Ensure the normalization output is formatted correctly when the 'relationship_item' element
     * is set in the context. This should ensure only minimal details for the item are output - its
     * "id" and "type".
     */
    public function testNormalizeOutputsCorrectlyWhenNormalizingRelationshipObject()
    {
        $mockCollection = $this->buildMockCollection();
        $mockSerializer = $this->prophesize('Symfony\Component\Serializer\Serializer');

        $normaliser = $this->buildNormaliser($mockSerializer->reveal());
        $mockEntity = $this->buildMockEntity(1, $mockCollection);

        $context = [
            'type' => 'test-type',
            'base_url' => 'http://baseurl.com',
            'relationship_item' => true,
        ];
        $expected = [
            'id' => '1',
            'type' => 'test-type',
        ];
        $actual = $normaliser->normalize($mockEntity, 'json', $context);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Ensure the normalization output is formatted correctly when outputting an item and
     * its "relationships" objects.
     */
    public function testNormalizeOutputsCorrectlyWhenNormalizingItemWithRelationshipsObject()
    {
        $mockCollection = $this->buildMockCollection();
        $mockSerializer = $this->prophesize('Symfony\Component\Serializer\Serializer');
        $mockSerializer->normalize($mockCollection, 'json', Argument::type('array'))
            ->willReturn(['Collection']);

        $normaliser = $this->buildNormaliser($mockSerializer->reveal());
        $mockEntity = $this->buildMockEntity(1, $mockCollection);

        $context = [
            'type' => 'test-type',
            'base_url' => 'http://baseurl.com',
            'slug_attribute' => 'slug',
        ];
        $expected = [
            'data' => [
                [
                    'id' => '1',
                    'type' => 'test-type',
                    'attributes' => [
                        'name' => 'Test Name',
                        'slug' => 'test-slug',
                    ],
                    'relationships' => [
                        'collections' => [
                            'data' => ['Collection'],
                            'links' => [
                                'self' => 'http://baseurl.com/test-slug/relationships/collections',
                                'related' => 'http://baseurl.com/test-slug/collections',
                            ],
                        ],
                    ],
                    'links' => [
                        'self' => 'http://baseurl.com/test-slug',
                    ],
                ],
            ],
        ];
        $actual = $normaliser->normalize($mockEntity, 'json', $context);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Ensure that the serializer is called correctly when sending Collection instances
     * to another class for further processing.
     */
    public function testSerializerMockIsCalledCorrectlyWhenProcessingCollections()
    {
        $mockCollection = $this->buildMockCollection();
        $mockSerializer = $this->prophesize('Symfony\Component\Serializer\Serializer');

        $normaliser = $this->buildNormaliser($mockSerializer->reveal());
        $mockEntity = $this->buildMockEntity(1, $mockCollection);

        $context = [
            'type' => 'test-type',
            'base_url' => 'http://baseurl.com',
        ];
        $normaliser->normalize($mockEntity, 'json', $context);
        $mockSerializer->normalize(
            Argument::type('Doctrine\Common\Collections\Collection'),
            'json',
            Argument::type('array')
        )->shouldHaveBeenCalledOnce();
    }

    /**
     * Ensure that the 'cache_key' element is set in the context.
     */
    public function testNormalizeSetsCacheKeyInContext()
    {
        $mockCollection = $this->buildMockCollection();
        $mockSerializer = $this->prophesize('Symfony\Component\Serializer\Serializer');

        $normaliser = $this->buildNormaliser($mockSerializer->reveal());
        $mockEntity = $this->buildMockEntity(1, $mockCollection);

        $context = [
            'type' => 'test-type',
            'base_url' => 'http://baseurl.com',
        ];
        $normaliser->normalize($mockEntity, 'json', $context);
        $mockSerializer->normalize(
            $mockCollection,
            'json',
            Argument::withEntry('cache_key', Argument::type('string'))
        )->shouldHaveBeenCalledOnce();
    }

    /**
     * Ensure the normalizer throws an exception when no 'type' value is specified
     * in the context.
     */
    public function testNormalizerThrowsExceptionWhenTypeNotFoundInContext()
    {
        $mockEntity = $this->prophesize('App\Entity\EntityInterface')->reveal();
        $normaliser = $this->buildNormaliser();

        $this->expectException('App\Exception\InvalidNormalizerContextException');
        $normaliser->normalize($mockEntity, 'json', ['base_url' => 'http://baseurl.com']);
    }

    /**
     * Ensure the normalizer throws an exception when no 'base_url' value is specified
     * in the context.
     */
    public function testNormalizerThrowsExceptionWhenBaseUrlNotFoundInContext()
    {
        $mockEntity = $this->prophesize('App\Entity\EntityInterface')->reveal();
        $normaliser = $this->buildNormaliser();

        $this->expectException('App\Exception\InvalidNormalizerContextException');
        $normaliser->normalize($mockEntity, 'json', ['type' => 'test_type']);
    }

    /**
     * Build a mock `Collection` instance.
     *
     * @return Collection
     */
    protected function buildMockCollection(): Collection
    {
        $mockEntity = $this->prophesize('App\Entity\EntityInterface')->reveal();
        $mockCollection = $this->prophesize('Doctrine\Common\Collections\Collection');
        $mockCollection->isEmpty()->willReturn(false);
        $mockCollection->first()->willReturn($mockEntity);

        return $mockCollection->reveal();
    }

    /**
     * Build a mock entity.
     *
     * @param int|null        $id
     * @param Collection|null $mockCollection
     *
     * @return EntityInterface
     */
    protected function buildMockEntity(?int $id, ?Collection $mockCollection): EntityInterface
    {
        return new class($id, $mockCollection) implements EntityInterface
        {
            public $id;
            public $mockCollection;

            function __construct(?int $id, ?Collection $mockCollection)
            {
                $this->id = $id;
                $this->mockCollection = $mockCollection;
            }

            function getId(): ?int
            {
                return $this->id;
            }

            function getName()
            {
                return 'Test Name';
            }

            function getSlug()
            {
                return 'test-slug';
            }

            function getCollections()
            {
                return $this->mockCollection;
            }
        };
    }
}
