<?php

namespace App\Infrastructure\DataFixtures;

use App\Domain\Account\Account;
use App\Domain\Subscriber\Subscriber;
use App\Domain\Subscriber\SubscriberList;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SubscriberListFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $account = $manager->getRepository(Account::class)->find(1);

        if (null == $account) {
            return;
        }

        $list = new SubscriberList();
        $list->setName('Faker List 100');
        $list->setAccount($account);
        $manager->persist($list);
        $manager->flush();

        $faker = \Faker\Factory::create();

        for ($i = 1; $i <= 100; ++$i) {
            $subscriber = new Subscriber();
            $subscriber->setAccount($account);
            $subscriber->setSubscribersList($list);
            $subscriber->setUser($account->getCreator());
            $subscriber->setName($faker->firstName);
            $subscriber->setSurname($faker->lastName);
            $subscriber->setEmail($faker->email);
            $manager->persist($subscriber);
            $manager->flush();
        }
    }

    public function getDependencies()
    {
        return [
            AppFixtures::class,
        ];
    }
}
