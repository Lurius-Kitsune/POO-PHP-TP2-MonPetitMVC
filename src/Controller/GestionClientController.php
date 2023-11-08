<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\GestionClientModel;
use ReflectionClass;
use App\Exceptions\AppException;

/**
 * Description of GestionClientController
 * @author benoit ROCHE
 */
class GestionClientController
{
    public function chercheUn(array $params)
    {
        // appel de la methode find ($id) de la classe Model adequate
        $modele = new GestionClientModel();
        $id = filter_var(intval($params["id"]), FILTER_VALIDATE_INT);
        $unClient = $modele->find($id);
        if ($unClient) {
            $r = new ReflectionClass($this);
            include_once PATH_VIEW . str_replace('Controller', 'View', $r->getShortName()) . "/unClient.php";
        } else {
            throw new AppException("Client " . $id . " inconnu");
        }
    }
}
