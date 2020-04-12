<?php
// entity/Competence.php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="competence")
 */
class Competence
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
  
    /** 
     * @ORM\Column(type="string") 
     */
    protected $Code;
  
    /** 
      * @ORM\Column(type="string")
      */
    protected $libelle;

    /**
     * @ORM\ManyToMany(targetEntity="Tache",mappedBy="competences")
     */
    protected $taches;
  
  

   /**
     * Constructor
     */
    public function __construct()
    {
        $this->taches = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code.
     *
     * @param string $code
     *
     * @return Competence
     */
    public function setCode($code)
    {
        $this->Code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->Code;
    }

    /**
     * Set libelle.
     *
     * @param string $libelle
     *
     * @return Competence
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle.
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Add tach.
     *
     * @param \Entity\Tache $tach
     *
     * @return Competence
     */
    public function addTache(\Entity\Tache $tache)
    {
        $this->taches[] = $tache;

        return $this;
    }

    /**
     * Remove tach.
     *
     * @param \Entity\Tache $tach
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTache(\Entity\Tache $tache)
    {
        return $this->taches->removeElement($tache);
    }

    /**
     * Get taches.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaches()
    {
        return $this->taches;
    }
}