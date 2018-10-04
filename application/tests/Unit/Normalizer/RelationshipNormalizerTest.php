<?php

namespace App\Tests\Unit\Normalizer;

use Prophecy\Argument;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * Class RelationshipNormalizerTest
 * @package App\Tests\Unit\Normalizer
 */
class RelationshipNormalizerTest extends NormalizerTestCase
{
    public function setUp()
    {
        $this->className = 'App\Normalizer\RelationshipNormalizer';
    }

    /**
     * Ensure `supportsNormalization()` returns true when the passed object is an instance of
     * `Collection`.
     */
    public function testSupportsNormalizationReturnsTrueWhenClassIsInstanceOfCollection()
    {
        $mockCollection = $this->buildMockCollection();
        $normaliser = $this->buildNormaliser();
        $this->assertEquals(true, $normaliser->supportsNormalization($mockCollection));
    }

    /**
     * Ensure `supportsNormalization()` returns false when the passed object is not an instance of
     * `Collection`.
     */
    public function testSupportsNormalizationReturnsFalseWhenClassIsNotInstanceOfCollection()
    {
        $normaliser = $this->buildNormaliser();
        $this->assertEquals(false, $normaliser->supportsNormalization(new \stdClass()));
    }

    /**
     * Ensure the normalization output is formatted correctly when the 'relationships' element
     * is set in the context. This should result in the data being output with links appended.
     */
    public function testNormalizeOutputsCorrectlyWhenRelationshipsContextIsSet()
    {
        $mockCollection = $this->buildMockCollection();
        // Need to mock this class even though it isn't owned- `SerializerInterface` has no `normalise()` method.
        $mockSerializer = $this->prophesize('Symfony\Component\Serializer\Serializer');
        $mockSerializer->normalize(Argument::type('App\Entity\EntityInterface'), 'json', Argument::type('array'))
            ->willReturn('Foo', 'Bar');

        $normaliser = $this->buildNormaliser($mockSerializer->reveal());

        $context = [
            'type' => 'test-type',
            'base_url' => 'http://baseurl.com/relationships',
            'relationships' => true
        ];
        $expected = [
            'data' => ['Foo', 'Bar'],
            'links' => [
                'self' => 'http://baseurl.com/relationships',
                'related' => 'http://baseurl.com'
            ]
        ];
        $actual = $normaliser->normalize($mockCollection, 'json', $context);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Ensure the normalization output is formatted correctly when the 'relationships' element
     * is not set in the context. This should result in the data being output without links
     * being appended.
     */
    public function testNormalizeOutputsCorrectlyWhenRelationshipsContextIsNotSet()
    {
        $mockCollection = $this->buildMockCollection();
        // Need to mock this class even though it isn't owned- `SerializerInterface` has no `normalise()` method.
        $mockSerializer = $this->prophesize('Symfony\Component\Serializer\Serializer');
        $mockSerializer->normalize(Argument::type('App\Entity\EntityInterface'), 'json', Argument::type('array'))
            ->willReturn('Foo', 'Bar');

        $normaliser = $this->buildNormaliser($mockSerializer->reveal());

        $context = [
            'type' => 'test-type',
            'base_url' => 'http://baseurl.com',
        ];
        $actual = $normaliser->normalize($mockCollection, 'json', $context);
        $this->assertEquals(['Foo', 'Bar'], $actual);
    }

    /**
     * Ensure that the serializer is called correctly when sending entity instances
     * to another class for further processing.
     */
    public function testSerializerMockIsCalledCorrectly()
    {
        $mockCollection = $this->buildMockCollection();
        // Need to mock this class even though it isn't owned- `SerializerInterface` has no `normalise()` method.
        $mockSerializer = $this->prophesize('Symfony\Component\Serializer\Serializer');

        $normaliser = $this->buildNormaliser($mockSerializer->reveal());

        $context = [
            'type' => 'test-type',
            'base_url' => 'http://baseurl.com',
        ];
        $normaliser->normalize($mockCollection, 'json', $context);
        $mockSerializer->normalize(
            Argument::type('App\Entity\EntityInterface'),
            'json',
            Argument::withEntry('relationship_item', true)
        )->shouldBeCalledTimes(2);
    }

    /**
     * Ensure that the 'cache_key' element is set in the context.
     */
    public function testNormalizeSetsCacheKeyInContext()
    {
        $mockCollection = $this->buildMockCollection();
        // Need to mock this class even though it isn't owned- `SerializerInterface` has no `normalise()` method.
        $mockSerializer = $this->prophesize('Symfony\Component\Serializer\Serializer');

        $normaliser = $this->buildNormaliser($mockSerializer->reveal());

        $context = [
            'type' => 'test-type',
            'base_url' => 'http://baseurl.com',
        ];
        $normaliser->normalize($mockCollection, 'json', $context);
        $mockSerializer->normalize(
            Argument::type('App\Entity\EntityInterface'),
            'json',
            Argument::withEntry('cache_key', Argument::type('string'))
        )->shouldHaveBeenCalled();
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
    protected function buildMockCollection()
    {
        $mockEntity = $this->prophesize('App\Entity\EntityInterface')->reveal();
        $mockCollection = $this->prophesize('Doctrine\Common\Collections\Collection');
        $mockCollection->isEmpty()->willReturn(false);
        $mockCollection->first()->willReturn($mockEntity);
        $iterator = new \ArrayIterator([$mockEntity, $mockEntity]);
        $mockCollection->getIterator()->willReturn($iterator);

        return $mockCollection->reveal();
    }
}
