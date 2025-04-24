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

use AtolOnline\Ffd105Tags;

/**
 * Исключение, возникающее при попытке указать слишком длинное имя кассира
 */
class SectoralCheckPropException extends AtolException
{
    protected $message = 'Значение sectoral_check_props не может быть пустым';
}
