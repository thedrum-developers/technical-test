<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    protected function getData()
    {
        return [
            'userName' => 'test',
            'apiKey' => '1234567890',
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $service = $this->createService($this->getData());
        $manager->persist($service);
        $manager->flush();
    }

    protected function createService(array $data): User
    {
        $service = new User();
        $service->setUsername($data['userName']);
        $service->setApiKey($data['apiKey']);

        return $service;
    }
}