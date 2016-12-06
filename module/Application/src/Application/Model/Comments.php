<?php
/**
 * Created by PhpStorm.
 * User: Sahej
 * Date: 6/13/2016
 * Time: 2:08 PM
 */

namespace Application\Model;


class Comments
{

    private $comments_id;
    private $pictures_id;
    private $Comments_comment;


    public function setCommentsComment($Comments_comment)
    {
        $this->Comments_comment = $Comments_comment;
    }

    public function setCommentsId($comments_id)
    {
        $this->comments_id = $comments_id;
    }

    public function setPicturesId($pictures_id)
    {
        $this->pictures_id = $pictures_id;
    }

    public function getCommentsComment()
    {
        return $this->Comments_comment;
    }

    public function getCommentsId()
    {
        return $this->comments_id;
    }

    public function getPicturesId()
    {
        return $this->pictures_id;
    }


    public function getCommentArray(){

        return [
            'comment' =>$this->getCommentsComment()
        ];
    }

}