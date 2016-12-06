<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;


use Application\Database\AuthorTable;
use Application\Database\CommentTable;
use Application\Database\PictureTable;
use Application\Model\Authors;
use Application\Model\Comments;
use Application\Model\Pictures;
use Instagram\Auth;
use Instagram\Core\Proxy;
use Instagram\Media;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Instagram\Instagram;
use Instagram\Comment;
use Zend\View\View;

/***
 * Class IndexController
 * @package Application\Controller
 *
 * author:Sahej Bhatia
 * Created: June 24th, 2016
 *
 * Description : this is the the sole controller for this project. all The routes are mapped to their respective actions which are handled here.
 *
 * Specifically for the OAuth - we had a constructor which holds all the information for our client
 * the scope defined what access are we allowed
 *  and the redirect URL : localhost:8090/import is handled in importAction. where all the database related functionality is handled .
 *
 *
 *
 */

class IndexController extends AbstractActionController
{

    private $auth;

    public function  __construct(){



        $this->auth = new Auth([
            'client_id'     => '83afd60bd0984160b8276436d5b77f1d',
            'client_secret' => '0f5ff315fbf74fcba3f0df746f8e700a',
            'redirect_uri'  => 'http://localhost:8090/import',
            'scope'         => array( 'likes', 'comments', 'relationships' )
        ]);

    }

    public function authorizeAction(){

        try{
            $this->auth->authorize();
        }catch(\Exception $e){
            echo $e->getMessage();
            exit(0);
        }
    }

    public function importAction(){


        $data = [];

        $request = $this->getRequest();
        /** @var \Zend\Http\Request $request */
        $code = $request->getQuery('code');


        //initiaiting the tables
        $picturesTable = new PictureTable("root","","comp2920");
        $authorsTable = new AuthorTable("root","","comp2920");
        $commentTable = new CommentTable("root","","comp2920");

        //grabing token for this user.
        $token = $this->auth->getAccessToken($code);

        $instagram = new Instagram();
        $instagram->setClientID('21d6c58169af41eabeb4827ef378bfa0');
        $instagram->setAccessToken($token);

        //grabbing current user
        $user = $instagram->getCurrentUser();

        //grabbing related data to current user.
        $media = $user->getMedia();

        $userid = $user->getId();

        try{

                //iterating through the data relatedto current user.
                foreach ($media as $picture)
                {

                    //grabbing images
                    $data[] = $picture->images;

                    //media id !!!
                    $mediaid = $picture->id;

                   $urls = $picture->images->standard_resolution->url;

                   $image = $picture->images->standard_resolution->url;

                    //grabbing pictures title
                    $description= $picture->caption->text;

                    //grabbing full name for user
                    $username=  $picture->user->full_name;
                    $name = explode(" ",$username);

                    //BEFORE YOU INSERT - MAKE SURE THE DATA DOES NOT EXITS
                    $pictures_result = $picturesTable->mediaExists($mediaid);
                    $authors_result = $authorsTable->authorExists($name[0],$name[1]);

                    if(count($authors_result) == 0){
                        $authors = $authorsTable->insertAuthors($name[0],$name[1]);

                    }

                    //grabbing the curent id for the author
                    $authorsid = $authorsTable->getAllID();
                    $allauthorid=[];
                    foreach ($authorsid as $id){
                        $allauthorid[]= $id['authors_id'];
                    }
                    $currentAuthors_id = max($allauthorid);


                    if (count($pictures_result) == 0){

                        $picturesTable->insertIntoTable($urls,$mediaid,$currentAuthors_id,$description);
                    }

                    //fetching current pictures id

                    $picturedid = $picturesTable->getPictureId();

                    $allid = [];

                    foreach ($picturedid as $id){
                        $allid[] = $id['pictures_id'];
                    }

                    $currentPicturesId=  max($allid);

                    //IF COMMENT COUNT > 0 - GET COMMENTS

                    $commentcount = $picture->comments->count;

                    if($commentcount > 0){

                        $userdata[] = $picture->getComments();

                        foreach ($userdata as $item) {

                            foreach ($item as $u){

                                $text[] = $u->text;

                                foreach ($text as $t){

                                    //checking if comments exits
                                    $comments_result = $commentTable->commentExits($t);

                                    //if comment doesnt exits - insert into table
                                    if(count($comments_result) == 0){
                                        $commentTable->insertComment($t,$currentPicturesId);

                                    }
                                }
                            }
                        }
                    }
                }


        }catch(\Exception $e){
            echo $e->getMessage();
        }

    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function picturesAction(){

        $data=[];

        $picturesTable = new PictureTable("root","","comp2920");
        $authorsTable = new AuthorTable("root","","comp2920");
        $request = $this->getRequest();


       if(isset($_GET['count'])){
            /** @var \Zend\Http\Request $request */
            $count = $request->getQuery('count');
      }

        if(isset($_GET['start'])){
            /** @var \Zend\Http\Request $request */
            $start = $request->getQuery('start');
        }


        if($count == null && $start == null){
            $pictures = $picturesTable->joinPicturesAuthors();

            foreach ($pictures as $pics) {
                $picmodel = new Pictures();
                $authmodel = new Authors();
                $picmodel->setPicturesId($pics['pictures_id']);
                $picmodel->setPicturesTitle($pics['pictures_title']);
                $picmodel->setPicturesFilename($pics['pictures_filename']);
                $picmodel->setPicturesDescription($pics['pictures_description']);
                $picmodel->setAuthorsId($pics['authors_id']);
                $picmodel->setPicturesInstagram($pics['pictures_instagram']);
                $authmodel->setAuthorsFirstname($pics['authors_firstname']);
                $authmodel->setAuthorsLastname($pics['authors_lastname']);


                $data[] = [
                    'id' => $picmodel->getPicturesId(),
                    'authorName' => $authmodel->getAuthorsFirstname() . " " . $authmodel->getAuthorsLastname(),
                    'pictureTitle' => $picmodel->getPicturesTitle(),
                    'pictureDescription' => $picmodel->getPicturesDescription(),
                    'filename' => $picmodel->getPicturesFilename(),
                    'pictureInstagram' =>$picmodel->getPicturesInstagram()
                ];
            }
        }else{

            $pictures = $picturesTable->joinPicturesAuthorsOffset($count,$start);
            foreach ($pictures as $pics) {
                $picmodel = new Pictures();
                $authmodel = new Authors();
                $picmodel->setPicturesId($pics['pictures_id']);
                $picmodel->setPicturesTitle($pics['pictures_title']);
                $picmodel->setPicturesFilename($pics['pictures_filename']);
                $picmodel->setPicturesDescription($pics['pictures_description']);
                $picmodel->setAuthorsId($pics['authors_id']);
                $picmodel->setPicturesInstagram($pics['pictures_instagram']);
                $authmodel->setAuthorsFirstname($pics['authors_firstname']);
                $authmodel->setAuthorsLastname($pics['authors_lastname']);


                $data[] = [
                    'id' => $picmodel->getPicturesId(),
                    'authorName' => $authmodel->getAuthorsFirstname() . " " . $authmodel->getAuthorsLastname(),
                    'pictureTitle' => $picmodel->getPicturesTitle(),
                    'pictureDescription' => $picmodel->getPicturesDescription(),
                    'filename' => $picmodel->getPicturesFilename(),
                    'pictureInstagram' =>$picmodel->getPicturesInstagram()
                ];
            }
        }

        return new JsonModel($data);
    }


    public function commentsAction(){

            $commentstable = new CommentTable("root","","comp2920");
            $request = $this->getRequest();

            /** @var \Zend\Http\Request $request */

        try{

            $pictureid = $request->getQuery('pictures_id');

            //should you check for int types??

            $data=[];
            $com = [];

            $comments = $commentstable->getComment($pictureid);

            foreach ($comments as $comment){

                $commentModel = new Comments();

                $commentModel->setCommentsComment($comment['comments_comment']);

                $data[]=$commentModel->getCommentArray();
                //this will return current comment
            }


            $com = [
                'comments' => $data
            ];

        }catch(\Exception $e){
            echo $e->getMessage();
        }

        //create a json on comments

            return new JsonModel($com);
    }

    public function searchAction(){

        $data=[];
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        $seacrhword = $request->getQuery('word');

        $picturestable = new PictureTable("root","","comp2920");

        if(is_string($seacrhword)){

            $title= $picturestable->searchtitle($seacrhword);

            foreach ($title as $t){
                $picmodel= new Pictures();
                $authmodel = new Authors();
                $picmodel->setPicturesId($t['pictures_id']);
                $picmodel->setPicturesTitle($t['pictures_title']);
                $picmodel->setPicturesFilename($t['pictures_filename']);
                $picmodel->setPicturesDescription($t['pictures_description']);
                $picmodel->setAuthorsId($t['authors_id']);
                $authmodel->setAuthorsFirstname($t['authors_firstname']);
                $authmodel->setAuthorsLastname($t['authors_lastname']);


                $data[]=[
                    'id'=>$picmodel->getPicturesId(),
                    'authorName'=>$authmodel->getAuthorsFirstname()." ".$authmodel->getAuthorsLastname(),
                    'pictureTitle'=>$picmodel->getPicturesTitle(),
                    'pictureDescription'=>$picmodel->getPicturesDescription(),
                    'filename'=>$picmodel->getPicturesFilename()
                ];
            }
        }




        return new JsonModel($data);

    }


}
