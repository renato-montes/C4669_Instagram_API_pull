<?php

namespace Application\Database;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class AuthorsTable
{
    private $sql;
    private $adapter;

    public function __construct($database, $username, $password)
    {
        $this->adapter = new Adapter([
            'username' => $username,
            'password' => $password,
            'database' => $database,
            'hostname' => '127.0.0.1',
            'driver'   => 'Pdo_Mysql'
        ]);

        $this->sql = new Sql($this->adapter);
    }

    public function getInstagramAuthorId($name)
    {
        $select = $this->sql
            ->select()
            ->from('authors')
            ->columns(['authors_id'])
            ->where(['authors_firstname' => $name])
        ;

        $query = $this->sql->buildSqlString($select);

        $result = $this->adapter->query($query)->execute()->current();
        return $result['authors_id'];
    }
    
    public function insertInstagramAuthor($name)
    {
        $insert = $this->sql->insert()
            ->into('authors')
            ->values(['authors_firstname' => $name])
        ;

        $query = $this->sql->buildSqlString($insert);

        return $this->adapter->query($query)->execute();
    }
}

