<?php

declare(strict_types=1);

namespace App\Repository;

use Tools\Repository;

/**
 * Description of ClientRepository
 *
 * @author Lucas Bruel
 */
class ClientRepository extends Repository {

    public function statistiquesTousClients(): array {
        $sql = "select client.id, client.nomCli, client.prenomCli, client.villeCli from client";
        // $sql = "select client.id, client.nomCli, client.prenomCli, client.villeCli,"
        //         . " count(commande.idClient) as nbCommandes"
        //         . " from client"
        //         . " inner join commande on client.id = commande.idClient"
        //         . " group by client.id, client.nomCli, client.prenomCli, client.villeCli"
        //        . " order by nbCommandes desc, client.nomCli ";
        return $this->executeSQL($sql);
    }
}
