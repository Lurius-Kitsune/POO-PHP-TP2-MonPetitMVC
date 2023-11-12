<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Client;
use Tools\Connexion;
use App\Exceptions\AppException;
use PDOException;
use Exception;
use PDO;

/**
 * Description of GestionClientModel
 * @author benoi
 */
class GestionClientModel
{
    public function find(int $id): Client
    {
        try {
            $unObjetPdo = Connexion::getConnexion();
            $sql = "select * from CLIENT where id = :id";
            $ligne = $unObjetPdo->prepare($sql);
            $ligne->bindValue(':id', $id, PDO::PARAM_INT);
            $ligne->execute();
            return $ligne->fetchObject(Client::class);
        } catch (Exception) {
            throw new AppException("Erreur technique inattendue");
        }
    }

    public function findAll(): array
    {
        $unObjetPdo = Connexion::getConnexion();
        $sql = "select * from CLIENT";
        $lignes = $unObjetPdo->query($sql);
        return $lignes->fetchAll(PDO::FETCH_CLASS, Client::class);
    }

    public function enregistreClient(Client $client)
    {
        try {
            $unObjetPdo = Connexion::getConnexion();
            $sql = "insert into CLIENT (titleCli, nomCli, prenomCli, adresseRue1li, adresseRue2li, cpCli, villeCli, telCli) 
                    values (:titreCli, :nomCli, :prenomCli, :adresseRue1Cli, :adresseRue2Cli, :cpCli, :villeCli, :telCli)";
            $s = $unObjetPdo->prepare($sql);
            $s->bindValue(':titreCli', $client->getTitreCli(), PDO::PARAM_STR);
            $s->bindValue(':nomCli', $client->getNomCli(), PDO::PARAM_STR);
            $s->bindValue(':prenomCli', $client->getPrenomCli(), PDO::PARAM_STR);
            $s->bindValue(':adresseRue1Cli', $client->getAdresseRue1Cli(), PDO::PARAM_STR);
            $s->bindValue(':adresseRue2Cli', $client->getAdresseRue2Cli(), PDO::PARAM_STR);
            $s->bindValue(':cpCli', $client->getCpCli(), PDO::PARAM_STR);
            $s->bindValue(':villeCli', $client->getVilleCli(), PDO::PARAM_STR);
            $s->bindValue(':telCli', $client->getTelCli(), PDO::PARAM_STR);
            $s->execute();
        } catch (Exception $e) {
            throw new AppException("Erreur technique inattendue");
        }
    }

    public function findids()
    {
        try {
            $unObjetPdo = Connexion::getConnexion();
            $sql = "select id from CLIENT";
            $lignes = $unObjetPdo->query($sql);
            // on va configurer le mode objet pour la lisibilite du code
            if ($lignes->rowCount() > 0) {
                // $lignes->setFetchMode () ;
                $t = $lignes->fetchAll(PDO::FETCH_ASSOC);
                return $t;
            } else {
                throw new AppException('Aucun client trouve');
            }
        } catch (PDOException) {
            throw new AppException("Erreur technique inattendue");
        }
    }
}
