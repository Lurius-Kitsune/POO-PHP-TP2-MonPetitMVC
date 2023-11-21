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

    public function countRows(): int {
        $sql = "select count(*) from " . $this->table;
        $nbLignes = $this->connexion->query($sql);
        return (int) $nbLignes->fetch(PDO::FETCH_NUM)[0];
    }

    public function findBy(array $params) {
        $element = "Choisir ...";
        while (in_array($element, $params)) {
            unset($params [array_search($element, $params)]);
        }
        $cles = array_keys($params);
        $methode = "findBy";
        for ($i = 0; $i < count($cles); $i++) {
            if ($i > 0) {
                $methode .= "_and_";
            }
            $methode .= $cles [$i];
        }
        return $this->traiteFindBy($methode, array_values($params));
    }

    public function executeSQL(string $sql): ?array {
        $resultat = $this->connexion->query($sql);
        return $resultat->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getRepository(string $entity): Repository {
        $repositoryName = str_replace('Entity', 'Repository', $entity) . 'Repository';
        $repository = new $repositoryName($entity);
        return $repository;
    }

    public function __call(string $methode, array $params) {
        if (preg_match("#^findBy#", $methode)) {
            return $this->traiteFindBy($methode, array_values($params[0]));
        }
    }

    private function traiteFindBy(string $methode, array $params): array {
        $criteres = str_replace("findBy", "", $methode);
        $criteres = explode("_and_", $criteres);
        if (count($criteres) > 0) {
            $sql = 'select * from ' . $this->table . " where ";
            $pasPremier = false;
            foreach ($criteres as $critere) {
                if ($pasPremier) {
                    $sql .= " and ";
                }
                $sql .= $critere . " = ? ";
                $pasPremier = true;
            }
            $lignes = $this->connexion->prepare($sql);
            $lignes->execute($params);
            $lignes->setFetchMode(PDO::FETCH_CLASS, $this->classeNameLong, null);
            return $lignes->fetchAll();
        }
    }

    public function findColumnDistinctValues(string $colonne): array {
        $sql = "select distinct " . $colonne . " as libelle from " . $this->table . " order by 1";
        //return $this->connexion->query ($sql)->fetchAll (PDO : : FETCH_ASSOC) ;
        $tab = $this->connexion->query($sql)->fetchAll(PDO::FETCH_COLUMN);
        return $tab;
    }

    public function modifieTable($objet) {
        $tobjet = $this->object2Array($objet);
        $parametres = array();
        $sql = "update " . $this->table . " set ";
        foreach ($tobjet as $cle => $valeur) {
            if ($cle != "id") {
                if ($this->gereNull($valeur)) {
                    $sql .= $cle . "= null ,";
                } else {
                    $sql .= $cle . "= :" . $cle . " ,";
                    $parametres [$cle] = $valeur;
                }
            }
        }
        $sql = substr($sql, 0, -1) . " where id =" . $tobjet['id'];
        $unObjetPDO = Connexion::getConnexion();
        $req = $unObjetPDO->prepare($sql);
        $req->execute($parametres);
    }

    // convertit un objet de la classe correspondant au Repository en tableau
    // exemple un objet de la classe Client dans le repository ClientRepository
    public function object2Array($objet) {
        $tobjet = (array) $objet;
        $tabloRetour = array();
        foreach ($tobjet as $cle => $valeur) {
            $cle = str_replace("\0", "", $cle);
            $cle = str_replace($this->classeNameLong, "", $cle);
            $tabloRetour [$cle] = $valeur;
        }
        return $tabloRetour;
    }

    private function gereNull($variable) {
        $retour = false;
        if ($variable == '_null_' || $variable == "0") {
            $retour = true;
        }
        return $retour;
    }
}
