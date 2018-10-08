<?php

namespace App\Test\Application\Command\Account;

use App\Application\Command\Account\CreateAccountCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateAccountCommandTest extends KernelTestCase
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    public function testCreateAccountCommand()
    {
        $command = new CreateAccountCommand(
            'Account name',
            'admin@google.com',
            'secret'
        );

        $this->assertEquals($command->name(), 'Account name');
    }

    public function testInvalidCreateAccountCommand()
    {
        $errors = $this->validator->validate(new CreateAccountCommand('S', 'not-email', 'pss'));

        $this->assertCount(2, $errors);
    }
}
