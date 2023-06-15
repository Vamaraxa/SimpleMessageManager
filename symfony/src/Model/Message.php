<?php

namespace App\Model;

class Message
{
    private ?string $uuid = null;

    private string $text = '';

    private ?\DateTimeInterface $dateOfCreated = null;

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getDateOfCreated(): ?\DateTimeInterface
    {
        return $this->dateOfCreated;
    }

    public function setDateOfCreated(\DateTimeInterface $dateOfCreated): self
    {
        $this->dateOfCreated = $dateOfCreated;

        return $this;
    }

    public function toArray()
    {
        return [
            'uuid' => $this->uuid,
            'text' => $this->text,
            'dateOfCreated' => $this->dateOfCreated
        ];
    }
}
