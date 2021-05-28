<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Validator\Digits;

use Application\Model\Picture;
use Application\Model\Author;
use Application\Model\Comment;

class PictureController extends AbstractActionController
{
    const PAGINATION_LIMIT = 8;
    
    private $picturesTable = NULL;
    private $commentsTable = NULL;

    public function __construct($picturesTable, $commentsTable)
    {
        $this->picturesTable = $picturesTable;
        $this->commentsTable = $commentsTable;
    }

    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function picturesAction()
    {
        $queryParameters['start'] = 0; //begin from first picture
        $queryParameters['count'] = self::PAGINATION_LIMIT;
        return $this->picturesInRangeAction($queryParameters);
    }

    public function picturesInRangeAction($queryParameters = NULL)
    {
        //obtain and validate GET parameters
        if (!isset($queryParameters))
        {
            $queryParameters = $this->getPicturesQueryParameters();
        }
        if (!$this->picturesQueryIsValid($queryParameters))
        {
            return new JsonModel([]);
        }
        
        //use GET params to get query result of picture data from PicturesTable
        $queriedPictures = $this->picturesTable
                                ->getPictureRange($queryParameters['start'],
                                                  $queryParameters['count']);
        
        //create each Picture object, hydrate it and obtain the array
        //of its data with the correct JSON keys
        $data = $this->getPictureData($queriedPictures);
        
        return new JsonModel($data);
    }

    public function commentsAction()
    {
        $data = [];
        $result = ['comments' => &$data];
        $pictureId = $this->getRequest()->getQuery('pictures_id');
        if (!$this->commentsQueryIsValid($pictureId))
        {
            return new JsonModel($result); //early return
        }
        
        $queriedComments = $this->commentsTable
                                ->getCommentsByPictureId($pictureId);

        $comment = new Comment(); //object pooling
        foreach ($queriedComments as $queriedComment)
        {
            $comment->setComment($queriedComment['comments_comment']);
            $data[] = ['comment' => $comment->getComment()];
            $comment->clear();
        }
        return new JsonModel($result);
    }

    public function searchAction()
    {
        $word = $this->getRequest()->getQuery('word');
        if (!isset($word))
        {
            return new JsonModel([]);
        }
        
        //use GET param to get query result of picture data from PicturesTable
        $queriedPictures = $this->picturesTable->getPicturesByTitle($word);
        
        //create each Picture object, hydrate it and obtain the array
        //of its data with the correct JSON keys
        $data = $this->getPictureData($queriedPictures);
        
        return new JsonModel($data);
    }
    
    public function getPicturesQueryParameters()
    {
        $params = [];

        $request = $this->getRequest();
        $params['start'] = $request->getQuery('start');
        $params['count'] = $request->getQuery('count');
        
        return $params;
    }

    public function picturesQueryIsValid($queryParameters)
    {
        //if a GET parameter is missing, the query is invalid
        //(NOTE: if both were originally missing, default values are being used)
        if (!isset($queryParameters['start']) || !isset($queryParameters['count']))
        {
            return FALSE;
        }

        //validate GET parameters so that they are integers
        $digitsValidator = new Digits();
        if (!$digitsValidator->isValid($queryParameters['start']) ||
            !$digitsValidator->isValid($queryParameters['count']))
        {
            return FALSE;
        }
        
        //?count=0 is an invalid query (but ?start=0 refers to the first
        //picture in the database and is therefore valid!)
        if ($queryParameters['count'] == 0)
        {
            return FALSE;
        }
        
        return TRUE;
    }
    
    public function getPictureData($queriedPictures)
    {
        $data = [];
        $picture = new Picture(); //object pooling
        $author = new Author(); //object pooling
        foreach ($queriedPictures as $queriedPicture)
        {
            // hydration of the Author of the Picture
            if (isset($queriedPicture['authors_firstname'])) { $author->setFirstName($queriedPicture['authors_firstname']); }
            if (isset($queriedPicture['authors_lastname'])) { $author->setLastName($queriedPicture['authors_lastname']); }
            
            // hydration of the properties of Picture
            $picture->setAuthorName($author->getFullName());
            $author->clear();
            $picture->setId($queriedPicture['pictures_id']);
            $picture->setTitle($queriedPicture['pictures_title']);
            $picture->setDescription($queriedPicture['pictures_description']);
            $picture->setFilename($queriedPicture['pictures_filename']);
            $picture->setInstagramUrl($queriedPicture['pictures_instagram']);
            
            $data[] = $picture->getData();
            $picture->clear();
        }
        return $data;
    }
    
    public function commentsQueryIsValid($pictureId)
    {
        if (!isset($pictureId))
        {
            return FALSE;
        }
        
        $digitsValidator = new Digits();
        return $digitsValidator->isValid($pictureId);
    }
}

