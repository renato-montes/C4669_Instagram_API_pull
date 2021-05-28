<?php

namespace Application\Model;

class Comment
{
    private $comment;

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function clear()
    {
        $this->comment = NULL;
    }
}

