<?php

namespace App\Test\Infrastructure\Subscriber;

use App\Domain\Account\Account;
use App\Domain\Subscriber\Subscriber;
use App\Domain\Subscriber\SubscriberList;
use App\Domain\User\User;
use App\Infrastructure\Subscriber\Repository\SubscriberRepository;
use App\Infrastructure\Subscriber\SubscriberManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SubscriberManagerTest extends KernelTestCase
{
    protected $validator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->validator = null;
    }

    /**
     * @test
     */
    public function it_creates_subscriber_from_raw_attributes()
    {
        $user = new User();
        $list = new SubscriberList();
        $account = new Account();
        $attributes = [
            'name' => 'John',
            'surname' => 'Foo',
            'email' => 'john.foo@dev.local',
        ];

        $repo = $this->createMock(SubscriberRepository::class);
        $manager = new SubscriberManager($repo, $this->validator);
        $subscriber = $manager->create($account, $user, $list, $attributes);

        $this->assertTrue($subscriber instanceof Subscriber);
        $this->assertSame('John', $subscriber->getName());
        $this->assertSame('Foo', $subscriber->getSurname());
        $this->assertSame('john.foo@dev.local', $subscriber->getEmail());
    }

    /**
     * @test
     */
    public function it_parses_correctly_csv_file()
    {
        $repo = $this->createMock(SubscriberRepository::class);
        $manager = new SubscriberManager($repo, $this->validator);
        $user = new User();
        $list = new SubscriberList();
        $account = new Account();

        $subscribers = $manager->fromCsv(
            $account,
            $user,
            $list,
            __DIR__.'/stub/list.csv'
        );

        $this->assertTrue($subscribers instanceof ArrayCollection);
        $this->assertSame(4, $subscribers->count());

        $subscribers->map(function ($item) {
            $this->assertTrue($item instanceof Subscriber);
        });
    }
}
