<?php

namespace Application\Model;

class Author
{
    private $firstName;
    private $lastName;

    public function setFirstName($name)
    {
        $this->firstName = $name;
    }

    public function setLastName($name)
    {
        $this->lastName = $name;
    }

    public function clear()
    {
        $this->firstName = NULL;
        $this->lastName = NULL;
    }
    
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}

