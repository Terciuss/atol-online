<?php
/*
 * Copyright (c) 2020-2021 Антон Аксенов (Anthony Axenov)
 *
 * This code is licensed under MIT.
 * Этот код распространяется по лицензии MIT.
 * https://github.com/anthonyaxenov/atol-online/blob/master/LICENSE
 */

declare(strict_types = 1);

namespace AtolOnline\Entities;

use AtolOnline\{
    Constants\Constraints,
    Enums\PaymentMethods,
    Enums\PaymentObjects,
    Enums\VatTypes,
    Exceptions\EmptyItemNameException,
    Exceptions\InvalidDeclarationNumberException,
    Exceptions\InvalidEnumValueException,
    Exceptions\InvalidOKSMCodeException,
    Exceptions\NegativeItemPriceException,
    Exceptions\NegativeItemQuantityException,
    Exceptions\TooHighItemQuantityException,
    Exceptions\TooHighPriceException,
    Exceptions\TooHighSumException,
    Exceptions\TooLongItemNameException,
    Exceptions\TooLongMeasurementUnitException,
    Exceptions\TooLongUserdataException,
    Exceptions\TooManyException};

/**
 * Предмет расчёта (товар, услуга)
 *
 * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 21-30
 */
class Item extends Entity
{
    /**
     * @var string Наименование (1030)
     */
    protected string $name;

    /**
     * @var float Цена в рублях (с учётом скидок и наценок) (1079)
     */
    protected float $price;

    /**
     * @var float Количество/вес (1023)
     */
    protected float $quantity;

    /**
     * @var string|null Единица измерения (1197)
     */
    protected ?string $measurement_unit = null;

    /**
     * @var string|null Признак способа расчёта (1214)
     */
    protected ?string $payment_method = null;

    /**
     * @var string|null Признак предмета расчёта (1212)
     */
    protected ?string $payment_object = null;

    /**
     * @var string|null Номер таможенной декларации (1321)
     */
    protected ?string $declaration_number = null;

    /**
     * @var Vat|null Ставка НДС
     */
    protected ?Vat $vat = null;

    /**
     * @var AgentInfo|null Атрибуты агента
     */
    protected ?AgentInfo $agent_info = null;

    /**
     * @var Supplier|null Атрибуты поставшика
     */
    protected ?Supplier $supplier = null;

    /**
     * @var string|null Дополнительный реквизит (1191)
     */
    protected ?string $user_data = null;

    /**
     * @var string|null Цифровой код страны происхождения товара (1230)
     */
    protected ?string $country_code = null;

    /**
     * Конструктор
     *
     * @param string|null $name Наименование
     * @param float|null $price Цена за одну единицу
     * @param float|null $quantity Количество
     * @throws TooLongItemNameException
     * @throws TooHighPriceException
     * @throws TooManyException
     * @throws NegativeItemPriceException
     * @throws EmptyItemNameException
     * @throws NegativeItemQuantityException
     */
    public function __construct(
        string $name = null,
        float $price = null,
        float $quantity = null,
    ) {
        !is_null($name) && $this->setName($name);
        !is_null($price) && $this->setPrice($price);
        !is_null($quantity) && $this->setQuantity($quantity);
    }

    /**
     * Возвращает наименование
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Устаналивает наименование
     *
     * @param string $name Наименование
     * @return $this
     * @throws TooLongItemNameException Слишком длинное имя/наименование
     * @throws EmptyItemNameException
     */
    public function setName(string $name): self
    {
        $name = trim($name);
        if (mb_strlen($name) > Constraints::MAX_LENGTH_ITEM_NAME) {
            throw new TooLongItemNameException($name);
        }
        if (empty($name)) {
            throw new EmptyItemNameException();
        }
        $this->name = $name;
        return $this;
    }

    /**
     * Возвращает цену в рублях
     *
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Устанавливает цену в рублях
     *
     * @param float $rubles
     * @return $this
     * @throws NegativeItemPriceException
     * @throws TooHighPriceException
     */
    public function setPrice(float $rubles): self
    {
        if ($rubles > Constraints::MAX_COUNT_ITEM_PRICE) {
            throw new TooHighPriceException($this->getName(), $rubles);
        }
        if ($rubles < 0) {
            throw new NegativeItemPriceException($this->getName(), $rubles);
        }
        $this->price = $rubles;
        //$this->calcSum();
        return $this;
    }

    /**
     * Возвращает количество
     *
     * @return float
     */
    public function getQuantity(): float
    {
        return $this->quantity;
    }

    /**
     * Устанавливает количество
     *
     * @param float $quantity Количество
     * @return $this
     * @throws TooHighItemQuantityException
     * @throws NegativeItemQuantityException
     */
    public function setQuantity(float $quantity): self
    {
        $quantity = round($quantity, 3);
        if ($quantity > Constraints::MAX_COUNT_ITEM_QUANTITY) {
            throw new TooHighItemQuantityException($this->getName(), $quantity);
        }
        if ($quantity < 0) {
            throw new NegativeItemQuantityException($this->getName(), $quantity);
        }
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Возвращает стоимость
     *
     * @return float
     * @throws TooHighSumException
     */
    public function getSum(): float
    {
        $sum = $this->price * $this->quantity;
        if ($sum > Constraints::MAX_COUNT_ITEM_PRICE) {
            throw new TooHighSumException($this->getName(), $sum);
        }
        return $sum;
    }

    /**
     * Возвращает заданную единицу измерения количества
     *
     * @return string|null
     */
    public function getMeasurementUnit(): ?string
    {
        return $this->measurement_unit;
    }

    /**
     * Устанавливает единицу измерения количества
     *
     * @param string|null $measurement_unit
     * @return $this
     * @throws TooLongMeasurementUnitException
     */
    public function setMeasurementUnit(?string $measurement_unit): self
    {
        $measurement_unit = trim((string)$measurement_unit);
        if (mb_strlen($measurement_unit) > Constraints::MAX_LENGTH_MEASUREMENT_UNIT) {
            throw new TooLongMeasurementUnitException($measurement_unit);
        }
        $this->measurement_unit = $measurement_unit ?: null;
        return $this;
    }

    /**
     * Возвращает признак способа оплаты
     *
     * @return string|null
     */
    public function getPaymentMethod(): ?string
    {
        return $this->payment_method;
    }

    /**
     * Устанавливает признак способа оплаты
     *
     * @param string|null $payment_method Признак способа оплаты
     * @return $this
     * @throws InvalidEnumValueException
     */
    public function setPaymentMethod(?string $payment_method): self
    {
        $payment_method = trim((string)$payment_method);
        PaymentMethods::isValid($payment_method);
        $this->payment_method = $payment_method ?: null;
        return $this;
    }

    /**
     * Возвращает признак предмета расчёта
     *
     * @return string|null
     */
    public function getPaymentObject(): ?string
    {
        return $this->payment_object;
    }

    /**
     * Устанавливает признак предмета расчёта
     *
     * @param string|null $payment_object Признак предмета расчёта
     * @return $this
     * @throws InvalidEnumValueException
     */
    public function setPaymentObject(?string $payment_object): self
    {
        $payment_object = trim((string)$payment_object);
        PaymentObjects::isValid($payment_object);
        $this->payment_object = $payment_object ?: null;
        return $this;
    }

    /**
     * Возвращает ставку НДС
     *
     * @return Vat|null
     */
    public function getVat(): ?Vat
    {
        return $this->vat;
    }

    /**
     * Устанавливает ставку НДС
     *
     * @param Vat|string|null $vat Объект ставки, одно из значений VatTypes или null для удаления ставки
     * @return $this
     * @throws TooHighSumException
     */
    public function setVat(Vat|string|null $vat): self
    {
        if (is_string($vat)) {
            $vat = trim($vat);
            VatTypes::isValid($vat) && $vat = new Vat($vat, $this->getSum());
        } elseif ($vat instanceof Vat) {
            $vat->setSum($this->getSum());
        }
        $this->vat = $vat ?: null;
        return $this;
    }

    /**
     * Возвращает установленный объект атрибутов агента
     *
     * @return AgentInfo|null
     */
    public function getAgentInfo(): ?AgentInfo
    {
        return $this->agent_info;
    }

    /**
     * Устанавливает атрибуты агента
     *
     * @param AgentInfo|null $agent_info
     * @return Item
     */
    public function setAgentInfo(?AgentInfo $agent_info): self
    {
        $this->agent_info = $agent_info;
        return $this;
    }

    /**
     * Возвращает установленного поставщика
     *
     * @return Supplier|null
     */
    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    /**
     * Устанавливает поставщика
     *
     * @param Supplier|null $supplier
     * @return Item
     */
    public function setSupplier(?Supplier $supplier): self
    {
        $this->supplier = $supplier;
        return $this;
    }

    /**
     * Возвращает дополнительный реквизит
     *
     * @return string|null
     */
    public function getUserData(): ?string
    {
        return $this->user_data;
    }

    /**
     * Устанавливает дополнительный реквизит
     *
     * @param string|null $user_data Дополнительный реквизит
     * @return $this
     * @throws TooLongUserdataException Слишком длинный дополнительный реквизит
     */
    public function setUserData(?string $user_data): self
    {
        $user_data = trim((string)$user_data);
        if (mb_strlen($user_data) > Constraints::MAX_LENGTH_USER_DATA) {
            throw new TooLongUserdataException($user_data);
        }
        $this->user_data = $user_data ?: null;
        return $this;
    }

    /**
     * Возвращает установленный код страны происхождения товара
     *
     * @return string|null
     * @see https://ru.wikipedia.org/wiki/Общероссийский_классификатор_стран_мира
     * @see https://classifikators.ru/oksm
     */
    public function getCountryCode(): ?string
    {
        return $this->country_code;
    }

    /**
     * Устанавливает код страны происхождения товара
     *
     * @param string|null $country_code
     * @return Item
     * @throws InvalidOKSMCodeException
     * @see https://classifikators.ru/oksm
     * @see https://ru.wikipedia.org/wiki/Общероссийский_классификатор_стран_мира
     */
    public function setCountryCode(?string $country_code): self
    {
        $country_code = trim((string)$country_code);
        if (preg_match(Constraints::PATTERN_OKSM_CODE, $country_code) != 1) {
            throw new InvalidOKSMCodeException($country_code);
        }
        $this->country_code = $country_code ?: null;
        return $this;
    }

    /**
     * Возвращает установленный код таможенной декларации
     *
     * @return string|null
     */
    public function getDeclarationNumber(): ?string
    {
        return $this->declaration_number;
    }

    /**
     * Устанавливает код таможенной декларации
     *
     * @param string|null $declaration_number
     * @return Item
     * @throws InvalidDeclarationNumberException
     */
    public function setDeclarationNumber(?string $declaration_number): self
    {
        if (is_string($declaration_number)) {
            $declaration_number = trim($declaration_number);
            if (
                mb_strlen($declaration_number) < Constraints::MIN_LENGTH_DECLARATION_NUMBER ||
                mb_strlen($declaration_number) > Constraints::MAX_LENGTH_DECLARATION_NUMBER
            ) {
                throw new InvalidDeclarationNumberException($declaration_number);
            }
        }
        $this->declaration_number = $declaration_number;
        return $this;
    }

    /**
     * Расчитывает стоимость и размер НДС на неё
     *
     * @return float
     * @throws TooHighPriceException Слишком большая сумма
     */
    //public function calcSum(): float
    //{
    //    $sum = $this->quantity * $this->price;
    //    if (rubles($sum) > 42949672.95) {
    //        throw new TooHighPriceException($sum, 42949672.95);
    //    }
    //    $this->sum = $sum;
    //    if ($this->vat) {
    //        $this->vat->setSum(rubles($sum));
    //    }
    //    return $this->getSum();
    //}

    /**
     * @inheritDoc
     * @throws TooHighSumException
     */
    public function jsonSerialize(): array
    {
        $json = [
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'quantity' => $this->getQuantity(),
            'sum' => $this->getSum(),
        ];
        $this->getMeasurementUnit() && $json['measurement_unit'] = $this->getMeasurementUnit();
        $this->getPaymentMethod() && $json['payment_method'] = $this->getPaymentMethod();
        $this->getPaymentObject() && $json['payment_object'] = $this->getPaymentObject();
        $this->getDeclarationNumber() && $json['declaration_number'] = $this->getDeclarationNumber();
        $this->getVat()?->jsonSerialize() && $json['vat'] = $this->getVat()->jsonSerialize();
        $this->getAgentInfo()?->jsonSerialize() && $json['agent_info'] = $this->getAgentInfo()->jsonSerialize();
        $this->getSupplier()?->jsonSerialize() && $json['supplier_info'] = $this->getSupplier()->jsonSerialize();
        $this->getUserData() && $json['user_data'] = $this->getUserData();
        //TODO excise
        $this->getCountryCode() && $json['country_code'] = $this->getCountryCode();
        //TODO nomenclature_code
        return $json;
    }
}
