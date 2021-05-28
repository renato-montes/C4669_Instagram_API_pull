<?php

namespace Application\Database;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class CommentsTable
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

    public function getCommentsByPictureId($pictureId)
    {
        $select = $this->sql
            ->select()
            ->from('comments')
            ->columns(['comments_comment'])
            ->where(['pictures_id' => $pictureId])
        ;

        $query = $this->sql->buildSqlString($select);

        return $this->adapter->query($query)->execute();
    }
    
    public function insertComment($pictureId, $comment)
    {
        $insert = $this->sql->insert()
            ->into('comments')
            ->values(['pictures_id' => $pictureId, 'comments_comment' => $comment])
        ;

        $query = $this->sql->buildSqlString($insert);

        return $this->adapter->query($query)->execute();
    }
}

