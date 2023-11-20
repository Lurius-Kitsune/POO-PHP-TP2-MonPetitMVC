<?php

namespace Tools;

use PDO;

/**
 * Description of Repository
 *
 * @author Lucas Bruel
 */
abstract class Repository {

    private string $classeNameLong;
    private string $classeNamespace;
    private string $table;
    private PDO $connexion;

    private function __construct(string $entity) {
        $tablo = explode("\\", $entity);
        $this->table = array_pop($tablo);
        $this->classeNamespace = implode("\\", $tablo);
        $this->classeNameLong = $entity;
        $this->connexion = Connexion::getConnexion();
    }

    public function findAll(): array {
        $sql = "select * from " . $this->table;
        $lignes = $this->connexion->query($sql);
        $lignes->setFetchMode(PDO::FETCH_CLASS, $this->classeNameLong, null);
        return $lignes->fetchAll();
    }

    public function findIds(): array {
        try {
            $unObjetPdo = Connexion::getConnexion();
            $sql = "select id from $this->table";
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

    public function find(int $id): ?object {
        try {
            $unObjetPdo = Connexion::getConnexion();
            $sql = "select * from $this->table where id = :id";
            $ligne = $unObjetPdo->prepare($sql);
            $ligne->bindValue(':id', $id, PDO::PARAM_INT);
            $ligne->execute();
            return $ligne->fetchObject($this->classeNameLong);
        } catch (Exception) {
            throw new AppException("Erreur technique inattendue");
        }
    }

    public function insert(object $object): void {
        // conversion d'un objet en tableau
        $attributs = (array) $object;
        array_shift($attributs);
        $colonnes = "(";
        $colonnesParams = "(";
        $parametres = array();
        foreach ($attributs as $cle => $valeur) {
            $cle = str_replace("\0", "", $cle);
            $c = str_replace($this->classeNameLong, "", $cle);
            if ($c != "id") {
                $colonnes .= $c . " ,";
                $colonnesParams .= " ? ,";
                $parametres[] = $valeur;
            }
        }
        $cols = substr($colonnes, 0, -1);
        $colsParams = substr($colonnesParams, 0, -1);
        $sql = "insert into " . $this->table . " " . $cols . ") values " . $colsParams . ") ";
        $unObjetPDO = Connexion::getConnexion();
        $req = $unObjetPDO->prepare($sql);
        $req->execute($parametres);
    }

    public static function getRepository(string $entity): Repository {
        $repositoryName = str_replace('Entity', 'Repository', $entity) . 'Repository';
        $repository = new $repositoryName($entity);
        return $repository;
    }
}
