<?php

namespace MyCollection;

use Exception;

class MyCollectionItem
{
    protected int $id;
    protected ?MyCollectionItem $previous = null;
    protected ?MyCollectionItem $next = null;
    protected $content;

    function __construct(?MyCollectionItem $previous, $content, $id)
    {
        if (is_null($content)) {
            throw new Exception('You cannot insert null values');
        }

        $this->previous = $previous;
        $this->content = $content;
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getPrevious(): ?MyCollectionItem
    {
        return $this->previous;
    }

    public function getNext(): ?MyCollectionItem
    {
        return $this->next;
    }

    public function link($content, $id): MyCollectionItem
    {
        $this->next = new MyCollectionItem($this, $content, $id);

        return $this->next;
    }

    public function unlink(): void
    {
        if(! is_null($this->next)) {
            $this->content = $this->next->content;
            $this->id = $this->next->id;
            $this->next = $this->next->next;

            if(! is_null($this->next)) {
                $this->next->previous = $this;
            }

            return;
        }

        if (! is_null($this->previous)) {
            $this->previous->next = null;
        }

        $this->content = null;
    }
}
