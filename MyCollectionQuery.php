<?php

namespace MyCollection;

class MyCollectionQuery extends MyAbstractCollection
{
    protected MyAbstractCollection $origin;
    protected array $linkedIds = [];

    public function __construct(MyAbstractCollection $origin)
    {
        $this->origin = $origin;
        $this->autoIncrement = $origin->autoIncrement;
    }

    protected function linkToQuery($value, int $id)
    {
        if (is_null($value)) {
            return;
        }

        $this->linkedIds[] = $id;

        if (is_null($this->last)) {
            $this->first = new MyCollectionItem(null, $value, $id);
            $this->last = $this->first;
        } else {
            $this->last->link($value, $id);
            $this->last = $this->last->getNext();
        }
    }

    public function orWhere(string $attribute, $value): MyCollectionQuery
    {
        return $this->orWhereIsTrue($attribute, '==', $value);
    }

    public function orWhereIsTrue(string $attribute, string $condition, $value)
    {
        $this->origin->removeNullFromLast();
        $item = $this->origin->first;
        $linkedIds = $this->linkedIds;

        while (! is_null($item)) {
            if (in_array($item->getId(), $linkedIds)) {
                $item = $item->getNext();

                continue;
            }

            if (is_object($item->getContent()) || is_array($item->getContent())) {
                $search = ((array) $item->getContent())[$attribute] ?? null;

                if (eval("return '$search' $condition '$value';")) {
                    $this->linkToQuery($item->getContent(), $item->getId());
                }
            }

            $item = $item->getNext();
        }

        return $this;
    }

    //untested
    public function orWhereIn(string $attribute, array $array): MyCollectionQuery
    {
        $this->origin->removeNullFromLast();
        $item = $this->origin->first;
        $linkedIds = $this->linkedIds;

        while (! is_null($item)) {
            if (in_array($item->getId(), $linkedIds)) {
                $item = $item->getNext();

                continue;
            }

            if (is_object($item->getContent()) || is_array($item->getContent())) {
                $search = ((array) $item->getContent())[$attribute] ?? null;

                if (in_array($search, $array)) {
                    $this->linkToQuery($item->getContent(), $item->getId());
                }
            }
            $item = $item->getNext();
        }

        return $this;
    }

    public function orWhereNotIn(string $attribute, array $array): MyCollectionQuery
    {
        $this->origin->removeNullFromLast();
        $item = $this->origin->first;
        $linkedIds = $this->linkedIds;

        while (! is_null($item)) {
            if (in_array($item->getId(), $linkedIds)) {
                $item = $item->getNext();

                continue;
            }
            if (is_object($item->getContent()) || is_array($item->getContent())) {
                $search = ((array) $item->getContent())[$attribute] ?? null;

                if (! in_array($search, $array)) {
                    $this->linkToQuery($item->getContent(), $item->getId());
                }
            }
            $item = $item->getNext();
        }

        return $this;
    }

    public function orWhereExists(string $attribute): MyCollectionQuery
    {
        $this->origin->removeNullFromLast();
        $item = $this->origin->first;
        $linkedIds = $this->linkedIds;

        while (! is_null($item)) {
            if (in_array($item->getId(), $linkedIds)) {
                $item = $item->getNext();

                continue;
            }
            if (is_object($item->getContent()) || is_array($item->getContent())) {
                $keys = array_keys((array) $item->getContent());

                if (in_array($attribute, $keys)) {
                    $this->linkToQuery($item->getContent(), $item->getId());
                }
            }
            $item = $item->getNext();
        }

        return $this;
    }

    public function orWhereNotExists(string $attribute): MyCollectionQuery
    {
        $this->origin->removeNullFromLast();
        $item = $this->origin->first;
        $linkedIds = $this->linkedIds;

        while (! is_null($item)) {
            if (in_array($item->getId(), $linkedIds)) {
                $item = $item->getNext();

                continue;
            }
            if (is_object($item->getContent()) || is_array($item->getContent())) {
                $keys = array_keys((array) $item->getContent());

                if (! in_array($attribute, $keys)) {
                    $this->linkToQuery($item->getContent(), $item->getId());
                }
            }
            $item = $item->getNext();
        }

        return $this;
    }

    public function export(): MyCollection
    {
        $collection = new MyCollection();

        $collection->first = $this->first;
        $collection->last = $this->last;
        $collection->autoIncrement = $this->autoIncrement;

        return $collection;
    }
}
