<?php

class Article {
    private $container;

    public function __constructor($container) { 
        $this->container = $container;
    }

    public function addArticle() : bool{
        return false;
    }    

    public function displayArticle(){

    }

    public function editArticle() : bool{
        return false;
    }

    public function deleteArticle() : bool{
        return false;
    }
}