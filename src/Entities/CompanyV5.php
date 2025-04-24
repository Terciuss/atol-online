<?php

declare(strict_types=1);

namespace AtolOnline\Entities;

use AtolOnline\{Constraints, Enums\SnoType, Traits\HasEmail, Traits\HasInn};
use AtolOnline\Exceptions\{InvalidEmailException,
    InvalidInnLengthException,
    InvalidPaymentAddressException,
    TooLongEmailException,
    TooLongException,
    TooLongPaymentAddressException};
use JetBrains\PhpStorm\ArrayShape;

/**
 * Класс, описывающий сущность компании-продавца
 *
 */
final class CompanyV5 extends Entity
{
    use HasEmail;
    use HasInn;

    protected ?string $location = null;

    /**
     * Конструктор
     *
     * @param string $inn ИНН (1018)
     * @param SnoType $sno Система налогообложения продавца (1055)
     * @param string $payment_address Место расчётов (адрес интернет-магазина) (1187)
     * @param string $email Почта (1117)
     * @throws InvalidEmailException
     * @throws InvalidInnLengthException
     * @throws InvalidPaymentAddressException
     * @throws TooLongEmailException
     * @throws TooLongPaymentAddressException
     */
    public function __construct(
        string $inn,
        protected SnoType $sno,
        protected string $payment_address,
        string $email,
    ) {
        $this->setInn($inn)
            ->setPaymentAddress($payment_address)
            ->setEmail($email);
    }

    /**
     * Возвращает установленный тип налогообложения
     *
     * @return SnoType
     */
    public function getSno(): SnoType
    {
        return $this->sno;
    }

    /**
     * Устанавливает тип налогообложения
     *
     * @param SnoType $sno
     * @return $this
     */
    public function setSno(SnoType $sno): self
    {
        $this->sno = $sno;
        return $this;
    }

    /**
     * Возвращает установленный адрес места расчётов
     *
     * @return string
     */
    public function getPaymentAddress(): string
    {
        return $this->payment_address;
    }

    /**
     * Устанавливает адрес места расчётов
     *
     * @param string $payment_address
     * @return $this
     * @throws TooLongPaymentAddressException
     * @throws InvalidPaymentAddressException
     */
    public function setPaymentAddress(string $payment_address): self
    {
        $payment_address = trim($payment_address);
        if (empty($payment_address)) {
            throw new InvalidPaymentAddressException();
        } elseif (mb_strlen($payment_address) > Constraints::MAX_LENGTH_PAYMENT_ADDRESS) {
            throw new TooLongPaymentAddressException($payment_address);
        }
        $this->payment_address = $payment_address;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param string|null $location
     */
    public function setLocation(?string $location): static
    {
        if($location && (mb_strlen($location) < 1 || mb_strlen($location) > 256)) {
            throw new TooLongException('Длинна должна быть от 1 до 256');
        }

        $this->location = $location;
        return $this;
    }



    /**
     * @inheritDoc
     */
    #[ArrayShape([
        'sno' => 'string',
        'email' => 'string',
        'inn' => 'string',
        'payment_address' => 'string',
    ])]
    public function jsonSerialize(): array
    {
        return [
            'inn' => $this->getInn(),
            'sno' => $this->getSno(),
            'payment_address' => $this->getPaymentAddress(),
            'email' => $this->getEmail(),
        ];
    }
}
