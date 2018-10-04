<?php

namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ServiceFixtures extends Fixture
{
    public const REFERENCE_PREFIX = 'service_';

    protected function getData()
    {
        return [
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
            ],
            [
                'name' => 'PPC',
                'slug' => 'ppc',
            ],
            [
                'name' => 'SEO',
                'slug' => 'seo',
            ],
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $data) {
            $service = $this->createService($data);
            $manager->persist($service);
            $manager->flush();
            $this->addReference(self::REFERENCE_PREFIX.$service->getSlug(), $service);
        }
    }

    protected function createService(array $data): Service
    {
        $service = new Service();
        $service->setName($data['name']);
        $service->setSlug($data['slug']);

        return $service;
    }
}