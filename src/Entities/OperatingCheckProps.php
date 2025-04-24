<?php

namespace AtolOnline\Entities;

use DateTime;

class OperatingCheckProps extends Entity
{
    /**
     * @var string Идентификатор операции
     */
    protected string $name;
    /**
     * @var string Данные операции
     */
    protected string $value;
    /**
     * @var string Дата и время операции в формате: «dd.mm.yyyy HH:MM:SS»
     */
    protected string $timestamp;

    public function __construct(
        string $name,
        string $value,
        DateTime $timestamp
    )
    {
        $this->setName($name)->setValue($value)->setTimestamp($timestamp);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): static
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * @param DateTime $timestamp
     */
    public function setTimestamp(DateTime $timestamp): static
    {
        $this->timestamp = $timestamp->format('d.m.Y H:i:s');
        return $this;
    }

    public function jsonSerialize(): array
    {
       return  [
           'name' => '',
           'value' => '',
           'timestamp' => ''
       ];
    }
}
