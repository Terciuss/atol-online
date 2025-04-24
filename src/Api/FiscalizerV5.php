<?php

/*
 * Copyright (c) 2020-2021 Антон Аксенов (Anthony Axenov)
 *
 * This code is licensed under MIT.
 * Этот код распространяется по лицензии MIT.
 * https://github.com/anthonyaxenov/atol-online/blob/master/LICENSE
 */

declare(strict_types=1);

namespace AtolOnline\Api;

use AtolOnline\{Constraints, TestEnvParams};
use AtolOnline\Entities\{CorrectionV5, ReceiptV5};
use AtolOnline\Exceptions\{AuthFailedException,
    EmptyGroupException,
    EmptyLoginException,
    EmptyPasswordException,
    InvalidCallbackUrlException,
    InvalidEntityInCollectionException,
    InvalidInnLengthException,
    InvalidPaymentAddressException,
    InvalidUuidException,
    TooLongCallbackUrlException,
    TooLongLoginException,
    TooLongPasswordException,
    TooLongPaymentAddressException};
use GuzzleHttp\Exception\GuzzleException;
use JetBrains\PhpStorm\Pure;
use Ramsey\Uuid\Uuid;

/**
 * Класс фискализатора для регистрации документов на ККТ
 */
final class FiscalizerV5 extends AtolClientV5
{
    /**
     * @var string|null Группа ККТ
     */
    private ?string $group = null;

    /**
     * @var string|null URL для приёма POST-запроса от API АТОЛ с результатом регистрации документа
     */
    private ?string $callback_url = null;

    /**
     * Конструктор
     *
     * @param bool $test_mode
     * @param string|null $login
     * @param string|null $password
     * @param string|null $group
     * @param array $config
     * @throws EmptyLoginException
     * @throws EmptyPasswordException
     * @throws TooLongLoginException
     * @throws TooLongPasswordException
     * @throws EmptyGroupException
     * @see https://guzzle.readthedocs.io/en/latest/request-options.html
     */
    public function __construct(
        bool    $test_mode = true,
        ?string $login = null,
        ?string $password = null,
        ?string $group = null,
        array   $config = []
    )
    {
        parent::__construct($test_mode, $login, $password, $config);
        !is_null($group) && $this->setGroup($group);
    }

    /**
     * Возвращает группу доступа к ККТ в соответствии с флагом тестового режима
     *
     * @return string|null
     */
    #[Pure]
    public function getGroup(): ?string
    {
        return $this->isTestMode()
            ? TestEnvParams::FFD12()['group']
            : $this->group;
    }

    /**
     * Устанавливает группу доступа к ККТ
     *
     * @param string $group
     * @return $this
     * @throws EmptyGroupException
     */
    public function setGroup(string $group): self
    {
        // критерии к длине строки не описаны ни в схеме, ни в документации
        empty($group = trim($group)) && throw new EmptyGroupException();
        $this->group = $group;
        return $this;
    }

    /**
     * Возвращает URL для приёма колбеков
     *
     * @return string|null
     */
    public function getCallbackUrl(): ?string
    {
        return $this->callback_url;
    }

    /**
     * Устанавливает URL для приёма колбеков
     *
     * @param string|null $url
     * @return $this
     * @throws TooLongCallbackUrlException
     * @throws InvalidCallbackUrlException
     */
    public function setCallbackUrl(?string $url = null): self
    {
        $url = trim((string)$url);
        if (mb_strlen($url) > Constraints::MAX_LENGTH_CALLBACK_URL) {
            throw new TooLongCallbackUrlException($url);
        } elseif (!empty($url) && !preg_match(Constraints::PATTERN_CALLBACK_URL, $url)) {
            throw new InvalidCallbackUrlException();
        }
        $this->callback_url = $url ?: null;
        return $this;
    }

    /**
     * Регистрирует документ прихода
     *
     * @param ReceiptV5 $receipt Объект документа
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
    public function sell(ReceiptV5 $receipt, ?string $external_id = null): ?AtolResponse
    {
        return $this->registerDocument('sell', $receipt, $external_id);
    }

    /**
     * Регистрирует документ возврата прихода
     *
     * @param ReceiptV5 $receipt Объект документа
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
    public function sellRefund(ReceiptV5 $receipt, ?string $external_id = null): ?AtolResponse
    {
        return $this->registerDocument('sell_refund', $receipt, $external_id);
    }

    /**
     * Регистрирует документ коррекции прихода
     *
     * @param CorrectionV5 $correction Объект документа
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
    public function sellCorrect(CorrectionV5 $correction, ?string $external_id = null): ?AtolResponse
    {
        return $this->registerDocument('sell_correction', $correction, $external_id);
    }

    /**
     * Регистрирует документ коррекции возврата прихода
     *
     * @param CorrectionV5 $correction Объект документа
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
    public function sellRefundCorrect(CorrectionV5 $correction, ?string $external_id = null): ?AtolResponse
    {
        return $this->registerDocument('sell_refund_correction', $correction, $external_id);
    }

    /**
     * Регистрирует документ расхода
     *
     * @param ReceiptV5 $receipt Объект документа
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
    public function buy(ReceiptV5 $receipt, ?string $external_id = null): ?AtolResponse
    {
        return $this->registerDocument('buy', $receipt, $external_id);
    }

    /**
     * Регистрирует документ возврата расхода
     *
     * @param ReceiptV5 $receipt Объект документа
     * @param string|null $external_id Уникальный код документа (если не указан, то будет создан UUID)
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
    public function buyRefund(ReceiptV5 $receipt, ?string $external_id = null): ?AtolResponse
    {
        return $this->registerDocument('buy_refund', $receipt, $external_id);
    }

    /**
     * Регистрирует документ коррекции расхода
     *
     * @param CorrectionV5 $correction Объект документа
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
    public function buyCorrect(CorrectionV5 $correction, ?string $external_id = null): ?AtolResponse
    {
        return $this->registerDocument('buy_correction', $correction, $external_id);
    }

    /**
     * Регистрирует документ коррекции возврата расхода
     *
     * @param CorrectionV5 $correction Объект документа
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
    public function buyRefundCorrect(CorrectionV5 $correction, ?string $external_id = null): ?AtolResponse
    {
        return $this->registerDocument('buy_refund_correction', $correction, $external_id);
    }

    /**
     * Проверяет статус чека на ККТ один раз
     *
     * @param string $uuid UUID регистрации
     * @return AtolResponse|null
     * @throws AuthFailedException
     * @throws EmptyLoginException
     * @throws EmptyPasswordException
     * @throws GuzzleException
     * @throws InvalidUuidException
     */
    public function getDocumentStatus(string $uuid): ?AtolResponse
    {
        !Uuid::isValid($uuid = trim($uuid)) && throw new InvalidUuidException($uuid);
        return $this->auth()
            ? $this->sendRequest('GET', $this->getFullUrl('report/' . $uuid))
            : null;
    }

    /**
     * Проверяет статус чека на ККТ нужное количество раз с указанным интервалом.
     * Вернёт результат как только при очередной проверке сменится статус регистрации документа.
     *
     * @param string $uuid UUID регистрации
     * @param int $retry_count Количество попыток
     * @param int $timeout Таймаут в секундах между попытками
     * @return AtolResponse|null
     * @throws AuthFailedException
     * @throws EmptyLoginException
     * @throws EmptyPasswordException
     * @throws GuzzleException
     * @throws InvalidUuidException
     */
    public function pollDocumentStatus(string $uuid, int $retry_count = 5, int $timeout = 1): ?AtolResponse
    {
        $try = 0;
        do {
            $response = $this->getDocumentStatus($uuid);
            if ($response->isSuccessful() && $response->getContent()->status == 'complete') {
                break;
            } else {
                sleep($timeout);
            }
            ++$try;
        } while ($try < $retry_count);
        return $response;
    }

    /**
     * Отправляет документ на регистрацию
     *
     * @param string $api_method Метод API
     * @param ReceiptV5|CorrectionV5 $document Документ
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
    protected function registerDocument(
        string                 $api_method,
        ReceiptV5|CorrectionV5 $document,
        ?string                $external_id = null,
        ?bool                  $ism_optional = false,
    ): ?AtolResponse
    {
        $this->isTestMode() && $document->getCompany()
            ->setInn(TestEnvParams::FFD12()['inn'])
            ->setPaymentAddress(TestEnvParams::FFD12()['payment_address']);
        $this->isTestMode() && $document instanceof ReceiptV5
        && $document->getClient()->setInn(TestEnvParams::FFD12()['inn']);
        $this->getCallbackUrl() && $data['service'] = ['callback_url' => $this->getCallbackUrl()];
        $data['ism_optional'] = $ism_optional;
        return $this->auth()
            ? $this->sendRequest(
                'POST',
                $this->getFullUrl($api_method),
                array_merge($data ?? [], [
                    'timestamp' => date('d.m.Y H:i:s'),
                    'external_id' => $external_id ?: Uuid::uuid4()->toString(),
                    $document::DOC_TYPE => $document->jsonSerialize(),
                ])
            )
            : null;
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    protected function getAuthEndpoint(): string
    {
        return $this->isTestMode()
            ? 'https://testonline.atol.ru/possystem/v5/getToken'
            : 'https://online.atol.ru/possystem/v5/getToken';
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    protected function getMainEndpoint(): string
    {
        return $this->isTestMode()
            ? 'https://testonline.atol.ru/possystem/v5/'
            : 'https://online.atol.ru/possystem/v5/';
    }

    /**
     * Возвращает полный URL метода API
     *
     * @param string $api_method
     * @return string
     */
    #[Pure]
    protected function getFullUrl(string $api_method): string
    {
        return $this->getMainEndpoint() . $this->getGroup() . '/' . trim($api_method);
    }
}
