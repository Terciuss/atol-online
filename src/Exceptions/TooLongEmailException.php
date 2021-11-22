<?php
/*
 * Copyright (c) 2020-2021 Антон Аксенов (Anthony Axenov)
 *
 * This code is licensed under MIT.
 * Этот код распространяется по лицензии MIT.
 * https://github.com/anthonyaxenov/atol-online/blob/master/LICENSE
 */

declare(strict_types = 1);

namespace AtolOnline\Exceptions;

use AtolOnline\Constants\Constraints;

/**
 * Исключение, возникающее при попытке указать слишком длинный email
 */
class TooLongEmailException extends TooLongException
{
    protected $message = 'Слишком длинный email';
    protected int $max = Constraints::MAX_LENGTH_EMAIL;
    protected array $ffd_tags = [1008, 1117];
}
