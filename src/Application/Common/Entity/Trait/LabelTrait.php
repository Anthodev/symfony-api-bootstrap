<?php

declare(strict_types=1);

namespace App\Application\Common\Entity\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait LabelTrait
{
    #[Assert\NotBlank, Assert\Length(max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private ?string $label = null;

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }
}
