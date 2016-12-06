<?php
/**
 * Created by PhpStorm.
 * User: Sahej
 * Date: 6/13/2016
 * Time: 2:06 PM
 */

namespace Application\Database;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class PictureTable
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



    public function getAllPictures(){
        $select = $this->sql->select()->from('pictures');
        $query = $this->sql->buildSqlString($select);
        return $this->adapter->query($query)->execute();
    }


    //perform a left join
    public function joinPicturesAuthors(){


        $select = $this->sql
            ->select()
            ->from('pictures')
            ->join('authors','pictures.authors_id = authors.authors_id',['authors_firstname','authors_lastname'],'left')
            ->limit(8);

        $query = $this->sql->buildSqlString($select);
        return $this->adapter->query($query)->execute();

    }

    public function joinPicturesAuthorsOffset($count,$start){


        $select = $this->sql
            ->select()
            ->from('pictures')
            ->join('authors','pictures.authors_id = authors.authors_id',['authors_firstname','authors_lastname'],'left')
            ->limit($count)
            ->offset($start);

        $query = $this->sql->buildSqlString($select);
        return $this->adapter->query($query)->execute();

    }

    public function searchtitle($word){

        //echo $word;
        //exit(0);



        $select = $this->sql
            ->select()
            ->from('pictures')
            ->join('authors','pictures.authors_id = authors.authors_id',['authors_firstname','authors_lastname'],'left')
            ->where('pictures.pictures_title LIKE "'.$word.'%"');


        $query = $this->sql->buildSqlString($select);

        return $this->adapter->query($query)->execute();
    }

    public function mediaExists($mediaid){

        $select = $this->sql
                        ->select()
                        //->columns('pictures_filename')
                        ->from('pictures')
                        ->where('pictures_filename LIKE "'.$mediaid.'%"');


        $query = $this->sql->buildSqlString($select);

        return $this->adapter->query($query)->execute();


    }

    public function insertIntoTable($url,$mediaid, $authorsid,$descrp){



        $insert = $this->sql
                    ->insert()
                    ->into('pictures')
                    ->values(['authors_id'=> $authorsid,'pictures_instagram' => $url, 'pictures_filename'=>$mediaid,'pictures_description'=>$descrp]);


        $query = $this->sql->buildSqlString($insert);

                return $this->adapter->query($query)->execute();
    }


    public function getPictureId(){

        $select = $this->sql->select()
                            ->columns(['pictures_id'])
                            ->from('pictures');

        $query = $this->sql->buildSqlString($select);

        return $this->adapter->query($query)->execute();

    }


}