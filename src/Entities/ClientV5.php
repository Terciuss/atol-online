<?php

declare(strict_types=1);

namespace AtolOnline\Entities;

use AtolOnline\Constraints;
use AtolOnline\Enums\DocumentType;
use AtolOnline\Exceptions\{InvalidEmailException,
    InvalidInnLengthException,
    InvalidPhoneException,
    TooLongClientNameException,
    TooLongEmailException,
    TooLongException};
use AtolOnline\Traits\{HasEmail, HasInn};
use DateTime;
use JetBrains\PhpStorm\Pure;

/**
 * Класс, описывающий покупателя
 *
 */
final class ClientV5 extends Entity
{
    use HasEmail;
    use HasInn;

    protected ?string $birthdate = null;
    protected ?string $citizenship = null;
    protected ?DocumentType $document_code = null;
    protected ?string $document_data = null;
    protected ?string $address = null;

    /**
     * Конструктор объекта покупателя
     *
     * @param string|null $name Наименование (1227)
     * @param string|null $email Телефон (1008)
     * @param string|null $phone Email (1008)
     * @param string|null $inn ИНН (1228)
     * @throws InvalidEmailException
     * @throws InvalidInnLengthException
     * @throws InvalidPhoneException
     * @throws TooLongClientNameException
     * @throws TooLongEmailException
     */
    public function __construct(
        protected ?string $name = null,
        protected ?string $phone = null,
        ?string $email = null,
        ?string $inn = null
    ) {
        !is_null($name) && $this->setName($name);
        !is_null($email) && $this->setEmail($email);
        !is_null($phone) && $this->setPhone($phone);
        !is_null($inn) && $this->setInn($inn);
    }

    /**
     * Возвращает наименование покупателя
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Устанавливает наименование покупателя
     *
     * @param string|null $name
     * @return $this
     * @throws TooLongClientNameException
     */
    public function setName(?string $name): self
    {
        if (is_string($name)) {
            $name = preg_replace('/[\n\r\t]/', '', trim($name));
            if (mb_strlen($name) > Constraints::MAX_LENGTH_CLIENT_NAME) {
                throw new TooLongClientNameException($name);
            }
        }
        $this->name = $name ?: null;
        return $this;
    }

    /**
     * Возвращает установленный телефон
     *
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Устанавливает телефон
     *
     * @param string|null $phone Номер телефона
     * @return $this
     * @throws InvalidPhoneException
     */
    public function setPhone(?string $phone): self
    {
        if (is_string($phone)) {
            $phone = preg_replace('/\D/', '', trim($phone));
            if (preg_match(Constraints::PATTERN_PHONE, $phone) !== 1) {
                throw new InvalidPhoneException($phone);
            }
        }
        $this->phone = empty($phone) ? null : "+$phone";
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBirthdate(): ?string
    {
        return $this->birthdate;
    }

    /**
     * @param string|null $birthdate
     */
    public function setBirthdate(?DateTime $birthdate): static
    {
        $this->birthdate = $birthdate?->format('d.m.Y');
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCitizenship(): ?string
    {
        return $this->citizenship;
    }

    /**
     * @param string|null $citizenship
     */
    public function setCitizenship(?string $citizenship): static
    {
        $this->citizenship = $citizenship;
        return $this;
    }

    /**
     * @return DocumentType|null
     */
    public function getDocumentCode(): ?DocumentType
    {
        return $this->document_code;
    }

    /**
     * @param DocumentType|null $document_code
     */
    public function setDocumentCode(?DocumentType $document_code): static
    {
        $this->document_code = $document_code;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDocumentData(): ?string
    {
        return $this->document_data;
    }

    /**
     * @param string|null $document_data
     */
    public function setDocumentData(?string $document_data): static
    {
        $this->document_data = $document_data;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     */
    public function setAddress(?string $address): static
    {
        if($address && mb_strlen($address) > 256) {
            throw new TooLongException($address);
        }

        $this->address = $address;
        return $this;
    }


    /**
     * @inheritDoc
     */
    #[Pure]
    public function jsonSerialize(): array
    {
        $json = $this->getProperties();

        foreach ($json as $k => $v) {
            if(is_null($v)) {
                unset($json[$k]);
            }
        }

        return $json;
    }
}
