<?php

namespace App\DataFixtures;

use App\Entity\Agency;
use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class AgencyFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            ServiceFixtures::class,
        ];
    }

    protected function getData()
    {
        return [
            [
                'name' => 'RoRo\'s Rocket Chips',
                'contact_email' => 'hello@roro.com',
                'web_address' => 'http://roro.com',
                'short_description' => 'The fieriest chips known to man.',
                'established' => '2019',
                'services' => ['web-development', 'ppc'],
            ],
            [
                'name' => 'Heavy Profesh Web Dev',
                'contact_email' => 'us@greatdevs.biz',
                'web_address' => 'https://greatdevs.biz',
                'short_description' => 'The most professional developers in town.',
                'established' => '1994',
                'services' => ['web-development', 'seo'],
            ],
            [
                'name' => 'Shass Kinsalott',
                'contact_email' => 'sounds@shasskinsal.ot',
                'web_address' => 'https://shasskinsal.ot',
                'short_description' => 'Post-modern audio branding agency based in London.',
                'established' => '2000',
                'services' => ['ppc', 'seo'],
            ],
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $agencyData) {
            $agency = $this->createAgency($agencyData);
            $manager->persist($agency);
            $manager->flush();
        }
    }

    protected function createAgency(array $data): Agency
    {
        $agency = new Agency();
        $agency->setName($data['name']);
        $agency->setContactEmail($data['contact_email']);
        $agency->setWebAddress($data['web_address']);
        $agency->setShortDescription($data['short_description']);
        $agency->setEstablished($data['established']);

        return $this->addServices($agency, $data['services']);
    }

    protected function addServices(Agency $agency, array $serviceSlugs): Agency
    {
        foreach ($serviceSlugs as $serviceSlug) {
            /** @var $service Service */
            $service = $this->getReference(ServiceFixtures::REFERENCE_PREFIX.$serviceSlug);
            $agency->addService($service);
        }

        return $agency;
    }
}