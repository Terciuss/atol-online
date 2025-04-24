<?php

declare(strict_types=1);

namespace AtolOnline\Entities;

use AtolOnline\Exceptions\AtolException;
use AtolOnline\Exceptions\TooLongException;

final class MarkCode extends Entity
{
    /**
     * @var string|null Код товара, формат которого не идентифицирован, как один из реквизитов.
     * Максимум 32 символа.
     */
    protected ?string $unknown = null;

    /**
     * @var string|null Код товара в формате EAN-8.
     * Ровно 8 цифр.
     */
    protected ?string $ean8 = null;

    /**
     * @var string|null Код товара в формате EAN-13.
     * Ровно 13 цифр.
     */
    protected ?string $ean13 = null;

    /**
     * @var string|null Код товара в формате ITF-14.
     * Ровно 14 цифр.
     */
    protected ?string $itf14 = null;

    /**
     * @var string|null Код товара в формате GS1, нанесенный на товар, не подлежащий маркировке
     * средствами идентификации.
     * Максимум 38 символов.
     */
    protected ?string $gs10 = null;

    /**
     * @var string|null Код товара в формате GS1, нанесенный на товар, подлежащий маркировке
     * средствами идентификации.
     * Максимум 200 символов.
     */
    protected ?string $gs1m = null;

    /**
     * @var string|null Код товара в формате короткого кода маркировки, нанесенный на товар,
     * подлежащий маркировке средствами идентификации.
     * Максимум 38 символов.
     */
    protected ?string $short = null;

    /**
     * @var string|null Контрольно-идентификационный знак мехового изделия.
     * Ровно 20 символов, должно соответствовать маске СС-СССССССССССССССС
     */
    protected ?string $fur = null;

    /**
     * @var string|null Код товара в формате ЕГАИС-2.0.
     * Ровно 23 символа.
     */
    protected ?string $egais20 = null;

    /**
     * @var string|null Код товара в формате ЕГАИС-3.0.
     * Ровно 14 символов.
     */
    protected ?string $egais30 = null;

    /**
     * @return string|null
     */
    public function getUnknown(): ?string
    {
        return $this->unknown;
    }

    /**
     * @param string|null $unknown
     */
    public function setUnknown(?string $unknown): static
    {
        $this->validateLength($unknown, 32);
        $this->unknown = $unknown;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEan8(): ?string
    {
        return $this->ean8;
    }

    /**
     * @param string|null $ean8
     */
    public function setEan8(?string $ean8): static
    {
        $this->validateLength($ean8, 8, true);
        $this->ean8 = $ean8;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEan13(): ?string
    {
        return $this->ean13;
    }

    /**
     * @param string|null $ean13
     */
    public function setEan13(?string $ean13): static
    {
        $this->validateLength($ean13, 13, true);
        $this->ean13 = $ean13;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getItf14(): ?string
    {
        return $this->itf14;
    }

    /**
     * @param string|null $itf14
     */
    public function setItf14(?string $itf14): static
    {
        $this->validateLength($itf14, 14, true);
        $this->itf14 = $itf14;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGs10(): ?string
    {
        return $this->gs10;
    }

    /**
     * @param string|null $gs10
     */
    public function setGs10(?string $gs10): static
    {
        $this->validateLength($gs10, 38);
        $this->gs10 = $gs10;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGs1m(): ?string
    {
        return $this->gs1m;
    }

    /**
     * @param string|null $gs1m
     */
    public function setGs1m(?string $gs1m): static
    {
        $this->validateLength($gs1m, 200);
        $this->gs1m = $gs1m;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getShort(): ?string
    {
        return $this->short;
    }

    /**
     * @param string|null $short
     */
    public function setShort(?string $short): static
    {
        $this->validateLength($short, 38);
        $this->short = $short;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFur(): ?string
    {
        return $this->fur;
    }

    /**
     * @param string|null $fur
     */
    public function setFur(?string $fur): static
    {
        $this->validateLength($fur, 20);
        if ($fur && !preg_match('/([a-zA-Zа-яА-Я0-9]{2})-([a-zA-Zа-яА-Я0-9]{16})/', $fur)) {
            throw new AtolException("значение fur не соответствует маске");
        }
        $this->fur = $fur;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEgais20(): ?string
    {
        return $this->egais20;
    }

    /**
     * @param string|null $egais20
     */
    public function setEgais20(?string $egais20): static
    {
        $this->validateLength($egais20, 23, true);
        $this->egais20 = $egais20;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEgais30(): ?string
    {
        return $this->egais30;
    }

    /**
     * @param string|null $egais30
     */
    public function setEgais30(?string $egais30): static
    {
        $this->validateLength($egais30, 14, true);
        $this->egais30 = $egais30;
        return $this;
    }

    private function validateLength($value, $length, $strict = false)
    {
        if (!$strict && $value && mb_strlen($value) > $length) {
            throw new TooLongException($value);
        }

        if ($strict && $value && mb_strlen($value) != $length) {
            throw new TooLongException($value, "Значение $value должно равняться $length символов");
        }
    }

    public function jsonSerialize(): array
    {
        return $this->getProperties();
    }
}
