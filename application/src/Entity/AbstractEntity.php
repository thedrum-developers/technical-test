<?php

namespace App\Entity;

/**
 * Class AbstractEntity
 * @package App\Entity
 */
abstract class AbstractEntity implements EntityInterface
{
    /**
     * @return int|null
     */
    abstract public function getId(): ?int;
}