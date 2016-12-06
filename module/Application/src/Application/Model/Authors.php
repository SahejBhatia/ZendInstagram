<?php
/**
 * Created by PhpStorm.
 * User: Sahej
 * Date: 6/13/2016
 * Time: 2:08 PM
 */

namespace Application\Model;


class Authors
{

    private $authors_id;
    private $authors_firstname;
    private $authors_lastname;

    public function getAuthorsFirstname()
    {
        return $this->authors_firstname;
    }

    public function getAuthorsId()
    {
        return $this->authors_id;
    }

    public function getAuthorsLastname()
    {
        return $this->authors_lastname;
    }

    public function setAuthorsFirstname($authors_firstname)
    {
        $this->authors_firstname = $authors_firstname;
    }

    public function setAuthorsId($authors_id)
    {
        $this->authors_id = $authors_id;
    }

    public function setAuthorsLastname($authors_lastname)
    {
        $this->authors_lastname = $authors_lastname;
    }

    public function getArray(){

        return [
            'author_id'=>$this->getAuthorsId(),
            'authors_firstname' =>$this->getAuthorsFirstname(),
            'authors_lastname' =>$this->getAuthorsLastname()
        ];
    }

}