<?php

/*
 * Copyright (c) 2020-2021 Антон Аксенов (Anthony Axenov)
 *
 * This code is licensed under MIT.
 * Этот код распространяется по лицензии MIT.
 * https://github.com/anthonyaxenov/atol-online/blob/master/LICENSE
 */

declare(strict_types=1);

namespace AtolOnline\Enums;

/**
 * Константы, определяющие признаки предметов расчёта
 *
 */
enum PaymentObjectV5: int
{
    /**
     * Реализуемый товар (кроме подакцизного и маркированного)
     */
    case GENERAL_GOODS = 1;

    /**
     * Подакцизный товар (без маркировки)
     */
    case EXCISE_GOODS = 2;

    /**
     * Выполняемая работа
     */
    case SERVICE_WORK = 3;

    /**
     * Оказываемая услуга
     */
    case SERVICE = 4;

    /**
     * Прием ставок в азартных играх
     */
    case GAMBLING_BET = 5;

    /**
     * Выплата выигрыша в азартных играх
     */
    case GAMBLING_WIN = 6;

    /**
     * Прием средств при реализации лотерей
     */
    case LOTTERY_SALE = 7;

    /**
     * Выплата выигрыша в лотереях
     */
    case LOTTERY_WIN = 8;

    /**
     * Права на интеллектуальную собственность
     */
    case INTELLECTUAL_PROPERTY = 9;

    /**
     * Аванс, задаток, предоплата, кредит
     */
    case PREPAYMENT = 10;

    /**
     * Вознаграждение агента/посредника
     */
    case AGENT_REWARD = 11;

    /**
     * Взносы, пени, штрафы, бонусы
     */
    case PAYMENT_FEES = 12;

    /**
     * Прочие предметы расчета
     */
    case OTHER_ITEMS = 13;

    /**
     * Передача имущественных прав
     */
    case PROPERTY_RIGHTS = 14;

    /**
     * Внереализационный доход
     */
    case NON_OPERATING_INCOME = 15;

    /**
     * Расходы, уменьшающие налог
     */
    case TAX_DEDUCTIBLE = 16;

    /**
     * Торговый сбор
     */
    case TRADE_LEVY = 17;

    /**
     * Туристический налог
     */
    case TOURISM_TAX = 18;

    /**
     * Залог
     */
    case DEPOSIT = 19;

    /**
     * Расходы по ст. 346.16 НК РФ
     */
    case EXPENSE_DEDUCTION = 20;

    /**
     * Страховые взносы ИП (без сотрудников)
     */
    case PENSION_INDIVIDUAL = 21;

    /**
     * Страховые взносы организаций и ИП (с сотрудниками)
     */
    case PENSION_ORGANIZATION = 22;

    /**
     * Мед. страховка ИП (без сотрудников)
     */
    case HEALTH_INDIVIDUAL = 23;

    /**
     * Мед. страховка организаций и ИП (с сотрудниками)
     */
    case HEALTH_ORGANIZATION = 24;

    /**
     * Социальное страхование (нетрудоспособность/профзаболевания)
     */
    case SOCIAL_INSURANCE = 25;

    /**
     * Казино и игровые автоматы
     */
    case CASINO_OPERATIONS = 26;

    /**
     * Выдача средств банковским агентом
     */
    case BANK_AGENT_PAYOUT = 27;

    /**
     * Подакцизный товар с маркировкой (без кода)
     */
    case EXCISE_MARKED_NO_CODE = 30;

    /**
     * Подакцизный товар с маркировкой (с кодом)
     */
    case EXCISE_MARKED_WITH_CODE = 31;

    /**
     * Маркированный товар (без кода, не подакцизный)
     */
    case MARKED_NO_CODE = 32;

    /**
     * Маркированный товар (с кодом, не подакцизный)
     */
    case MARKED_WITH_CODE = 33;
}
