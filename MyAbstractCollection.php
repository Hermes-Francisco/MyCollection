<?php

namespace MyCollection;

abstract class MyAbstractCollection
{
    protected ?MyCollectionItem $first = null;
    protected ?MyCollectionItem $last = null;
    protected int $autoIncrement = 0;

    public function get(): array
    {
        $this->removeNullFromLast();
        $itemArray = [];

        $current = $this->first;

        while (! is_null($current)) {
            $itemArray[] = $current->getContent();

            $current = $current->getNext();
        }

        return $itemArray;
    }

    public function first()
    {
        $this->removeNullFromLast();

        if (is_null($this->first)) {
            return null;
        }

        return $this->first->getContent();
    }

    public function last()
    {
        $this->removeNullFromLast();

        if (is_null($this->last)) {
            return null;
        }

        return $this->last->getContent();
    }

    public function count(): int
    {
        $this->removeNullFromLast();

        $count = 0;

        $item = $this->first;

        while (! is_null($item)) {
            $count++;
            $item = $item->getNext();
        }

        return $count;
    }

    public function where(string $attribute, $value): MyCollectionQuery
    {
        return $this->whereIsTrue($attribute, '==', $value);
    }

    public function whereIsTrue(string $attribute, string $condition, $value): MyCollectionQuery
    {
        $this->removeNullFromLast();

        $query = new MyCollectionQuery($this);
        $item = $this->first;

        while (! is_null($item)) {
            if (is_object($item->getContent()) || is_array($item->getContent())) {
                $search = ((array) $item->getContent())[$attribute] ?? null;

                if (eval("return '$search' $condition '$value';")) {
                    $query->linkToQuery($item->getContent(), $item->getId());
                }
            }
            $item = $item->getNext();
        }

        return $query;
    }

    public function whereIn(string $attribute, array $array): MyCollectionQuery
    {
        $this->removeNullFromLast();

        $query = new MyCollectionQuery($this);
        $item = $this->first;

        while (! is_null($item)) {
            if (is_object($item->getContent()) || is_array($item->getContent())) {
                $search = ((array) $item->getContent())[$attribute] ?? null;

                if (in_array($search, $array)) {
                    $query->linkToQuery($item->getContent(), $item->getId());
                }
            }
            $item = $item->getNext();
        }

        return $query;
    }

    public function whereNotIn(string $attribute, array $array): MyCollectionQuery
    {
        $this->removeNullFromLast();

        $query = new MyCollectionQuery($this);
        $item = $this->first;

        while (! is_null($item)) {
            if (is_object($item->getContent()) || is_array($item->getContent())) {
                $search = ((array) $item->getContent())[$attribute] ?? null;

                if (! in_array($search, $array)) {
                    $query->linkToQuery($item->getContent(), $item->getId());
                }
            }
            $item = $item->getNext();
        }

        return $query;
    }

    public function whereExists(string $attribute): MyCollectionQuery
    {
        $this->removeNullFromLast();

        $query = new MyCollectionQuery($this);
        $item = $this->first;

        while (! is_null($item)) {
            if (is_object($item->getContent()) || is_array($item->getContent())) {
                $keys = array_keys((array) $item->getContent());

                if (in_array($attribute, $keys)) {
                    $query->linkToQuery($item->getContent(), $item->getId());
                }
            }
            $item = $item->getNext();
        }

        return $query;
    }

    public function whereNotExists(string $attribute): MyCollectionQuery
    {
        $this->removeNullFromLast();

        $query = new MyCollectionQuery($this);
        $item = $this->first;

        while (! is_null($item)) {
            if (is_object($item->getContent()) || is_array($item->getContent())) {
                $keys = array_keys((array) $item->getContent());

                if (! in_array($attribute, $keys)) {
                    $query->linkToQuery($item->getContent(), $item->getId());
                }
            }
            $item = $item->getNext();
        }

        return $query;
    }

    public function each(Callable $callback): void
    {
        $this->removeNullFromLast();

        $item = $this->first;

        while (! is_null($item)) {
            $callback($item->getContent());

            $item = $item->getNext();
        }
    }

    protected function removeNullFromLast(): void
    {
        if (! is_null($this->last) && is_null($this->last->getContent())) {
            $this->last = $this->last->getPrevious();
        }

        if(is_null($this->last)) {
            $this->autoIncrement = 0;
            $this->first = null;
        }
    }
}
