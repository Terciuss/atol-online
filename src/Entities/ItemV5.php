<?php

/*
 * Copyright (c) 2020-2021 Антон Аксенов (Anthony Axenov)
 *
 * This code is licensed under MIT.
 * Этот код распространяется по лицензии MIT.
 * https://github.com/anthonyaxenov/atol-online/blob/master/LICENSE
 */

declare(strict_types=1);

namespace AtolOnline\Entities;

use AtolOnline\Constraints;
use AtolOnline\Enums\{Measure, PaymentMethod, PaymentObjectV5, VatType};
use AtolOnline\Exceptions\{EmptyItemNameException,
    InvalidDeclarationNumberException,
    InvalidOKSMCodeException,
    NegativeItemExciseException,
    NegativeItemPriceException,
    NegativeItemQuantityException,
    TooHighItemPriceException,
    TooHighItemQuantityException,
    TooHighItemSumException,
    TooLongItemNameException,
    TooLongUserdataException};

/**
 * Предмет расчёта (товар, услуга)
 *
 */
final class ItemV5 extends Entity
{
    protected string $name;

    protected float $price;

    protected float $quantity;

    protected Measure $measure;

//    protected float $sum;

    /**
     * @var PaymentMethod|null Признак способа расчёта (1214)
     */
    protected ?PaymentMethod $payment_method = null;

    /**
     * @var PaymentObjectV5|null Признак предмета расчёта (1212)
     */
    protected ?PaymentObjectV5 $payment_object = null;

    /**
     * @var Vat|null Ставка НДС
     */
    protected ?Vat $vat = null;

    /**
     * @var string|null Дополнительный реквизит (1191)
     */
    protected ?string $user_data = null;

    /**
     * @var float|null Сумма акциза, включенная в стоимость (1229)
     */
    protected ?float $excise = null;

    /**
     * @var string|null Цифровой код страны происхождения товара (1230)
     */
    protected ?string $country_code = null;

    /**
     * @var string|null Номер таможенной декларации (1321)
     */
    protected ?string $declaration_number = null;

    /**
     * @var string|null Реквизит «дробное количество маркированного товара» (тег 1291)
     * включается в состав реквизита «предмет расчета» (тег 1059) только в
     * случае если расчет осуществляется за маркированный товар и значение
     * реквизита «мера количества предмета расчета» (тег 2108) принимает
     * значение равное «0».
     */
    protected ?MarkQuantity $mark_quantity = null;

    /**
     * @var string|null Включается в чек в случае, если предметом расчета является товар,
     * подлежащий обязательной маркировке средством идентификации.
     * Должен принимать значение равное «0».
     */
    protected ?string $mark_processing_mode = null;

    /**
     * @var SectoralCheckProps[]|null Необходимо указывать, если в составе реквизита «предмет расчета»
     * (тег 1059) содержатся сведения о товаре, подлежащем обязательной
     * маркировке средством идентификации и включение указанного
     * реквизита предусмотрено НПА отраслевого регулирования для
     * соответствующей товарной группы.
     * Обязательно при wholesale = true
     */
    protected ?array $sectoral_item_props = null;

    /**
     * @var MarkCode|null Включается в чек в случае, если предметом расчета является товар,
     * подлежащий обязательной маркировке средством идентификации.
     * Обязательно при wholesale = true.
     */
    protected ?MarkCode $mark_code = null;

    /**
     * @var AgentInfo|null Атрибуты агента
     */
    protected ?AgentInfo $agent_info = null;

    /**
     * @var Supplier|null Атрибуты поставшика
     */
    protected ?Supplier $supplier_info = null;

    /**
     * @var bool|null Признак использования ОСУ.
     * Объемно-сортовой учет (ОСУ) — это учет движения товара не по
     * каждой промаркированной единице продукции, а по партии товара с
     * одинаковым атрибутивным составом.
     * При установке в true позволяет указать код товара (mark_code) и общее
     * количество товара в упаковке (quantity) вместо кода маркировки
     * каждого экземпляра товара.
     * При отсутствии параметра в запросе принимает значение false.
     */
    protected ?bool $wholesale = null;

    /**
     * Конструктор
     *
     * @param string $name Наименование (1030)
     * @param float $price Цена в рублях (с учётом скидок и наценок) (1079)
     * @param float $quantity Количество/вес (1023)
     * @param Measure $measure
     * @param PaymentMethod $payment_method
     * @param PaymentObjectV5 $payment_object
     * @throws EmptyItemNameException
     * @throws NegativeItemPriceException
     * @throws NegativeItemQuantityException
     * @throws TooHighItemPriceException
     * @throws TooHighItemQuantityException
     * @throws TooHighItemSumException
     * @throws TooLongItemNameException
     */
    public function __construct(
        string          $name,
        float           $price,
        float           $quantity,
        Measure         $measure,
        PaymentMethod   $payment_method,
        PaymentObjectV5 $payment_object,
        Vat|VatType     $vat,
    )
    {
        $this->setName($name);
        $this->setPrice($price);
        $this->setQuantity($quantity);
        $this->setMeasure($measure);
        $this->setPaymentMethod($payment_method);
        $this->setPaymentObject($payment_object);
        $this->setVat($vat);
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
     * @throws TooLongItemNameException
     * @throws EmptyItemNameException
     */
    public function setName(string $name): self
    {
        if (mb_strlen($name = trim($name)) > Constraints::MAX_LENGTH_ITEM_NAME) {
            throw new TooLongItemNameException($name);
        }
        empty($name) && throw new EmptyItemNameException();
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
     * @param float $price
     * @return $this
     * @throws NegativeItemPriceException
     * @throws TooHighItemPriceException
     * @throws TooHighItemSumException
     */
    public function setPrice(float $price): self
    {
        $price = round($price, 2);
        $price > Constraints::MAX_COUNT_ITEM_PRICE && throw new TooHighItemPriceException($this->getName(), $price);
        $price < 0 && throw new NegativeItemPriceException($this->getName(), $price);
        $this->price = $price;
        $this->getVat()?->setSum($this->getSum());
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
     * @throws TooHighItemSumException
     */
    public function setQuantity(float $quantity): self
    {
        $quantity = round($quantity, 3);
        if ($quantity > Constraints::MAX_COUNT_ITEM_QUANTITY) {
            throw new TooHighItemQuantityException($this->getName(), $quantity);
        }
        $quantity < 0 && throw new NegativeItemQuantityException($this->getName(), $quantity);
        $this->quantity = $quantity;
        $this->getVat()?->setSum($this->getSum());
        return $this;
    }

    /**
     * @return Measure
     */
    public function getMeasure(): Measure
    {
        return $this->measure;
    }

    /**
     * @param Measure $measure
     */
    public function setMeasure(Measure $measure): static
    {
        $this->measure = $measure;
        return $this;
    }


    /**
     * Возвращает стоимость (цена * количество + акциз)
     *
     * @return float
     * @throws TooHighItemSumException
     */
    public function getSum(): float
    {
        $sum = $this->getPrice() * $this->getQuantity() + (float)$this->getExcise();
        if ($sum > Constraints::MAX_COUNT_ITEM_SUM) {
            throw new TooHighItemSumException($this->getName(), $sum);
        }
        return $sum;
    }

    /**
     * Возвращает признак способа оплаты
     *
     * @return PaymentMethod|null
     */
    public function getPaymentMethod(): ?PaymentMethod
    {
        return $this->payment_method;
    }

    /**
     * Устанавливает признак способа оплаты
     *
     * @param PaymentMethod|null $payment_method Признак способа оплаты
     * @return $this
     */
    public function setPaymentMethod(?PaymentMethod $payment_method): self
    {
        $this->payment_method = $payment_method;
        return $this;
    }

    /**
     * Возвращает признак предмета расчёта
     *
     * @return PaymentObjectV5|null
     */
    public function getPaymentObject(): ?PaymentObjectV5
    {
        return $this->payment_object;
    }

    /**
     * Устанавливает признак предмета расчёта
     *
     * @param PaymentObjectV5|null $payment_object Признак предмета расчёта
     * @return $this
     */
    public function setPaymentObject(?PaymentObjectV5 $payment_object): self
    {
        $this->payment_object = $payment_object;
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
     * @param Vat | VatType | null $vat Объект ставки, одно из значений VatTypes или null для удаления ставки
     * @return $this
     * @throws TooHighItemSumException
     */
    public function setVat(Vat|VatType|null $vat): self
    {
        if (is_null($vat)) {
            $this->vat = null;
        } elseif ($vat instanceof Vat) {
            $vat->setSum($this->getSum());
            $this->vat = $vat;
        } else {
            $this->vat = new Vat($vat, $this->getSum());
        }
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
     * @return ItemV5
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
    public function getSupplierInfo(): ?Supplier
    {
        return $this->supplier_info;
    }

    /**
     * Устанавливает поставщика
     *
     * @param Supplier|null $supplier_info
     * @return ItemV5
     */
    public function setSupplierInfo(?Supplier $supplier_info): self
    {
        $this->supplier_info = $supplier_info;
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
     * @throws TooLongUserdataException
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
     * Возвращает установленную сумму акциза
     *
     * @return float|null
     */
    public function getExcise(): ?float
    {
        return $this->excise;
    }

    /**
     * Устанавливает сумму акциза
     *
     * @param float|null $excise
     * @return ItemV5
     * @throws NegativeItemExciseException
     * @throws TooHighItemSumException
     */
    public function setExcise(?float $excise): self
    {
        if ($excise < 0) {
            throw new NegativeItemExciseException($this->getName(), $excise);
        }
        $this->excise = $excise;
        $this->getVat()?->setSum($this->getSum());
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
     * @return ItemV5
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
     * @return ItemV5
     * @throws InvalidDeclarationNumberException
     */
    public function setDeclarationNumber(?string $declaration_number): self
    {
        if (is_string($declaration_number)) {
            $declaration_number = trim($declaration_number);
            $is_short = mb_strlen($declaration_number) < Constraints::MIN_LENGTH_DECLARATION_NUMBER;
            $is_long = mb_strlen($declaration_number) > Constraints::MAX_LENGTH_DECLARATION_NUMBER;
            if ($is_short || $is_long) {
                throw new InvalidDeclarationNumberException($declaration_number);
            }
        }
        $this->declaration_number = $declaration_number;
        return $this;
    }

    /**
     * @return MarkQuantity|null
     */
    public function getMarkQuantity(): ?MarkQuantity
    {
        return $this->mark_quantity;
    }

    /**
     * @param MarkQuantity|null $mark_quantity
     */
    public function setMarkQuantity(?MarkQuantity $mark_quantity): static
    {
        $this->mark_quantity = $mark_quantity;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMarkProcessingMode(): ?string
    {
        return $this->mark_processing_mode;
    }

    /**
     * @param string|null $mark_processing_mode
     */
    public function setMarkProcessingMode(?string $mark_processing_mode): static
    {
        $this->mark_processing_mode = $mark_processing_mode;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getSectoralItemProps(): ?array
    {
        return $this->sectoral_item_props;
    }

    /**
     * @param array|null $sectoral_item_props
     */
    public function setSectoralItemProps(?array $sectoral_item_props): static
    {
        $this->sectoral_item_props = $sectoral_item_props;
        return $this;
    }

    /**
     * @return MarkCode|null
     */
    public function getMarkCode(): ?MarkCode
    {
        return $this->mark_code;
    }

    /**
     * @param MarkCode|null $mark_code
     */
    public function setMarkCode(?MarkCode $mark_code): static
    {
        $this->mark_code = $mark_code;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getWholesale(): ?bool
    {
        return $this->wholesale;
    }

    /**
     * @param bool|null $wholesale
     */
    public function setWholesale(?bool $wholesale): static
    {
        $this->wholesale = $wholesale;
        return $this;
    }

    /**
     * @inheritDoc
     * @throws TooHighItemSumException
     */
    public function jsonSerialize(): array
    {
        $json = $this->getProperties();
        $json['sum'] = $this->getSum();
        return $json;
    }
}
