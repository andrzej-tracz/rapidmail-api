<?php

namespace App\Infrastructure\Doctrine\Utils;

use Doctrine\ORM\EntityManagerInterface;

class ClassUtils
{
    /**
     * Check if given class pr object is Entity.
     *
     * @param EntityManagerInterface $em
     * @param $class
     *
     * @return bool
     */
    public static function isEntity(EntityManagerInterface $em, $class)
    {
        if (is_object($class)) {
            $class = self::entityClass($class);
        }

        return !$em->getMetadataFactory()->isTransient($class);
    }

    /**
     * Fetches real Entity class.
     *
     * @param $proxyOrEntity
     *
     * @return string
     */
    public static function entityClass($proxyOrEntity)
    {
        return \Doctrine\Common\Util\ClassUtils::getClass($proxyOrEntity);
    }
}
