<?php

declare(strict_types=1);

namespace App\Controller;

use ReflectionClass;
use App\Exceptions\AppException;
use App\Model\GestionCommandeModel;

/**
 * Description of GestionClientController
 * @author lucas BRUEL
 */
class GestionCommandeController
{
    public function chercheUne(array $params)
    {
        // appel de la methode find ($id) de la classe Model adequate
        $modele = new GestionCommandeModel();
        $id = filter_var(intval($params["id"]), FILTER_VALIDATE_INT);
        $uneCommande = $modele->find($id);
        if ($uneCommande) {
            $r = new ReflectionClass($this);
            include_once PATH_VIEW . str_replace('Controller', 'View', $r->getShortName()) . "/uneCommande.php";
        } else {
            throw new AppException("Client " . $id . " inconnu");
        }
    }
}
