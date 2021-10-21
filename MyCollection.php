<?php

namespace MyCollection;

use Exception;

class MyCollection extends MyAbstractCollection
{
    public function __construct(array $values = null)
    {
        if (! is_null($values)) {
            $this->push(...$values);
        }
    }

    public function push(...$values)
    {
        $this->removeNullFromLast();

        foreach ($values as $value) {
            $this->link($value);
        }
    }

    public function pop(int $index = null)
    {
        $this->removeNullFromLast();

        if (! is_null($index)) {
            $this->popWithIndex($index);

            return;
        }

        if (isset($this->last)) {
            $this->last->unlink();

            $this->last = $this->last->getPrevious();
        }
    }

    protected function link($value)
    {
        if (is_null($value)) {
            return;
        }

        if (is_null($this->last)) {
            $this->first = new MyCollectionItem(null, $value, 0);
            $this->last = $this->first;
        } else {
            $this->autoIncrement++;
            $this->last->link($value, $this->autoIncrement);
            $this->last = $this->last->getNext();
        }
    }

    protected function popWithIndex(int $index): void
    {
        $item = $this->first;

        for ($index; $index > 0; $index--) {
            if (is_null($item)) {
                throw new Exception('Index overflow');
            }

            $item = $item->getNext();
        }

        if (is_null($item)) {
            throw new Exception('Index overflow');
        }

        if($item->getId() == $this->last->getId()) {
            $this->last = $this->last->getPrevious();
        }

        $item->unlink();
    }
}
