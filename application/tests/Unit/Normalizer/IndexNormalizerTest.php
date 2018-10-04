<?php

namespace App\Tests\Unit\Normalizer;

use Prophecy\Argument;

/**
 * Class IndexNormalizerTest
 * @package App\Tests\Unit\Normalizer
 */
class IndexNormalizerTest extends NormalizerTestCase
{
    public function setUp()
    {
        $this->className = 'App\Normalizer\IndexNormalizer';
    }

    /**
     * Ensure `supportsNormalization()` returns true when the passed object is an array of
     * `EntityInterface` instances.
     */
    public function testSupportsNormalizationReturnsTrueWhenDataIsAnArrayContainingEntityInterfaceInstances()
    {
        $mockEntity = $this->prophesize('App\Entity\EntityInterface');
        $normaliser = $this->buildNormaliser();
        $this->assertEquals(true, $normaliser->supportsNormalization([$mockEntity->reveal()]));
    }

    /**
     * Ensure `supportsNormalization()` returns false when the passed object is not an array of
     * `EntityInterface` instances.
     */
    public function testSupportsNormalizationReturnsFalseWhenDataIsNotAnArrayContainingEntityInterfaceInstances()
    {
        $normaliser = $this->buildNormaliser();
        $this->assertEquals(false, $normaliser->supportsNormalization(new \stdClass()));
    }

    /**
     * Ensure normalized outpiut is formatted correctly.
     */
    public function testNormalizeOutputsCorrectly()
    {
        $mockEntity = $this->prophesize('App\Entity\EntityInterface')->reveal();
        $mockEntities = [$mockEntity, $mockEntity];

        // Need to mock this class even though it isn't owned- `SerializerInterface` has no `normalise()` method.
        $mockSerializer = $this->prophesize('Symfony\Component\Serializer\Serializer');
        $mockSerializer->normalize(Argument::type('App\Entity\EntityInterface'), 'json', Argument::type('array'))
            ->willReturn('Foo', 'Bar');

        $normaliser = $this->buildNormaliser($mockSerializer->reveal());

        $context = [
            'type' => 'test-type',
            'base_url' => 'http://baseurl.com',
            'index_format' => true,
        ];
        $expected = [
            'data' => ['Foo', 'Bar'],
            'links' => [
                'self' => 'http://baseurl.com',
            ],
        ];
        $actual = $normaliser->normalize($mockEntities, 'json', $context);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Ensure that the serializer is called correctly when sending entity instances
     * to another class for further processing.
     */
    public function testSerializerMockIsCalledCorrectly()
    {
        $mockEntity = $this->prophesize('App\Entity\EntityInterface')->reveal();
        $mockEntities = [$mockEntity, $mockEntity];

        // Need to mock this class even though it isn't owned- `SerializerInterface` has no `normalise()` method.
        $mockSerializer = $this->prophesize('Symfony\Component\Serializer\Serializer');
        $mockSerializer->normalize(Argument::type('App\Entity\EntityInterface'), 'json', Argument::type('array'))
            ->willReturn('Foo', 'Bar');

        $normaliser = $this->buildNormaliser($mockSerializer->reveal());

        $context = [
            'type' => 'test-type',
            'base_url' => 'http://baseurl.com',
        ];
        $normaliser->normalize($mockEntities, 'json', $context);
        $mockSerializer->normalize(
            Argument::type('App\Entity\EntityInterface'),
            'json',
            Argument::withEntry('multiple_items', true)
        )->shouldBeCalledTimes(2);
    }

    /**
     * Ensure that the 'cache_key' element is set in the context.
     */
    public function testNormalizeSetsCacheKeyInContext()
    {
        $mockEntity = $this->prophesize('App\Entity\EntityInterface')->reveal();
        $mockEntities = [$mockEntity, $mockEntity];

        // Need to mock this class even though it isn't owned- `SerializerInterface` has no `normalise()` method.
        $mockSerializer = $this->prophesize('Symfony\Component\Serializer\Serializer');
        $normaliser = $this->buildNormaliser($mockSerializer->reveal());

        $context = [
            'type' => 'test-type',
            'base_url' => 'http://baseurl.com',
        ];
        $normaliser->normalize($mockEntities, 'json', $context);
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
}
