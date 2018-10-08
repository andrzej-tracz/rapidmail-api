<?php

namespace App\Application\Handler\User;

use App\Application\Command\User\SendConfirmationEmailCommand;

class SendConfirmationEmailHandler
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param SendConfirmationEmailCommand $command
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function handle(SendConfirmationEmailCommand $command)
    {
        $user = $command->user();

        $message = (new \Swift_Message('Account Confirmation'))
            ->setFrom('send@example.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'emails/user/registration.html.twig',
                    [
                        'user' => $user,
                    ]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }
}
