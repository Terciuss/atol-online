<?php

/*
 * Copyright (c) 2020-2021 Антон Аксенов (Anthony Axenov)
 *
 * This code is licensed under MIT.
 * Этот код распространяется по лицензии MIT.
 * https://github.com/anthonyaxenov/atol-online/blob/master/LICENSE
 */

/** @noinspection PhpMultipleClassDeclarationsInspection */

declare(strict_types=1);

namespace AtolOnline\Entities;

use ArrayAccess;
use BadMethodCallException;
use Illuminate\Contracts\Support\Arrayable;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Stringable;

/**
 * Абстрактное описание любой сущности, представляемой как json
 */
abstract class Entity implements JsonSerializable, Stringable, Arrayable, ArrayAccess
{
    /**
     * @inheritDoc
     */
    abstract public function jsonSerialize(): array;

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        'company' => "\AtolOnline\Entities\Company",
        'correction_info' => "\AtolOnline\Entities\CorrectionInfo",
        'payments' => "array",
        'vats' => "\AtolOnline\Collections\Vats|null",
        'cashier' => "null|string",
    ])]
    public function toArray()
    {
        return $this->jsonSerialize();
    }

    /**
     * Возвращает строковое представление json-структуры объекта
     *
     * @return false|string
     */
    public function __toString()
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->toArray()[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->toArray()[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new BadMethodCallException(
            'Объект ' . static::class . ' нельзя изменять как массив. Следует использовать сеттеры.'
        );
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new BadMethodCallException(
            'Объект ' . static::class . ' нельзя изменять как массив. Следует использовать сеттеры.'
        );
    }

    public function getProperties($withNull = false): array
    {
        $arr = get_object_vars($this);
        foreach ($arr as $k => $v) {
            $method = $this->camel('get_' . $k);
            if (method_exists($this, $method)) {
                $v = $this->$method();
            }
            if (!$withNull && is_null($v)) {
                unset($arr[$k]);
                continue;
            }
            if ($v instanceof Entity) {
                $arr[$k] = $v->jsonSerialize();
            }
        }
        return $arr;
    }

    private function camel(string $str): string
    {
        $str = preg_replace_callback(
            '/[^a-zA-Z0-9]+([a-zA-Z0-9])/',
            function ($matches) {
                return strtoupper($matches[1]);
            },
            $str
        );
        return lcfirst($str);
    }
}
