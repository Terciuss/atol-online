<?php

declare(strict_types=1);

namespace AtolOnline\Entities;

use AtolOnline\Api\AtolResponse;
use AtolOnline\Api\FiscalizerV5;
use AtolOnline\Collections\ItemsV5;
use AtolOnline\Collections\Payments;
use AtolOnline\Collections\Vats;
use AtolOnline\Constraints;
use AtolOnline\Exceptions\AuthFailedException;
use AtolOnline\Exceptions\EmptyItemsException;
use AtolOnline\Exceptions\EmptyLoginException;
use AtolOnline\Exceptions\EmptyPasswordException;
use AtolOnline\Exceptions\InnCashierException;
use AtolOnline\Exceptions\InvalidEntityInCollectionException;
use AtolOnline\Exceptions\InvalidInnLengthException;
use AtolOnline\Exceptions\InvalidPaymentAddressException;
use AtolOnline\Exceptions\SectoralCheckPropException;
use AtolOnline\Exceptions\TooLongAddCheckPropException;
use AtolOnline\Exceptions\TooLongCashierException;
use AtolOnline\Exceptions\TooLongPaymentAddressException;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Класс, описывающий документ прихода, расхода, возврата прихода, возврата расхода
 *
 */
final class ReceiptV5 extends Entity
{
    /**
     * Тип документа
     */
    public const DOC_TYPE = 'receipt';

    /**
     * @var ClientV5 Покупатель
     */
    protected ClientV5 $client;

    /**
     * @var CompanyV5 Продавец
     */
    protected CompanyV5 $company;

    /**
     * @var ItemsV5 Коллекция предметов расчёта
     */
    protected ItemsV5 $items;

    /**
     * @var Payments Коллекция оплат
     */
    protected Payments $payments;

    /**
     * @var Vats|null Коллекция ставок НДС
     */
    protected ?Vats $vats = null;

    /**
     * @var string|null ФИО кассира
     */
    protected ?string $cashier = null;

    /**
     * @var string|null ИНН кассира
     */
    protected ?string $cashier_inn = null;

    /**
     * @var string|null Дополнительный реквизит
     */
    protected ?string $additional_check_props = null;

    /**
     * @var float Итоговая сумма чека
     */
    protected float $total = 0;

    /**
     * @var AdditionalUserProps|null Дополнительный реквизит пользователя
     */
    protected ?AdditionalUserProps $additional_user_props = null;

    /**
     * @var OperatingCheckProps|null Условия применения и значение реквизита «операционный реквизит
     * чека» (тег 1270) определяются ФНС России
     */
    protected ?OperatingCheckProps $operating_check_props = null;

    /**
     * @var SectoralCheckProps[]|null Дополнительный реквизит пользователя
     */
    protected ?array $sectoral_check_props = null;

    /**
     * @var string|null Заводской номер автоматического устройства для расчетов
     */
    protected ?string $device_number = null;

    /**
     * Конструктор
     *
     * @param ClientV5 $client
     * @param CompanyV5 $company
     * @param ItemsV5 $items
     * @param Payments $payments
     * @throws EmptyItemsException
     * @throws InvalidEntityInCollectionException
     */
    public function __construct(ClientV5 $client, CompanyV5 $company, ItemsV5 $items, Payments $payments)
    {
        $this->setClient($client)->setCompany($company)->setItems($items)->setPayments($payments);
    }

    /**
     * Возвращает установленного покупателя
     *
     * @return ClientV5
     */
    public function getClient(): ClientV5
    {
        return $this->client;
    }

    /**
     * Устанаваливает покупателя
     *
     * @param ClientV5 $client
     * @return $this
     */
    public function setClient(ClientV5 $client): self
    {
        $this->client = $client;
        return $this;
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
     * Возвращает установленную коллекцию предметов расчёта
     *
     * @return ItemsV5
     */
    public function getItems(): ItemsV5
    {
        return $this->items ?? new ItemsV5();
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
     * Возвращает установленную коллекцию оплат
     *
     * @return Payments
     */
    public function getPayments(): Payments
    {
        return $this->payments;
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
        $payments->checkCount();
        $payments->checkItemsClasses();
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
        return $this->vats ?? new Vats();
    }

    /**
     * Устанаваливает коллекцию ставок НДС
     *
     * @param Vats|null $vats
     * @return $this
     * @throws Exception
     */
    public function setVats(?Vats $vats): self
    {
        $vats->checkCount();
        $vats->checkItemsClasses();
        $this->vats = $vats;
        /** @var Vat $vat */
        $this->getVats()->each(fn($vat) => $vat->setSum($this->getTotal()));
        return $this;
    }

    /**
     * Возвращает полную сумму чека
     *
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
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

    public function getCashierInn(): ?string
    {
        return $this->cashier_inn;
    }

    /**
     * Устанаваливает инн кассира
     *
     * @param string|null $cashier_inn
     * @return $this
     * @throws TooLongCashierException
     */
    public function setCashierInn(?string $cashier_inn): self
    {
        if (is_string($cashier_inn)) {
            $cashier_inn = trim($cashier_inn);
            if (mb_strlen($cashier_inn) != Constraints::LENGTH_CASHIER_INN) {
                throw new InnCashierException($cashier_inn);
            }
        }
        $this->cashier = $cashier_inn ?: null;
        return $this;
    }

    public function getDeviceNumber(): ?string
    {
        return $this->device_number;
    }

    public function setDeviceNumber(?string $device_number): self
    {
        $this->cashier = $device_number ? trim($device_number) : null;
        return $this;
    }

    /**
     * Возвращает установленный дополнительный реквизит чека
     *
     * @return string|null
     */
    public function getAddCheckProps(): ?string
    {
        return $this->additional_check_props;
    }

    /**
     * Устанаваливает дополнительный реквизит чека
     *
     * @param string|null $additional_check_props
     * @return $this
     * @throws TooLongAddCheckPropException
     */
    public function setAddCheckProps(?string $additional_check_props): self
    {
        if (is_string($additional_check_props)) {
            $additional_check_props = trim($additional_check_props);
            if (mb_strlen($additional_check_props) > Constraints::MAX_LENGTH_ADD_CHECK_PROP) {
                throw new TooLongAddCheckPropException($additional_check_props);
            }
        }
        $this->additional_check_props = $additional_check_props ?: null;
        return $this;
    }

    /**
     * Возвращает установленный дополнительный реквизит пользователя
     *
     * @return AdditionalUserProps|null
     */
    public function getAddUserProps(): ?AdditionalUserProps
    {
        return $this->additional_user_props;
    }

    /**
     * Устанаваливает дополнительный реквизит пользователя
     *
     * @param AdditionalUserProps|null $additional_user_props
     * @return $this
     */
    public function setAddUserProps(?AdditionalUserProps $additional_user_props): self
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
     * @param SectoralCheckProps[]|null $sectoral_check_props
     * @throws SectoralCheckPropException
     */
    public function setSectoralCheckProps(?array $sectoral_check_props): static
    {
        foreach ($sectoral_check_props ?? [] as $prop) {
            if (!($prop instanceof SectoralCheckProps)) {
                throw new SectoralCheckPropException();
            }
        }

        $this->sectoral_check_props = $sectoral_check_props;
        return $this;
    }

    /**
     * @param SectoralCheckProps $sectoral_check_props
     * @return $this
     */
    public function addSectoralCheckProps(SectoralCheckProps $sectoral_check_props): static
    {
        $this->sectoral_check_props[] = $sectoral_check_props;
        return $this;
    }

    /**
     * Регистрирует приход по текущему документу
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
    public function sell(FiscalizerV5 $fiscalizer, ?string $external_id = null): ?AtolResponse
    {
        return $fiscalizer->sell($this, $external_id);
    }

    /**
     * Регистрирует возврат прихода по текущему документу
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
    public function sellRefund(FiscalizerV5 $fiscalizer, ?string $external_id = null): ?AtolResponse
    {
        return $fiscalizer->sellRefund($this, $external_id);
    }

    /**
     * Регистрирует расход по текущему документу
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
    public function buy(FiscalizerV5 $fiscalizer, ?string $external_id = null): ?AtolResponse
    {
        return $fiscalizer->buy($this, $external_id);
    }

    /**
     * Регистрирует возврат расхода по текущему документу
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
    public function buyRefund(FiscalizerV5 $fiscalizer, ?string $external_id = null): ?AtolResponse
    {
        return $fiscalizer->buyRefund($this, $external_id);
    }

    /**
     * Возвращает массив для кодирования в json
     *
     * @throws Exception
     */
    public function jsonSerialize(): array
    {
        $json = $this->getProperties();

        return $json;
    }
}
