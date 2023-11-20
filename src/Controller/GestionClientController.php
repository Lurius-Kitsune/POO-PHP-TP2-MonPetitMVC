<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Client;
use App\Model\GestionClientModel;
use App\Repository\ClientRepository;
use App\Exceptions\AppException;
use Tools\MyTwig;
use Tools\Repository;
use ReflectionClass;
use Exception;

/**
 * Description of GestionClientController
 * @author benoit ROCHE
 */
class GestionClientController {

    private ClientRepository $repository;

    public function __construct() {
        $this->repository = Repository::getRepository("App\Entity\Client");
    }

    public function chercheUn(array $params) {
        // on recupere tous les id des clients
        $ids = $this->repository->findIds();
        // on place les ids trouves dans le tableal de parametres a envoyer a la vue
        $params ['lesId'] = $ids;
        $params ['ac'] = 'Commande';
        // on teste si l'l'id du client a chercher a ete passe dans l'URL
        if (array_key_exists('id', $params)) {
            $id = filter_var(intval($params ["id"]), FILTER_VALIDATE_INT);
            $unClient = $this->repository->find($id);
            if ($unClient) {
                // le client a ete trouvé
                $params ['unClient'] = $unClient;
            } else {
                //le client a ete cherche mais pas trouve
                $params ['message'] = "Client " . $id . " inconnu";
            }
        }
        $r = new ReflectionClass($this);
        $vue = str_replace('Controller', 'view', $r->getshortName()) . "/unClient.html.twig";
        MyTwig::afficheVue($vue, $params);
    }

    public function chercheTous() {
        // appel de la methode findAll() de la classe Model adequate
        $clients = $this->repository->findAll();
        if ($clients) {
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller', 'View', $r->getshortName()) . "/plusieursClients.html.twig";
            MyTwig::afficheVue($vue, array('clients' => $clients));
        } else {
            throw new AppException("Aucun client a afficher");
        }
    }

    public function creerClient(array $params) {
        if (empty($params)) {
            $vue = "GestionClientView\\creerClient.html.twig";
            MyTwig::afficheVue($vue, array());
        } else {
            try {
                $params = $this->verificationSaisieClient($params);
                //Creation de l'objet client à partir des données du formulaire
                $client = new Client($params);
                $this->repository->insert($client);
                $this->chercheTous();
            } catch (Exception $ex) {
                throw new AppException("Erreur à l'enregistrement d'un nouveau client");
            }
        }
    }

    private function verificationSaisieClient(array $params): array {
        $params["nomCli"] = htmlspecialchars($params["nomCli"]);
        $params["prenomCli"] = htmlspecialchars($params["prenomCli"]);
        $params["adresseRue1Cli"] = htmlspecialchars($params["adresseRue1Cli"]);
        if ($params["adresseRue2Cli"]) {
            $params["adresseRue2Cli"] = htmlspecialchars($params["adresseRue2Cli"]);
        }
        $params["cpCli"] = filter_var($params["cpCli"], FILTER_SANITIZE_NUMBER_INT);
        $params["villeCli"] = htmlspecialchars($params["villeCli"]);
        $params["telCli"] = filter_var($params["telCli"], FILTER_SANITIZE_NUMBER_INT);
        return $params;
    }

    public function nbClients(): void {
        $nbClients = $this->repository->countRows();
        echo "Nombre de client : " . $nbClients;
    }

    private function trieTTStr(array $tableau, string $sousTableau): array {
        usort($tableau, function ($a, $b) use ($sousTableau) {
            return strcmp($a[$sousTableau], $b[$sousTableau]);
        });
        return $tableau;
    }

    private function trieTTIntDesc(array $tableau, string $sousTableau): array {
        usort($tableau, function ($a, $b) use ($sousTableau) {
            return $b[$sousTableau] - $a[$sousTableau];
        });
        return $tableau;
    }

    public function statsClients() {
        // récupération d'un objet ClientRepository
        $clients = $this->repository->statistiquesTousClients();
        $repositoryCommande = Repository::getRepository("App\Entity\Commande");
        $commandes = $repositoryCommande->findAll();
        for ($i = 0; $i < count($clients); $i++) {
            $nbCommande = 0;
            foreach ($commandes as $commande) {
                if ($commande->getIdClient() == $clients[$i]["id"]) {
                    $nbCommande += 1;
                }
            }
            $clients[$i]['nbCommandes'] = $nbCommande;
        }
        $clientsTrie = $this->trieTTIntDesc($this->trieTTStr($clients, "nomCli"), "nbCommandes");
        if ($clientsTrie) {
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller', 'View', $r->getShortName()) . "\statsClient.html.twig";
            MyTwig::afficheVue($vue, array('clients' => $clientsTrie));
        } else {
            throw new AppException("Aucun clients");
        }
    }

    public function testFindBy(): void {
        $parametres = array('titreCli' => 'Madame', 'cpCli' => '14000');
        $clients = $this->repository->findBytitreCli_and_cpCli($parametres);
        $r = new ReflectionClass($this);
        $vue = str_replace('Controller', 'View', $r->getShortName()) . "/plusieursClients.html.twig";
        MyTwig::afficheVue($vue, array('clients' => $clients));
    }

    public function rechercheClients(array $params): void {
        $titres = $this->repository->findColumnDistinctValues('titreCli');
        $cps = $this->repository->findColumnDistinctValues('cpCli');
        $villes = $this->repository->findColumnDistinctValues('villeCli');
        $paramsVue['titres'] = $titres;
        $paramsVue['cps'] = $cps;
        $paramsVue['villes'] = $villes;
        // Gestion du retour du formulaire
        // On va d'abord filtrer et préparer le retour du formulaire avec la fonction verifieEtPrepareCriteres
        $criteresPrepares = $this->verifieEtPrepareCriteres($params);
        if (count($criteresPrepares) > 0) {
            $clients = $this->repository->findBy($params);
            $paramsVue['clients'] = $clients;
            foreach ($criteresPrepares as $valeur) {
                if ($valeur != "Choisir...") {
                    (($valeur != "Choisir...") ? ($criteres[] = $valeur) : null);
                }
            }
            $paramsVue['criteres'] = $criteres;
        }
        $vue = "GestionClientView\\filtreClients.html.twig";
        MyTwig::afficheVue($vue, $paramsVue);
    }

    private function verifieEtPrepareCriteres(array $params): array {
        $args = array(
            'titreCli' => array(
                'filter' => FILTER_VALIDATE_REGEXP | FILTER_SANITIZE_SPECIAL_CHARS,
                'flags' => FILTER_NULL_ON_FAILURE,
                'options' => array('regexp' => '/^(Monsieur|Madame|Mademoiselle)$/'
                )),
            'cpCli' => array(
                'filter' => FILTER_VALIDATE_REGEXP | FILTER_SANITIZE_SPECIAL_CHARS,
                'flags' => FILTER_NULL_ON_FAILURE,
                'options' => array('regexp' => "/[0-9]{5}/"
                )),
            'villeCli' => FILTER_SANITIZE_SPECIAL_CHARS
        );
        $retour = filter_var_array($params, $args, false);
        if (isset($retour['titreCli']) || isset($retour['cpCli']) || isset($retour['villeCli'])) {
            // c'est le retour du formulaire de choix de filtre
            $element = "Choisir ... ";
            while (in_array($element, $retour)) {
                unset($retour[array_search($element, $retour)]);
            }
        }
        return $retour;
    }
}
