<?php

namespace App\Tests\Integration;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FixturesLoader
 * @package App\Tests\Integration
 */
class FixturesLoader
{
    const FIXTURES_PATH = __DIR__.'/../../src/DataFixtures/';

    const FIXTURES_NAMESPACE = 'App\DataFixtures\\';

    /**
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface     $container
     */
    public static function resetDatabase(EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $metaData = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropSchema($metaData);
        $schemaTool->updateSchema($metaData);
        self::loadFixturesIntoDatabase($entityManager, $container);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface     $container
     */
    protected static function loadFixturesIntoDatabase(
        EntityManagerInterface $entityManager,
        ContainerInterface $container
    ) {
        $fixturesLoader = new ContainerAwareLoader($container);
        self::addFixtureClassesToLoader($fixturesLoader);
        $purger = new ORMPurger($entityManager);
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($fixturesLoader->getFixtures());
    }

    /**
     * @param ContainerAwareLoader $fixturesLoader
     */
    protected static function addFixtureClassesToLoader(ContainerAwareLoader $fixturesLoader)
    {
        $handle = opendir(self::FIXTURES_PATH);
        while ($fileName = readdir($handle)) {
            $pathInfo = pathinfo($fileName);
            if ($pathInfo['extension'] === 'php') {
                require_once self::FIXTURES_PATH.$fileName;
                $class = self::FIXTURES_NAMESPACE.$pathInfo['filename'];
                if (class_exists($class)) {
                    $fixturesLoader->addFixture(new $class);
                }
            }
        }
        closedir($handle);
    }
}