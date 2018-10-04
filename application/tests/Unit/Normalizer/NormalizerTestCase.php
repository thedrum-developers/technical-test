<?php

namespace App\Tests\Unit\Normalizer;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class NormalizerTestCase
 * @package App\Tests\Unit\Normalizer
 */
class NormalizerTestCase extends TestCase
{
    /**
     * @var
     */
    protected $className;

    /**
     * Build an instance of a Normalizer class under test.
     *
     * @param null|SerializerInterface $mockSerializer
     *
     * @return NormalizerInterface
     */
    protected function buildNormaliser(?SerializerInterface $mockSerializer = null): NormalizerInterface
    {
        $mockClassMetadataFactory = $this->prophesize(
            'Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface'
        )->reveal();
        $mockClassDiscriminationResolver = $this->prophesize(
            'Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface'
        )->reveal();

        /** @var NormalizerInterface $normaliser */
        $normaliser = new $this->className(
            $mockClassMetadataFactory,
            null,
            null,
            $mockClassDiscriminationResolver
        );

        if ($mockSerializer) {
            $normaliser->setSerializer($mockSerializer);
        }

        return $normaliser;
    }
}