<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Validator\Digits;

use Instagram\Instagram;
use Instagram\Core\ApiException;

class InstagramConnectionController extends AbstractActionController
{
    private $auth;
    private $picturesTable;
    private $commentsTable;
    private $authorsTable;

    public function __construct($auth, $picturesTable, $commentsTable, $authorsTable)
    {
        $this->picturesTable = $picturesTable;
        $this->commentsTable = $commentsTable;
        $this->authorsTable  = $authorsTable;
        $this->auth          = $auth;
    }
    
    public function importAction()
    {
        $request = $this->getRequest();
        $code = $request->getQuery('code');

        if (isset($code))
        {
            try
            {
                $token = $this->auth->getAccessToken($code);
                $this->useToken($token);
                $data = ['message' => 'done'];
            }
            catch (ApiException $exception)
            {
                $data = ['message' => 'error'];
            }
        }
        else
        {
            $data = ['message' => 'error'];
        }
        
        $this->layout('layout/done');
        return new ViewModel($data);
    }

    public function authorizeAction()
    {
        $this->auth->authorize();
    }
    
    public function useToken($token)
    {
        $instagram = new Instagram();
        $instagram->setAccessToken($token);

        $user = $instagram->getCurrentUser();
        $mediaData = $user->getMedia()->getData();
        $commentData = NULL;
        foreach ($mediaData as $picture)
        {
            $this->savePictureData($picture);
        }
    }
    
    public function savePictureData($picture)
    {
        //get user who made the Instagram post
        $username = $picture->getUser()->getUserName();
        
        //if the user is not in the database, add them
        $userId = $this->authorsTable->getInstagramAuthorId($username);
        if (!isset($userId))
        {
            $this->authorsTable->insertInstagramAuthor($username);
            $userId = $this->authorsTable->getInstagramAuthorId($username);
        }
        
        //save picture information in the database
        $this->picturesTable->insertPicture($picture->getStandardResImage()->url,
                                            $userId,
                                            $picture->getCaption());
        $pictureId = $this->picturesTable->getLastPictureId();
        
        //obtain and save the comments of the picture
        $commentData = $picture->getComments()->getData();
        foreach ($commentData as $comment)
        {
            $this->commentsTable->insertComment($pictureId, $comment->getText());
        }
    }
}

