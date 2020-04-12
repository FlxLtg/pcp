<?php


namespace Controllers;

use Entity\Request;

require 'bootstrap.php';

class IndexController extends Controller
{
    public function index()
    {
        echo $this->twig->render('index.html');
    }
  
    public function connexion($request)
    {
        echo $this->twig->render('connexion.html');
    }
  
    public function inscription($request)
    {
        echo $this->twig->render('inscription.html');
    }
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
}

