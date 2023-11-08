<?php

include_once PATH_VIEW . "header.html";
echo "<p>Nombre de commandes trouvés : " . count($commandes) . "</p>";

foreach ($commandes as $commande) {
    echo $commande->getId() . " - " . $commande->getDateCde() . " - " . ($commande->getNoFacture() ?? "Non facturé") . " - " . $commande->getIdClient() . "<br>";
}

include_once PATH_VIEW . "footer.html";
