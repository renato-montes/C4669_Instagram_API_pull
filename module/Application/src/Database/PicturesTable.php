<?php

namespace Application\Database;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;

class PicturesTable
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

    public function getPictureRange($start, $count)
    {
        $select = $this->sql
            ->select()
            ->from(['p' => 'pictures'])
            ->join(['a' => 'authors'],
                   'p.authors_id = a.authors_id',
                   ['authors_firstname', 'authors_lastname'],
                   'left'
                  )
            ->limit($count)
            ->offset($start)
        ;

        $query = $this->sql->buildSqlString($select);

        return $this->adapter->query($query)->execute();
    }
    
    public function getPicturesByTitle($word)
    {
        $select = $this->sql
            ->select()
            ->from(['p' => 'pictures'])
            ->where(function (Where $where) use ($word) {
                $where->like('p.pictures_title', '%' . $word . '%');
            })
            ->join(['a' => 'authors'],
                   'p.authors_id = a.authors_id',
                   ['authors_firstname', 'authors_lastname'],
                   'left'
                  )
        ;
        
        // using a prepared statement to protect the app against SQL injection
        $statement = $this->sql->prepareStatementForSqlObject($select);
        return $statement->execute();
    }
    
    public function insertPicture($url, $userId, $caption)
    {
        $insert = $this->sql->insert()
            ->into('pictures')
            ->values(['pictures_instagram'   => $url,
                      'authors_id'           => $userId,
                      'pictures_description' => $caption])
        ;

        $query = $this->sql->buildSqlString($insert);
        return $this->adapter->query($query)->execute();
    }
    
    public function getLastPictureId()
    {
        $select = $this->sql->select()
            ->columns(['last_id' => new Expression('MAX(pictures_id)')])
            ->from('pictures')
        ;
        
        $query = $this->sql->buildSqlString($select);
        $maxid = $this->adapter->query($query)->execute()->current();
        return $maxid['last_id'];
    }
}

