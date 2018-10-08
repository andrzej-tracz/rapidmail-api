<?php

namespace App\Infrastructure\Queue;

use App\Infrastructure\Doctrine\Utils\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionProperty;

class CommandRefresher
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @param ShouldQueue $command
     *
     * @return ShouldQueue
     *
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \ReflectionException
     */
    public function refreshCommand(ShouldQueue $command): ShouldQueue
    {
        $reflect = new ReflectionClass($command);
        $props = $reflect->getProperties(
            ReflectionProperty::IS_PUBLIC
            | ReflectionProperty::IS_PROTECTED
            | ReflectionProperty::IS_PRIVATE
        );

        foreach ($props as $prop) {
            $prop->setAccessible(true);
            $value = $prop->getValue($command);
            $this->logger->info("Refreshing property {$prop->getName()}.");

            switch (true) {
                case ClassUtils::isEntity($this->em, $value):
                    $this->logger->info('Property is Entity.');
                    $prop->setValue($command, $this->refreshEntity($value));
                    break;
                default:
                    $this->logger->info('Property is NOT Entity.');
                    break;
            }
        }

        return $command;
    }

    /**
     * @param $entity
     *
     * @return null|object
     *
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \ReflectionException
     * @throws EntityNotFoundException
     */
    protected function refreshEntity($entity)
    {
        $class = ClassUtils::entityClass($entity);
        $meta = $this->em->getClassMetadata($class);
        $identifier = $meta->getSingleIdentifierFieldName();

        $reflect = new ReflectionClass($entity);
        $id = $reflect->getProperty($identifier);
        $id->setAccessible(true);
        $value = $id->getValue($entity);

        if (!$value) {
            throw new \RuntimeException('Missing entity id');
        }

        $this->logger->info("Refreshing Entity {$class} with id #{$value}.");
        $entity = $this->em->getRepository($class)->find($value);

        if (!$entity) {
            throw EntityNotFoundException::fromClassNameAndIdentifier($class, [$id]);
        }

        return $entity;
    }
}
