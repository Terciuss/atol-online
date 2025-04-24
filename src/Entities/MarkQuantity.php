<?php

declare(strict_types=1);

namespace AtolOnline\Entities;

final class MarkQuantity extends Entity
{
    /**
     * @var int числитель дробной части предмета расчета
     * Значение реквизита «числитель» (тег 1293) должно быть строго меньше
     * значения реквизита «знаменатель» (тег 1294)
     * не может равняться «0
     */
    protected int $numerator;
    /**
     * @var int Знаменатель дробной части предмета расчета
     * Заполняется значением, равным количеству товара в партии (упаковке),
     * имеющей общий код маркировки товара.
     * не может равняться «0»
     */
    protected int $denominator;

    public function jsonSerialize(): array
    {
        return $this->getProperties();
    }
}
