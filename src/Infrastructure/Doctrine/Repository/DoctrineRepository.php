<?php

namespace App\Infrastructure\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;

abstract class DoctrineRepository extends EntityRepository
{
    public function save($entity)
    {
        $this->_em->persist($entity);
        $this->_em->flush();

        return $entity;
    }

    public function remove($entity)
    {
        $this->_em->remove($entity);
        $this->_em->flush();

        return $entity;
    }

    public function merge($entity)
    {
        $this->_em->merge($entity);
        $this->_em->flush();

        return $entity;
    }

    public function refresh($entity)
    {
        $this->_em->refresh($entity);

        return $entity;
    }
}
