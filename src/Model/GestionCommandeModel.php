<?php

declare(strict_types=1);

namespace App\Model;

use PDO;
use App\Entity\Commande;
use Tools\Connexion;
use Exception;
use App\Exceptions\AppException;

/**
 * Description of GestionClientModel
 * @author lucas
 */
class GestionCommandeModel
{
    public function find(int $id): Commande
    {
        try {
            $unObjetPdo = Connexion::getConnexion();
            $sql = "select * from COMMANDE where id = :id";
            $ligne = $unObjetPdo->prepare($sql);
            $ligne->bindValue(':id', $id, PDO::PARAM_INT);
            $ligne->execute();
            return $ligne->fetchObject(Commande::class);
        } catch (Exception) {
            throw new AppException("Erreur technique inattendue");
        }
    }

    public function findAll(): array
    {
        $unObjetPdo = Connexion::getConnexion();
        $sql = "select * from CLIENT";
        $lignes = $unObjetPdo->query($sql);
        return $lignes->fetchAll(PDO::FETCH_CLASS, Commande::class);
    }
}
