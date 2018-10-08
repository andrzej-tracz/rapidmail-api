<?php

namespace App\Infrastructure\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class AbstractResourceVoter extends Voter
{
    const RESOURCE_CREATE = 'create';
    const RESOURCE_READ = 'read';
    const RESOURCE_UPDATE = 'update';
    const RESOURCE_DELETE = 'delete';

    /**
     * Map Http verbs into resources action.
     *
     * @var array
     */
    public static $abilities = [
        Request::METHOD_GET => AbstractResourceVoter::RESOURCE_READ,
        Request::METHOD_POST => AbstractResourceVoter::RESOURCE_CREATE,
        Request::METHOD_PUT => AbstractResourceVoter::RESOURCE_UPDATE,
        Request::METHOD_DELETE => AbstractResourceVoter::RESOURCE_DELETE,
    ];

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

        return true;
    }
}
