<?php

declare(strict_types=1);

namespace App\Entity;

// use DateTime;


/**
 * Description of cours
 *
 * @author Benoît
 */
class Commande
{
    private int $id;
    private $dateCde;
    private ?int $noFacture;
    private int $idClient;

    public function __construct($params = null)
    {
        if (!is_null($params)) {
            foreach ($params as $cle => $valeur) {
                if (strlen($valeur) > 0) {
                    $this->$cle = $valeur;
                } else {
                    $this->$cle = null;
                }
            }
        }
    }

    //    public function __set($attribute, $value):void{
    //        if($attribute=="dateCde"){
    //            $this->dateCommande= new DateTime($value);
    //        }
    //    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDateCde()
    {
        //return new DateTime($this->dateCde);
        return $this->dateCde;
    }

    public function getNoFacture(): ?int
    {
        return $this->noFacture;
    }

    public function getIdClient(): int
    {
        return $this->idClient;
    }

    public function setNoFacture(?int $noFacture): void
    {
        $this->noFacture = $noFacture;
    }
}
