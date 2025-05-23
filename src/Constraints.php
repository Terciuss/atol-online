<?php

/*
 * Copyright (c) 2020-2021 Антон Аксенов (Anthony Axenov)
 *
 * This code is licensed under MIT.
 * Этот код распространяется по лицензии MIT.
 * https://github.com/anthonyaxenov/atol-online/blob/master/LICENSE
 */

declare(strict_types=1);

namespace AtolOnline;

/**
 * Класс с константами ограничений
 */
final class Constraints
{
    /**
     * Максимальная длина Callback URL
     */
    public const MAX_LENGTH_CALLBACK_URL = 256;

    /**
     * Максимальная длина email
     */
    public const MAX_LENGTH_EMAIL = 64;

    /**
     * Максимальная длина логина ККТ
     */
    public const MAX_LENGTH_LOGIN = 100;

    /**
     * Максимальная длина пароля ККТ
     */
    public const MAX_LENGTH_PASSWORD = 100;

    /**
     * Максимальная длина адреса места расчётов
     */
    public const MAX_LENGTH_PAYMENT_ADDRESS = 256;

    /**
     * Максимальная длина наименования покупателя (1227)
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 17
     */
    public const MAX_LENGTH_CLIENT_NAME = 256;

    /**
     * Максимальная длина наименования предмета расчёта (1030)
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 21
     */
    public const MAX_LENGTH_ITEM_NAME = 128;

    /**
     * Максимальная цена за единицу предмета расчёта (1079)
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 21
     */
    public const MAX_COUNT_ITEM_PRICE = 42949672.95;

    /**
     * Максимальное количество (вес) единицы предмета расчёта (1023)
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 21
     */
    public const MAX_COUNT_ITEM_QUANTITY = 99999.999;

    /**
     * Максимальная стоимость всех предметов расчёта в документе прихода, расхода,
     * возврата прихода, возврата расхода (1043)
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 21
     */
    public const MAX_COUNT_ITEM_SUM = 42949672.95;

    /**
     * Максимальная длина телефона или email покупателя (1008)
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 17
     */
    public const MAX_LENGTH_CLIENT_CONTACT = 64;

    /**
     * Длина операции для платёжного агента (1044)
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 19
     */
    public const MAX_LENGTH_PAYING_AGENT_OPERATION = 24;

    /**
     * Максимальное количество предметов расчёта в документе прихода, расхода, возврата прихода, возврата расхода
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 21
     */
    public const MAX_COUNT_DOC_ITEMS = 100;

    /**
     * Максимальная длина единицы измерения предмета расчётов
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 21
     */
    public const MAX_LENGTH_MEASUREMENT_UNIT = 16;

    /**
     * Максимальная длина пользовательских данных для предмета расчётов (1191)
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 29
     */
    public const MAX_LENGTH_USER_DATA = 64;

    /**
     * Минимальная длина кода таможенной декларации (1231)
     *
     * @see https://online.atol.ru/possystem/v4/schema/sell Схема "#/receipt/items/declaration_number"
     */
    public const MIN_LENGTH_DECLARATION_NUMBER = 1;

    /**
     * Максимальная длина кода таможенной декларации (1231)
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 30
     */
    public const MAX_LENGTH_DECLARATION_NUMBER = 32;

    /**
     * Максимальное количество платежей в любом документе
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 30 и 35
     */
    public const MAX_COUNT_DOC_PAYMENTS = 10;

    /**
     * Максимальное количество ставок НДС в любом документе
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 31 и 36
     */
    public const MAX_COUNT_DOC_VATS = 6;

    /**
     * Максимальная сумма одной оплаты
     */
    public const MAX_COUNT_PAYMENT_SUM = 99999.999;

    /**
     * Максимальная длина имени кассира (1021)
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 32
     */
    public const MAX_LENGTH_CASHIER_NAME = 64;

    public const LENGTH_CASHIER_INN = 64;

    /**
     * Максимальная длина кода товара в байтах (1162)
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 21
     */
    public const MAX_LENGTH_ITEM_CODE = 32;

    /**
     * Максимальная длина значения дополнительного реквизита чека (1192)
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 32
     */
    public const MAX_LENGTH_ADD_CHECK_PROP = 16;

    /**
     * Максимальная длина наименования дополнительного реквизита пользователя (1085)
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 32
     */
    public const MAX_LENGTH_ADD_USER_PROP_NAME = 64;

    /**
     * Максимальная длина значения дополнительного реквизита пользователя (1086)
     *
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 32
     */
    public const MAX_LENGTH_ADD_USER_PROP_VALUE = 256;

    /**
     * Формат даты документа коррекции
     */
    public const CORRECTION_DATE_FORMAT = 'd.m.Y';

    /**
     * Регулярное выражение для валидации строки ИНН
     *
     * @see https://online.atol.ru/possystem/v4/schema/sell Схема "#/receipt/client/inn"
     */
    public const PATTERN_INN
        = /* @lang PhpRegExp */
        '/(^[\d]{10}$)|(^[\d]{12}$)/';

    /**
     * Регулярное выражение для валидации номера телефона
     *
     * @see https://online.atol.ru/possystem/v4/schema/sell Схема "#/definitions/phone_number"
     */
    public const PATTERN_PHONE
        = /* @lang PhpRegExp */
        '/^([^\s\\\]{0,17}|\+[^\s\\\]{1,18})$/';

    /**
     * Регулярное выражение для валидации строки Callback URL
     */
    public const PATTERN_CALLBACK_URL
        = /* @lang PhpRegExp */
        '/^http(s?):\/\/[0-9a-zA-Zа-яА-Я]' .
        '([-.\w]*[0-9a-zA-Zа-яА-Я])*(:(0-9)*)*(\/?)([a-zAZ0-9а-яА-Я\-.?,\'\/\\\+&=%\$#_]*)?$/';

    /**
     * Регулярное выражение для валидации кода страны происхождения товара
     */
    public const PATTERN_OKSM_CODE
        = /* @lang PhpRegExp */
        '/^[\d]{3}$/';
}
