<?php

namespace App\Infrastructure\DataFixtures;

use App\Domain\Account\Account;
use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\Message;
use App\Domain\Project\Project;
use App\Domain\Subscriber\Subscriber;
use App\Domain\Subscriber\SubscriberList;
use App\Infrastructure\Utils\Str;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CampaignFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $account = $manager->getRepository(Account::class)->findAll()[0];
        $projects = $manager->getRepository(Project::class)->findAll();
        $project = $projects[0];

        $list = new SubscriberList();
        $list->setName('Faker List 1K');
        $list->setAccount($account);
        $manager->persist($list);
        $manager->flush();

        $lists = $manager->getRepository(SubscriberList::class)->findAll();
        /** @var $list SubscriberList */
        $list = $lists[0];

        $campaign = new Campaign();
        $campaign->setStatus(Campaign::STATUS_SENDING);
        $campaign->setName('Test campaign');
        $campaign->setTitle('Test campaign');
        $campaign->setPreheader('Test campaign');
        $campaign->setFromName('a.tracz@maesto.net');
        $campaign->setFromEmail('a.tracz@maesto.net');
        $campaign->setReplyTo('a.tracz@maesto.net');

        $campaign->setAccount($account);
        $campaign->setProject($project);
        $campaign->setReceiversList($list);

        $manager->persist($campaign);
        $manager->flush();

        $subscriber = new Subscriber();
        $subscriber->setName('John Foo');
        $subscriber->setAccount($account);
        $subscriber->setEmail('john@maesto.net');
        $subscriber->setSubscribersList($list);
        $subscriber->setUser($account->getCreator());

        $manager->persist($subscriber);
        $manager->flush();

        $message = new Message();
        $message->setCampaign($campaign);
        $message->setSubscriber($subscriber);
        $message->setToken(Str::random());
        $message->setStatus(Message::STATUS_QUEUED);

        $manager->persist($message);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            AppFixtures::class,
            ProjectFixtures::class,
            SubscriberListFixtures::class,
        ];
    }
}
