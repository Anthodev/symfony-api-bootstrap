<?php

declare(strict_types=1);

namespace App\Application\Common\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

trait IdTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(name: 'id', type: UlidType::NAME, unique: true)]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private ?Ulid $id = null;

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function setId(Ulid $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function setDefaultId(): static
    {
        $this->id = new Ulid();

        return $this;
    }
}
