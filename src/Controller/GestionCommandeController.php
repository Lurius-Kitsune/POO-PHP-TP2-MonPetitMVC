<?php

declare(strict_types=1);

namespace App\Controller;

use ReflectionClass;
use App\Exceptions\AppException;
use App\Model\GestionCommandeModel;
use App\Repository\CommandeRepository;
use Tools\Repository;
use Tools\MyTwig;

/**
 * Description of GestionClientController
 * @author lucas BRUEL
 */
class GestionCommandeController {

    private CommandeRepository $repository;

    public function __construct() {
        $this->repository = Repository::getRepository("App\Entity\Commande");
    }

    public function chercheUne(array $params) {
        // on recupere tous les id des commandes
        $ids = $this->repository->findIds();
        // on place les ids trouves dans le tableal de parametres a envoyer a la vue
        $params ['lesId'] = $ids;
        $params ['ac'] = 'Commande';
        // on teste si l'id de la commande a chercher a ete passe dans l'URL
        if (array_key_exists('id', $params)) {
            $id = filter_var(intval($params ["id"]), FILTER_VALIDATE_INT);
            $uneCommande = $this->repository->find($id);
            if ($uneCommande) {
                // le client a ete trouvé
                $params ['uneCommande'] = $uneCommande;
            } else {
                //le client a ete cherche mais pas trouve
                $params ['message'] = "Commande " . $id . " inconnu";
            }
        }
        $r = new ReflectionClass($this);
        $vue = str_replace('Controller', 'view', $r->getshortName()) . "/uneCommande.html.twig";
        MyTwig::afficheVue($vue, $params);
    }

    public function chercheToutes() {
        // appel de la methode findAll() de la classe Model adequate
        $modele = new GestionCommandeModel();
        $commandes = $modele->findAll();
        if ($commandes) {
            $r = new ReflectionClass($this);
            include_once PATH_VIEW . str_replace('Controller', 'View', $r->getshortName()) . "/plusieursCommande.php";
        } else {
            throw new AppException("Aucun client a afficher");
        }
    }
}
