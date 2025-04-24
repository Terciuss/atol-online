<?php

/*
 * Copyright (c) 2020-2021 Антон Аксенов (Anthony Axenov)
 *
 * This code is licensed under MIT.
 * Этот код распространяется по лицензии MIT.
 * https://github.com/anthonyaxenov/atol-online/blob/master/LICENSE
 */

namespace AtolOnline\Entities;

use AtolOnline\{Api\AtolResponse,
    Api\FiscalizerV5,
    Collections\ItemsV5,
    Collections\Payments,
    Collections\Vats,
    Constraints};
use AtolOnline\Exceptions\{AuthFailedException,
    EmptyItemsException,
    EmptyLoginException,
    EmptyPasswordException,
    InvalidEntityInCollectionException,
    InvalidInnLengthException,
    InvalidPaymentAddressException,
    TooLongCashierException,
    TooLongPaymentAddressException};
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Класс, описывающий документ коррекции
 *
 */
final class CorrectionV5 extends Entity
{
    /**
     * Тип документа
     */
    public const DOC_TYPE = 'correction';

    /**
     * @var ClientV5|null сведения о покупателе (клиенте)
     */
    protected ?ClientV5 $client = null;

    protected CompanyV5 $company;

    protected CorrectionInfo $correction_info;

    protected ItemsV5 $items;

    protected Payments $payments;

    protected ?Vats $vats = null;

    protected ?string $cashier = null;

    protected ?string $cashier_inn = null;

    protected ?string $additional_check_props = null;

    protected float $total = 0;

    protected ?AdditionalUserProps $additional_user_props = null;

    protected ?OperatingCheckProps $operating_check_props = null;

    protected ?array $sectoral_check_props = null;

    protected ?string $device_number = null;

    /**
     * Конструктор
     *
     * @param CompanyV5 $company Продавец
     * @param CorrectionInfo $correction_info Данные коррекции
     * @param ItemsV5 $items Коллекция товаров
     * @param Payments $payments Коллекция оплат
     * @throws InvalidEntityInCollectionException
     * @throws Exception
     */
    public function __construct(
        CompanyV5      $company,
        CorrectionInfo $correction_info,
        ItemsV5        $items,
        Payments       $payments,
    )
    {
        $this->setCompany($company)
            ->setCorrectionInfo($correction_info)
            ->setItems($items)
            ->setPayments($payments);
    }

    /**
     * Возвращает установленного продавца
     *
     * @return CompanyV5
     */
    public function getCompany(): CompanyV5
    {
        return $this->company;
    }

    /**
     * Устанаваливает продавца
     *
     * @param CompanyV5 $company
     * @return $this
     */
    public function setCompany(CompanyV5 $company): self
    {
        $this->company = $company;
        return $this;
    }

    /**
     * Возвращает установленного кассира
     *
     * @return string|null
     */
    public function getCashier(): ?string
    {
        return $this->cashier;
    }

    /**
     * Устанаваливает кассира
     *
     * @param string|null $cashier
     * @return $this
     * @throws TooLongCashierException
     */
    public function setCashier(?string $cashier): self
    {
        if (is_string($cashier)) {
            $cashier = trim($cashier);
            if (mb_strlen($cashier) > Constraints::MAX_LENGTH_CASHIER_NAME) {
                throw new TooLongCashierException($cashier);
            }
        }
        $this->cashier = $cashier ?: null;
        return $this;
    }

    /**
     * Возвращает установленные данные коррекции
     *
     * @return CorrectionInfo
     */
    public function getCorrectionInfo(): CorrectionInfo
    {
        return $this->correction_info;
    }

    /**
     * Устанавливает данные коррекции
     *
     * @param CorrectionInfo $correction_info
     * @return Correction
     */
    public function setCorrectionInfo(CorrectionInfo $correction_info): CorrectionV5
    {
        $this->correction_info = $correction_info;
        return $this;
    }

    /**
     * Возвращает установленную коллекцию оплат
     *
     * @return Payments
     */
    public function getPayments(): Payments
    {
        return $this->payments;
    }

    /**
     * @return ClientV5|null
     */
    public function getClient(): ?ClientV5
    {
        return $this->client;
    }

    /**
     * @param ClientV5|null $client
     */
    public function setClient(?ClientV5 $client): static
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return ItemsV5
     */
    public function getItems(): ItemsV5
    {
        return $this->items;
    }

    /**
     * Устанаваливает коллекцию предметов расчёта
     *
     * @param ItemsV5 $items
     * @return $this
     * @throws InvalidEntityInCollectionException
     * @throws Exception
     * @throws EmptyItemsException
     */
    public function setItems(ItemsV5 $items): self
    {
        $items->checkCount();
        $items->checkItemsClasses();
        $this->items = $items;
        $this->getItems()->each(fn($item) => $this->total += $item->getSum());
        $this->total = round($this->total, 2);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCashierInn(): ?string
    {
        return $this->cashier_inn;
    }

    /**
     * @param string|null $cashier_inn
     */
    public function setCashierInn(?string $cashier_inn): static
    {
        $this->cashier_inn = $cashier_inn;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAdditionalCheckProps(): ?string
    {
        return $this->additional_check_props;
    }

    /**
     * @param string|null $additional_check_props
     */
    public function setAdditionalCheckProps(?string $additional_check_props): static
    {
        $this->additional_check_props = $additional_check_props;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * @param float $total
     */
    public function setTotal(float $total): static
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return AdditionalUserProps|null
     */
    public function getAdditionalUserProps(): ?AdditionalUserProps
    {
        return $this->additional_user_props;
    }

    /**
     * @param AdditionalUserProps|null $additional_user_props
     */
    public function setAdditionalUserProps(?AdditionalUserProps $additional_user_props): static
    {
        $this->additional_user_props = $additional_user_props;
        return $this;
    }

    /**
     * @return OperatingCheckProps|null
     */
    public function getOperatingCheckProps(): ?OperatingCheckProps
    {
        return $this->operating_check_props;
    }

    /**
     * @param OperatingCheckProps|null $operating_check_props
     */
    public function setOperatingCheckProps(?OperatingCheckProps $operating_check_props): static
    {
        $this->operating_check_props = $operating_check_props;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getSectoralCheckProps(): ?array
    {
        return $this->sectoral_check_props;
    }

    /**
     * @param array|null $sectoral_check_props
     */
    public function setSectoralCheckProps(?array $sectoral_check_props): static
    {
        $this->sectoral_check_props = $sectoral_check_props;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDeviceNumber(): ?string
    {
        return $this->device_number;
    }

    /**
     * @param string|null $device_number
     */
    public function setDeviceNumber(?string $device_number): static
    {
        $this->device_number = $device_number;
        return $this;
    }


    /**
     * Устанаваливает коллекцию оплат
     *
     * @param Payments $payments
     * @return $this
     * @throws InvalidEntityInCollectionException
     */
    public function setPayments(Payments $payments): self
    {
        $payments->checkCount()->checkItemsClasses();
        $this->payments = $payments;
        return $this;
    }

    /**
     * Возвращает установленную коллекцию ставок НДС
     *
     * @return Vats|null
     */
    public function getVats(): ?Vats
    {
        return $this->vats;
    }

    /**
     * Устанаваливает коллекцию ставок НДС
     *
     * @param Vats $vats
     * @return $this
     * @throws Exception
     */
    public function setVats(Vats $vats): self
    {
        $vats->checkCount()->checkItemsClasses();
        $this->vats = $vats;
        return $this;
    }

    /**
     * Регистрирует коррекцию прихода по текущему документу
     *
     * @param FiscalizerV5 $fiscalizer Объект фискализатора
     * @param string|null $external_id Уникальный код документа (если не указан, то будет создан новый UUID)
     * @return AtolResponse|null
     * @throws AuthFailedException
     * @throws EmptyLoginException
     * @throws EmptyPasswordException
     * @throws GuzzleException
     * @throws InvalidEntityInCollectionException
     * @throws InvalidInnLengthException
     * @throws InvalidPaymentAddressException
     * @throws TooLongPaymentAddressException
     */
    public function sellCorrect(FiscalizerV5 $fiscalizer, ?string $external_id = null): ?AtolResponse
    {
        return $fiscalizer->sellCorrect($this, $external_id);
    }

    /**
     * Регистрирует коррекцию расхода по текущему документу
     *
     * @param FiscalizerV5 $fiscalizer Объект фискализатора
     * @param string|null $external_id Уникальный код документа (если не указан, то будет создан новый UUID)
     * @return AtolResponse|null
     * @throws AuthFailedException
     * @throws EmptyLoginException
     * @throws EmptyPasswordException
     * @throws GuzzleException
     * @throws InvalidEntityInCollectionException
     * @throws InvalidInnLengthException
     * @throws InvalidPaymentAddressException
     * @throws TooLongPaymentAddressException
     */
    public function buyCorrect(FiscalizerV5 $fiscalizer, ?string $external_id = null): ?AtolResponse
    {
        return $fiscalizer->buyCorrect($this, $external_id);
    }

    /**
     * @inheritDoc
     * @throws InvalidEntityInCollectionException
     */
    #[ArrayShape([
        'company' => '\AtolOnline\Entities\Company',
        'correction_info' => '\AtolOnline\Entities\CorrectionInfo',
        'payments' => 'array',
        'vats' => '\AtolOnline\Collections\Vats|null',
        'cashier' => 'null|string',
    ])]
    public function jsonSerialize(): array
    {
        $json = $this->getProperties();
        return $json;
    }
}
