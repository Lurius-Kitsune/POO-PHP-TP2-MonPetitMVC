<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Client;
use App\Model\GestionClientModel;
use ReflectionClass;
use Exception;
use App\Exceptions\AppException;
use Tools\MyTwig;

/**
 * Description of GestionClientController
 * @author benoit ROCHE
 */
class GestionClientController {

    public function chercheUn(array $params) {
        $modele = new GestionClientModel();
        // on recupere tous les id des clients
        $ids = $modele->findIds();
        // on place les ids trouves dans le tableal de parametres a envoyer a la vue
        $params ['lesId'] = $ids;
        // on teste si l'l'id du client a chercher a ete passe dans l'URL
        if (array_key_exists('id', $params)) {
            $id = filter_var(intval($params ["id"]), FILTER_VALIDATE_INT);
            $unClient = $modele->find($id);
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
        $modele = new GestionClientModel();
        $Clients = $modele->findAll();
        if ($Clients) {
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller', 'View', $r->getshortName()) . "/plusieursClients.html.twig";
            MyTwig::afficheVue($vue, array('Clients' => $Clients));
        } else {
            throw new AppException("Aucun client a afficher");
        }
    }

    public function creerClient(array $params) {
        $vue = "GestionClientView\\creerClient.html.twig";
        MyTwig::afficheVue($vue, array());
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
}
