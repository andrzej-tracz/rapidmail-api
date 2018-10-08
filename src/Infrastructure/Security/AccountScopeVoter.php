<?php

namespace App\Infrastructure\Security;

use App\Domain\Account\BelongsToAccount;
use App\Domain\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AccountScopeVoter extends AbstractResourceVoter
{
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
        if ($subject && !$subject instanceof BelongsToAccount) {
            return false;
        }

        return parent::supports($attribute, $subject);
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string           $attribute
     * @param BelongsToAccount $subject
     * @param TokenInterface   $token
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

        throw new \RuntimeException('Unhandled action in BelongsToAccountVoter');
    }

    /**
     * @param BelongsToAccount $subject
     * @param User             $user
     *
     * @return bool
     */
    protected function canView(BelongsToAccount $subject, User $user)
    {
        return $subject->getAccount()->getId() === $user->getActiveProfile()->getAccount()->getId();
    }

    /**
     * @param BelongsToAccount $user
     * @param User             $user
     *
     * @return bool
     */
    protected function canCreate(User $user)
    {
        return true;
    }

    /**
     * @param BelongsToAccount $subject
     * @param User             $user
     *
     * @return bool
     */
    protected function canUpdate(BelongsToAccount $subject, User $user)
    {
        return $this->canView($subject, $user);
    }

    /**
     * @param BelongsToAccount $subject
     * @param User             $user
     *
     * @return bool
     */
    protected function canDelete(BelongsToAccount $subject, User $user)
    {
        return $this->canView($subject, $user)
            && $subject->getAccount()->getId() === $user->getActiveProfile()->getAccount()->getId();
    }
}
