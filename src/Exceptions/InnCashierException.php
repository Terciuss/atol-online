<?php

/*
 * Copyright (c) 2020-2021 Антон Аксенов (Anthony Axenov)
 *
 * This code is licensed under MIT.
 * Этот код распространяется по лицензии MIT.
 * https://github.com/anthonyaxenov/atol-online/blob/master/LICENSE
 */

declare(strict_types=1);

namespace AtolOnline\Exceptions;

use AtolOnline\Constraints;
use AtolOnline\Ffd105Tags;

/**
 * Исключение, возникающее при попытке указать слишком длинное имя кассира
 */
class InnCashierException extends TooLongException
{
    protected $message = 'Некорректный инн кассира';
    protected array $ffd_tags = [Ffd105Tags::CASHIER];
}
