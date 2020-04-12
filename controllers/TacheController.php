<?php

namespace Controllers;

use Entity\Tache;
use Entity\User;
use Entity\Competence;
use Entity\Request;

require 'bootstrap.php';


class TacheController extends Controller

{
  
  public function new($request) 
  {
    $competence = $request->getEm()->getRepository('Entity\Competence')->find($_GET["competenceId"]);
    
     echo $this->twig->render('newTache.html', 
         [
          "competence" => $competence
         ]);
  }
  
  public function create($request)
  {
    if ($_POST != NULL) { 
        
        $user = $request->getUser();
        $competence = $request->getEm()->getRepository('Entity\Competence')->find($_GET['competenceId']); // on definit la variable competence comme etant l'objet competence qui a pour parametre un id recuperer dans l'url
        $dt = new \DateTime($_POST['date']); //on instancie un nouvel objet date pour pouvoir le traiter comme tel et non comme un string
        $tache = new Tache;
        $tache->setDescription($_POST['description_tache']);
        $tache->setTitre($_POST['titre_tache']);
        $tache->setDate($dt);
        $tache->addCompetence($competence); // on attribue la tache a l'id de la competence concernée (permet d'avoir plusieurs taches associées au même id)
        $user->addTache($tache);
      
        $request->getEm()->persist($tache);
        $request->getEm()->flush();
        
//       header('location : http://195.154.118.169/felix/tpmvc/?c=competence&t=list');
//         echo $this->twig->render('listCompetence.html'); ////////////////////// PROBLEME DE REDIRECTION A RESOUDRE \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
        
       }
    else {
            echo "Le formulaire est nul";
         }  
   
  } 
  
  public function edit($request)
  {
    $competence = $request->getEm()->getRepository('Entity\Competence')->find($_GET["competenceId"]);
    $tache = $request->getEm()->getRepository('Entity\Tache')->find($_GET["tacheId"]);
    
    echo $this->twig->render('editTache.html',
                            [
                              "competence" => $competence,
                              "tache" => $tache,
                            ]);
  }
    
  public function edited($request) 
  {
    
    $competence = $request->getEm()->getRepository('Entity\Competence')->find($_GET['competenceId']); // on definit la variable competence comme etant l'objet competence qui a pour parametre un id recuperer dans l'url
    $tache = $request->getEm()->getRepository('Entity\Tache')->find($_GET['tacheId']); // id de la tache
    $dt = new \DateTime($_POST['date_tache']);
    
    $tache->setTitre($_POST['titre_tache']);
    $tache->setDescription($_POST['description_tache']);
    $tache->setDate($dt);
    
    $request->getEm()->persist($tache);
    $request->getEm()->flush();
    
  }
  public function delete($request) 
  {
    $tache = $request->getEm()->getRepository('Entity\Tache')->find($_GET["tacheID"]);
    
    if ($_GET["tacheID"] == NULL) {
       http_response_code(500);
    } 
    

    else   {        
     $tache = $request->getEm()->getRepository('Entity\Tache')->find($_GET["tacheID"]);
     $request->getEm()->remove($tache);
     $request->getEm()->flush();
     http_response_code(200);
    }
  }
  
 public function showTache($request)
 {
   $tache = $request->getEm()->getRepository('Entity\Tache')->find($_GET["tacheID"]);

   echo $this->twig->render('showTache.html', [
     "tache" => $tache
   ]);
   
 }
}