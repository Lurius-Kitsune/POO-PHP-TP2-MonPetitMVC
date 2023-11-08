<?php

declare(strict_types=1);

namespace App\Entity;

class Client
{
    private int $id;
    private string $titreCli;
    private string $nomCli;
    private string $prenomCli;
    private string $adresseRue1Cli;
    private ?string $adresseRue2Cli;
    private string $cpCli;
    private string $villeCli;
    private string $telCli;

    public function __construct(array $params = null)
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

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitreCli(): string
    {
        return $this->titreCli;
    }

    public function getNomCli(): string
    {
        return filter_var($this->nomCli, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        //return $this->nomCli;
    }

    public function getPrenomCli(): string
    {
        return $this->prenomCli;
    }

    public function getAdresseRue1Cli(): string
    {
        return $this->adresseRue1Cli;
    }

    public function getAdresseRue2Cli(): ?string
    {
        return $this->adresseRue2Cli;
    }

    public function getCpCli(): string
    {
        return $this->cpCli;
    }

    public function getVilleCli(): string
    {
        return $this->villeCli;
    }

    public function getTelCli(): string
    {
        return $this->telCli;
    }

    public function __toString(): string
    {
        return "Le client dont le numéro est égal à " . $this->id . " s'appelle " . $this->titreCli . " " . $this->nomCli . " " . $this->prenomCli . "<br>";
    }

    public function setAdresseRue1Cli(string $adresseRue1Cli): void
    {
        $this->adresseRue1Cli = $adresseRue1Cli;
    }

    public function setAdresseRue2Cli(string $adresseRue2Cli): void
    {
        $this->adresseRue2Cli = $adresseRue2Cli;
    }

    public function setCpCli(string $cpCli): void
    {
        $this->cpCli = $cpCli;
    }

    public function setVilleCli(string $villeCli): void
    {
        $this->villeCli = $villeCli;
    }

    public function setTelCli(string $telCli): void
    {
        $this->telCli = $telCli;
    }
}
