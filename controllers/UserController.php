<?php

namespace Controllers;

use Entity\User;
use Entity\Request;

require 'bootstrap.php';

class UserController extends Controller

{
    public function inscription($request) // renvoie le formulaire pour s'inscrire
    {
        echo $this->twig->render('inscription.html');
    }
  
    public function checkEmail($email) // verifie si l'email est de la bonne forme
    {
       $result = 0;
       if (preg_match ( " /^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/ " , $email))
       {
         $result = 1;
       }
       return $result;
    }
   
    public function checkMdp($decoded_data) // verifie si les deux mots de passe sont identiques
    {
       $result = 0;
       if ($decoded_data['motDePasse'] == $decoded_data['motDePasseVerif'])
       {
         $result = 1;
       }
       return $result;
    }
  
    public function inscriptionValid($request) { // creer un nouvel utilisateur si toutes les conditions sont remplis
      
      $content_raw = file_get_contents("php://input"); // ON RECUPERE LES DONNEES DES INPUTS 
      $decoded_data = json_decode($content_raw, true);
      $email = $decoded_data['email'];
      $status = array(
        200 => '200 OK',
        400 => '400 Bad Request',
        403 => "Invention",
        404 => "Not found",
        422 => 'Unprocessable Entity',
        500 => '500 Internal Server Error'
        );
      $code=403;
      $message="Il semblerait que vous ne soyez pas encore inscrit..";
    
    
       if ((!$decoded_data['identifiant']) OR (!$decoded_data['motDePasse']) OR (!$decoded_data['motDePasseVerif']) OR (!$decoded_data['email'])) // le "!" vient verifier si les données sont fausses (si pas de données alors vaut false)
        { 
          $message="Merci de remplir tous les champs";
          $code=500;
        }  
       
       elseif ($request->getEm()->getRepository('Entity\User')->findOneBy(['email'=>$decoded_data['email']])) // si l'email est déja utilisé (donc qu'il le trouve dans la bdd) ->erreur
       { 
         $message="un compte existe deja pour cette adresse mail";
         $code=500;
       }
      
       elseif ($request->getEm()->getRepository('Entity\User')->findOneBy(['identifiant'=>$decoded_data['identifiant']])) // si l'identifiant existe deja -> erreur
       { 
         $message="cet identifiant est déja utilisé";
         $code=500;
       }
         
      elseif ($this->checkMdp($decoded_data) == 1 && $this->checkEmail($decoded_data["email"]) == 1) // creer l'utilisateur si les conditions sont bien remplis
      {
        
        $dt = new \DateTime(date("Y/m/d"));
        
        $user = new User;
        $user->setIdentifiant($decoded_data['identifiant']);
        $user->setMdp($decoded_data['motDePasse']);
        $user->setEmail($decoded_data['email']);
        $user->setDateInscription($dt);
        $user->setRole('utilisateur');
        
        $request->getEm()->persist($user);
        $request->getEm()->flush();
        
        $message="Inscription reussie";
        $code=200;
          
      }
          
      elseif ($this->checkMdp($decoded_data) == 0) // erreur de mot de passe
      {
           $message="Les mots de passe ne sont pas identiques";
           $code=403;
//         echo "<script>alert('ERREUR - les mots de passe ne sont pas identiques')</script>";
//         echo $this->twig->render('inscription.html');
      }
      
      elseif ($this->checkEmail($email) == 0) // erreur d'email
      {
           $message="l'adresse mail n'est pas au bon format - Exemple de format valide : test@test.com";
           $code=403;
//         echo "<script>alert('ERREUR - l\'adresse mail n\'est pas au bon format - Exemple de format valide : test@test.com')</script>";
//         echo $this->twig->render('inscription.html');
      }
    
      else {
        http_response_code($code);
        echo json_encode(array(
            'status' => $status[$code],
            'message' => $message,
        ));
      } 
      

        http_response_code($code);
        echo json_encode(array(
            'status' => $status[$code],
            'message' => $message,
        ));
  
    
    }
     
    public function connexion($request) // renvoie le formulaire de connexion
    { 
       echo $this->twig->render('connexion.html');
    } 
  
    public function connexionValid($request) {
      
      $content_raw = file_get_contents("php://input"); // ON RECUPERE LES DONNEES DES INPUTS 
      $decoded_data = json_decode($content_raw, true);
      $status = array(
        200 => '200 OK',
        400 => '400 Bad Request',
        403 => "Invention",
        404 => "Not found",
        422 => 'Unprocessable Entity',
        500 => '500 Internal Server Error'
        );
      $code=403;
      $message="Il semblerait que vous ne soyez pas encore inscrit..";
      $user = $request->getEm()->getRepository('Entity\User')->findOneBy(['identifiant'=>$decoded_data['identifiant']]);

      // ON TEST SI LES CHAMPS NE SONT PAS VIDE //
      
      if ((!$decoded_data['identifiant']) OR (!$decoded_data['motDePasse'])) // le "!" vient verifier si les données sont fausses (si pas de données alors vaut false)
        { 
          $message="Merci de remplir tous les champs";
          $code=500;
        }
        
      // A PARTIR D'ICI ON TEST SI L'IDENTIFIANT EST DANS LA BDD //  
      
      if ($user) {
        
        // TEST SI LE MDP CORRESPOND A L'IDENTIFIANT //
        if ($user->getMdp() == $decoded_data['motDePasse']) {
            $_SESSION['id'] = $user->getId();
            $message="Tout est bon";
            $code=200;
        }
        
        // SI LE MOT DE PASSE NE CORRESPOND PAS A L'IDENTIFIANT //
        else {
             $message="Identifant ou mot de passe incorrect";
             $code=403;
        }
        
        // ON RETOURNE LA REPONSE //
        http_response_code($code);
        echo json_encode(array( // return the encoded json
            'status' => $status[$code], // success or not?
            'message' => $message
        ));
      } 
      
        // SI L'IDENTIFIANT N'EST PAS DANS LA BDD //
      else {
        http_response_code($code);
        echo json_encode(array(
            'status' => $status[$code], // success or not?
            'message' => $message,
        ));
      } 
      
     }
  
    public function disconnect($request)
    {
//       $user = $request->getEm()->getRepository('Entity\User')->find($_SESSION['id']);
//       $request->getEm()->persist($user);
//       $request->getEm()->flush();
      $_SESSION = array();
      session_destroy();
      http_response_code(200);
    } 
  
  public function profil($request) 
  {
    $user = $request->getUser();
    $competences = $request->getEm()->getRepository('Entity\Competence')->findAll();
    $taches = $user->getTaches();
    $tache = $request->getEm()->getRepository('Entity\Tache');
    
    echo $this->twig->render('profil.html', [
      'user' => $user,
      'competences' => $competences,
      'tachesUser' => $taches,
    ]);
  }
  
  public function githubAdd($request)
  {
  $user = $request->getUser();
  $content_raw = file_get_contents("php://input"); // ON RECUPERE LES DONNEES DES INPUTS 
  $decoded_data = json_decode($content_raw, true);
  $githubLink = $decoded_data['githubLink'];
  
  $user->setGithub($githubLink);
  $request->getEm()->persist($user);
  $request->getEm()->flush();
  }
  
  
}  
 
    