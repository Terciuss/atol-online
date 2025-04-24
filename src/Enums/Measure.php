<?php

declare(strict_types=1);

namespace AtolOnline\Enums;

enum Measure: int
{
    /**
     * Применяется для предметов расчета, которые могут быть реализованы поштучно или единицами
     */
    case PIECEWISE_OR_UNITS = 0;

    /**
     * Грамм
     */
    case GRAM = 10;

    /**
     * Килограмм
     */
    case KILOGRAM = 11;

    /**
     * Тонна
     */
    case TON = 12;

    /**
     * Сантиметр
     */
    case CENTIMETER = 20;

    /**
     * Дециметр
     */
    case DECIMETER = 21;

    /**
     * Метр
     */
    case METER = 22;

    /**
     * Квадратный сантиметр
     */
    case SQUARE_CENTIMETER = 30;

    /**
     * Квадратный дециметр
     */
    case SQUARE_DECIMETER = 31;

    /**
     * Квадратный метр
     */
    case SQUARE_METER = 32;

    /**
     * Миллилитр
     */
    case MILLILITER = 40;

    /**
     * Литр
     */
    case LITER = 41;

    /**
     * Кубический метр
     */
    case CUBIC_METER = 42;

    /**
     * Киловатт час
     */
    case KILOWATT_HOUR = 50;

    /**
     * Гигакалория
     */
    case GIGACALORIE = 51;

    /**
     * Сутки (день)
     */
    case DAY = 70;

    /**
     * Час
     */
    case HOUR = 71;

    /**
     * Минута
     */
    case MINUTE = 72;

    /**
     * Секунда
     */
    case SECOND = 73;

    /**
     * Килобайт
     */
    case KILOBYTE = 80;

    /**
     * Мегабайт
     */
    case MEGABYTE = 81;

    /**
     * Гигабайт
     */
    case GIGABYTE = 82;

    /**
     * Терабайт
     */
    case TERABYTE = 83;

    /**
     * Применяется при использовании иных единиц измерения
     */
    case OTHER_UNITS = 255;
}
