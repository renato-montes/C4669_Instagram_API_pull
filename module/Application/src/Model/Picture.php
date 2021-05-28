<?php

namespace Application\Model;

class Picture
{
    private $id;
    private $authorName;
    private $title;
    private $description;
    private $filename;
    private $instagramUrl;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function setInstagramUrl($url)
    {
        $this->instagramUrl = $url;
    }

    public function clear()
    {
        $this->id = NULL;
        $this->authorName = NULL;
        $this->title = NULL;
        $this->description = NULL;
        $this->filename = NULL;
    }

    public function getData()
    {
        $data = [];
        $data['id'] = $this->id;
        $data['authorName'] = $this->authorName;
        $data['pictureTitle'] = $this->title;
        $data['pictureDescription'] = $this->description;
        $data['filename'] = $this->filename;
        $data['pictureInstagram'] = $this->instagramUrl;
        return $data;
    }
}

