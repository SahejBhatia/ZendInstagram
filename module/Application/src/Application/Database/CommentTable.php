<?php
/**
 * Created by PhpStorm.
 * User: Sahej
 * Date: 6/13/2016
 * Time: 2:08 PM
 */

namespace Application\Database;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class CommentTable
{



    private $sql;
    private $adapter;

    public function __construct($username,$password,$database){
        $this->adapter = new Adapter([
            'driver'=>'Pdo_MySql',
            'hostname'=> '127.0.0.1',
            'username'=>$username,
            'password' =>$password,
            'database'=>$database ,
        ]);

        $this->sql= new Sql($this->adapter);


    }



    public function getAllComments(){
        $select = $this->sql->select()->from('comments');
        $query = $this->sql->buildSqlString($select);
        return $this->adapter->query($query)->execute();
    }

    public function getComment($pictid){
        $select = $this->sql
                        ->select()
                        ->columns(['comments_comment'])
                        ->from('comments')
                        ->where('pictures_id ='. $pictid);


        $query = $this->sql->buildSqlString($select);


        //maybe this is not returning anything ..
        return $this->adapter->query($query)->execute();

    }

    public function commentExits($comment){

        $select = $this->sql->select()
                            ->from('comments')
                            ->where('comments_comment LIKE "'.$comment.'%"');
        $query = $this->sql->buildSqlString($select);
        return $this->adapter->query($query)->execute();

    }

    public function insertComment($comment, $currentPicturesId){

        $insert = $this->sql
            ->insert()
            ->into('comments')
            ->values(['pictures_id'=> $currentPicturesId, 'comments_comment' => $comment]);

        $query = $this->sql->buildSqlString($insert);

        return $this->adapter->query($query)->execute();
    }



}