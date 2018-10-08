<?php

namespace App\Infrastructure\DataFixtures;

use App\Domain\Account\Account;
use App\Domain\Project\Project;
use App\Domain\Template\Template;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $account = $manager->getRepository(Account::class)->find(1);

        $template = new Template();
        $template->setName('Test');
        $template->setIsPublic(true);
        $template->setIsActive(true);
        $manager->persist($template);
        $manager->flush();

        $project = new Project();
        $project->setName('Test project');
        $project->setAccount($account);
        $project->setTemplate($template);
        $project->setContents('Test contents');

        $manager->persist($project);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            AppFixtures::class,
        ];
    }
}
