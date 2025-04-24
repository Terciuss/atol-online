<?php

namespace AtolOnline\Entities;

use AtolOnline\Enums\IdentifierFOIV;
use DateTime;

class SectoralCheckProps extends Entity
{
    /**
     * @var IdentifierFOIV Идентификатор ФОИВ
     */
    protected IdentifierFOIV $federal_id;
    /**
     * @var string Дата нормативного акта федерального органа исполнительной власти,
    регламентирующего порядок заполнения реквизита «значение отраслевого
    реквизита» (тег 1265)
     */
    protected string $date;
    /**
     * @var string Номер нормативного акта федерального органа исполнительной власти,
    регламентирующего порядок заполнения реквизита «значение
    отраслевого реквизита» (тег 1265)
     */
    protected string $number;
    /**
     * @var string Состав значений, определенных нормативного актом федерального органа
    исполнительной власти
     */
    protected string $value;

    public function __construct(
        IdentifierFOIV $federal_id,
        DateTime $date,
        string $number,
        string $value
    )
    {
        $this->setFederalId($federal_id)->setDate($date)->setNumber($number)->setValue($value);
    }

    /**
     * @return IdentifierFOIV
     */
    public function getFederalId(): IdentifierFOIV
    {
        return $this->federal_id;
    }

    /**
     * @param IdentifierFOIV $federal_id
     */
    public function setFederalId(IdentifierFOIV $federal_id): static
    {
        $this->federal_id = $federal_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): static
    {
        $this->date = $date->format('d.m.Y');
        return $this;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber(string $number): static
    {
        $this->number = $number;
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


    public function jsonSerialize(): array
    {
        return  [
            'name' => '',
            'value' => '',
            'timestamp' => ''
        ];
    }
}
