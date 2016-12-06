<?php
/**
 * Created by PhpStorm.
 * User: Sahej
 * Date: 6/19/2016
 * Time: 6:37 PM
 */

namespace Application\Database;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;


class AuthorTable
{

    private $sql;
    private $adapter;

    public function __construct($username, $password,$database){
        $this->adapter = new Adapter([
            'driver'=>'Pdo_MySql',
            'hostname'=> '127.0.0.1',
            'username'=>$username,
            'password' =>$password,
            'database'=>$database ,
        ]);

        $this->sql= new Sql($this->adapter);


    }



    public function getAllAuthors(){
        $select = $this->sql->select()->from('authors');
        $query = $this->sql->buildSqlString($select);
        return $this->adapter->query($query)->execute();
    }

    public function insertAuthors($firstname,$lastname){
       $insert = $this->sql
            ->insert()
            ->into('authors')
            ->values(['authors_firstname' => $firstname, 'authors_lastname'=>$lastname]);


        $query = $this->sql->buildSqlString($insert);

        return $this->adapter->query($query)->execute();
    }

    public function getAllID(){
        $select = $this->sql
                        ->select()
                        ->columns(['authors_id'])
                        ->from('authors');
        $query = $this->sql->buildSqlString($select);

        return $this->adapter->query($query)->execute();
    }

    public function getIDbyName($firstname,$lastname){

        $select = $this->sql->select()
                            ->columns(['authors_id'])
                            ->from('authors')
                            ->where('authors_firstname LIKE "'.$firstname.'%" AND authors_lastname LIKE "'.$lastname.'%"');

        $query = $this->sql->buildSqlString($select);
        return $this->adapter->query($query)->execute();
    }



    public function getAuthorByName($firstname,$lastname){

        $select = $this->sql
                        ->select()
                        ->columns(['authors_id'])
                        ->from('authors')
                        ->where('authors_firstname LIKE "'.$firstname.'%" AND authors_lastname LIKE "'.$lastname.'%"');


        $query = $this->sql->buildSqlString($select);

        return $this->adapter->query($query)->execute();


    }

    
    public function authorExists($firstname,$lastname){

        $select = $this->sql
            ->select()
            ->from('authors')
            ->where('authors_firstname LIKE "'.$firstname.'%" AND authors_lastname LIKE "'.$lastname.'%"');


        $query = $this->sql->buildSqlString($select);

        return $this->adapter->query($query)->execute();


    }
}