<?php

namespace App\Infrastructure\Project\Security;

use App\Domain\Project\Project;
use App\Domain\User\User;
use App\Infrastructure\Security\AbstractResourceVoter;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProjectVoter extends AbstractResourceVoter
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [
            static::RESOURCE_READ,
            static::RESOURCE_UPDATE,
            static::RESOURCE_CREATE,
            static::RESOURCE_DELETE,
        ])) {
            return false;
        }

        if (is_null($subject) && in_array($attribute, [
            static::RESOURCE_CREATE,
        ])) {
            return true;
        }

        if (!$subject instanceof Project) {
            return false;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute
     * @param Project        $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case static::RESOURCE_CREATE:
                return $this->canCreate($user);

            case static::RESOURCE_READ:
                return $this->canView($subject, $user);

            case static::RESOURCE_UPDATE:
                return $this->canUpdate($subject, $user);

            case static::RESOURCE_DELETE:
                return $this->canDelete($subject, $user);
        }

        throw new \RuntimeException('Unhandled action in ProjectVoter');
    }

    /**
     * @param Project $subject
     * @param User    $user
     *
     * @return bool
     */
    protected function canView(Project $subject, User $user)
    {
        $this->logger->debug("Checking Project: {$subject->getId()} for View permission");

        return $subject->getAccount()->getId() === $user->getActiveProfile()->getAccount()->getId();
    }

    /**
     * @param Project $user
     * @param User    $user
     *
     * @return bool
     */
    protected function canCreate(User $user)
    {
        return true;
    }

    /**
     * @param Project $subject
     * @param User    $user
     *
     * @return bool
     */
    protected function canUpdate(Project $subject, User $user)
    {
        $this->logger->debug("Checking Project: {$subject->getId()} for Update permission");

        return $this->canView($subject, $user);
    }

    /**
     * @param Project $subject
     * @param User    $user
     *
     * @return bool
     */
    protected function canDelete(Project $subject, User $user)
    {
        $this->logger->debug("Checking Project: {$subject->getId()} for Delete permission");

        return $this->canView($subject, $user)
            && $subject->getAccount()->getId() === $user->getActiveProfile()->getAccount()->getId();
    }
}
