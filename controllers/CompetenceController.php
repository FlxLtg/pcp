<?php

namespace Controllers;

use Entity\Competence;
use Entity\Tache;
use Entity\User;
use Entity\Request;

require 'bootstrap.php';


class CompetenceController extends Controller
{  
  
  public function list($request)
  { 
      $competences = $request->getEm()->getRepository('Entity\Competence')->findAll();
      $allTaches = $request->getEm()->getRepository('Entity\Tache')->findAll();
      $user = $request->getUser();
      $userTaches = $user->getTaches();
  
      echo $this->twig->render('listCompetence.html',
        [
          "competences" => $competences,
          "userTaches" => $userTaches,
          "user" => $user,
        ]
      );
  }
  
  public function new($request) 
  {
     echo $this->twig->render('newCompetence.html',);
  }
  
  public function create($request)
  {
    if ($request->getPost() != NULL) { 
        
        $competence = new Competence;
        $competence->setCode($_POST['code_competence']);
        $competence->setLibelle($_POST['libelle_competence']);
      
        $request->getEm()->persist($competence); // on dit à doctrine de gérer a present l'objet
        $request->getEm()->flush(); // on lui dit d'enregistrer dans la bdd
        
        echo $this->twig->render('listCompetence.html',);
       }
    else {
            echo "Le formulaire est nul";
         }  
   
  }
  

  
  
  public function edit($request) 
      
  {
    $competence = $request->getEm()->getRepository('Entity\Competence')->find($_GET["competenceId"]);
    
     echo $this->twig->render('editCompetence.html',
     [
       "competence" => $competence
     ]);
  }
  
  public function delete($request) 
  {
     $competence = $request->getEm()->getRepository('Entity\Competence')->find($_GET["competenceID"]);
     $taches = $competence->getTaches();
       
     $request->getEm()->remove($competence);
     $request->getEm()->flush();
     http_response_code(200);
  }
  
}  