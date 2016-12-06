<?php
/**
 * Created by PhpStorm.
 * User: Sahej
 * Date: 6/13/2016
 * Time: 2:07 PM
 */

namespace Application\Model;


class Pictures
{

    private $pictures_id;
    private $authors_id;
    private $pictures_title;
    private $pictures_filename;
    private $pictures_description;
    private $picturesInstagram;

    public function getPicturesInstagram()
    {
        return $this->picturesInstagram;
    }

    public function setPicturesInstagram($picturesInstagram)
    {
        $this->picturesInstagram = $picturesInstagram;
    }

    public function setPicturesId($pictures_id)
    {
        $this->pictures_id = $pictures_id;
    }

    public function setAuthorsId($authors_id)
    {
        $this->authors_id = $authors_id;
    }

    public function setPicturesTitle($pictures_title)
    {
        $this->pictures_title = $pictures_title;
    }

    public function setPicturesFilename($pictures_filename)
    {
        $this->pictures_filename = $pictures_filename;
    }

    public function setPicturesDescription($pictures_description)
    {
        $this->pictures_description = $pictures_description;
    }


    public function getAuthorsId()
    {
        return $this->authors_id;
    }

    public function getPicturesId()
    {
        return $this->pictures_id;
    }

    public function getPicturesTitle()
    {
        return $this->pictures_title;
    }

    public function getPicturesFilename()
    {
        return $this->pictures_filename;
    }

    public function getPicturesDescription()
    {
        return $this->pictures_description;
    }

    public function getArray(){

        return [
            'id'=>$this->getPicturesId(),
            'authors_id'=>$this->getAuthorsId(),
            'picturesTitle' =>$this->getPicturesTitle(),
            'filename' =>$this->getPicturesFilename(),
            'picturesDescription' =>$this->getPicturesDescription()
        ];

    }


}