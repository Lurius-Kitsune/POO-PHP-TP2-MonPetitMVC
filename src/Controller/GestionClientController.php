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
        $Clients = $this->repository->findAll();
        if ($Clients) {
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller', 'View', $r->getshortName()) . "/plusieursClients.html.twig";
            MyTwig::afficheVue($vue, array('Clients' => $Clients));
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

    public function enregistreClient(array $params) {
        try {
            $client = new Client($params);
            $modele = new GestionClientModel();
            $modele->enregistreClient($client);
        } catch (Exception $e) {
            throw new AppException("Erreyr à l'enregistrement d'un nouveau client");
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
}
