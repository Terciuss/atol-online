<?php
/*
 * Copyright (c) 2020-2021 Антон Аксенов (Anthony Axenov)
 *
 * This code is licensed under MIT.
 * Этот код распространяется по лицензии MIT.
 * https://github.com/anthonyaxenov/atol-online/blob/master/LICENSE
 */

declare(strict_types = 1);

namespace AtolOnline\Constants;

/**
 * Класс с константами ограничений
 */
final class Constraints
{
    /**
     * Максимальная длина Callback URL
     */
    const MAX_LENGTH_CALLBACK_URL = 256;

    /**
     * Максимальная длина email
     */
    const MAX_LENGTH_EMAIL = 64;
    
    /**
     * Максимальная длина логина ККТ
     */
    const MAX_LENGTH_LOGIN = 100;
    
    /**
     * Максимальная длина пароля ККТ
     */
    const MAX_LENGTH_PASSWORD = 100;
    
    /**
     * Максимальная длина адреса места расчётов
     */
    const MAX_LENGTH_PAYMENT_ADDRESS = 256;

    /**
     * Максимальная длина наименования покупателя (1227)
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 17
     */
    const MAX_LENGTH_CLIENT_NAME = 256;

    /**
     * Максимальная длина наименования предмета расчёта (1030)
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 21
     */
    const MAX_LENGTH_ITEM_NAME = 128;

    /**
     * Максимальная цена за единицу предмета расчёта (1079)
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 21
     */
    const MAX_COUNT_ITEM_PRICE = 42949672.95;

    /**
     * Максимальное количество (вес) единицы предмета расчёта (1023)
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 21
     */
    const MAX_COUNT_ITEM_QUANTITY = 99999.999;

    /**
     * Максимальная стоимость всех предметов расчёта в документе прихода, расхода,
     * возврата прихода, возврата расхода (1043)
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 21
     */
    const MAX_COUNT_ITEM_SUM = 42949672.95;

    /**
     * Максимальная длина телефона или email покупателя (1008)
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 17
     */
    const MAX_LENGTH_CLIENT_CONTACT = 64;

    /**
     * Длина операции для платёжного агента (1044)
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 19
     */
    const MAX_LENGTH_PAYING_AGENT_OPERATION = 24;

    /**
     * Максимальное количество предметов расчёта в документе прихода, расхода, возврата прихода, возврата расхода
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 21
     */
    const MAX_COUNT_DOC_ITEMS = 100;

    /**
     * Максимальная длина единицы измерения предмета расчётов
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 21
     */
    const MAX_LENGTH_MEASUREMENT_UNIT = 16;

    /**
     * Максимальная длина пользовательских данных для предмета расчётов (1191)
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 29
     */
    const MAX_LENGTH_USER_DATA = 64;

    /**
     * Максимальное количество платежей в любом документе
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 30 и 35
     */
    const MAX_COUNT_DOC_PAYMENTS = 10;

    /**
     * Максимальное количество ставок НДС в любом документе
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 31 и 36
     */
    const MAX_COUNT_DOC_VATS = 6;

    /**
     * Максимальная длина имени кассира (1021)
     * @see https://online.atol.ru/files/API_atol_online_v4.pdf Документация, стр 32
     */
    const MAX_LENGTH_CASHIER_NAME = 64;
    
    /**
     * Регулярное выражание для валидации строки ИНН
     */
    const PATTERN_INN = "/(^[0-9]{10}$)|(^[0-9]{12}$)/";
    
    /**
     * Регулярное выражание для валидации строки Callback URL
     */
    const PATTERN_CALLBACK_URL = "/^http(s?)\:\/\/[0-9a-zA-Zа-яА-Я]([-.\w]*[0-9a-zA-Zа-яА-Я])*(:(0-9)*)*(\/?)([a-zAZ0-9а-яА-Я\-\.\?\,\'\/\\\+&=%\$#_]*)?$/";
}