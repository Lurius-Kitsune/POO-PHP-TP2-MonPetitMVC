function filtrerClient()
{
    if (titres.value == "Choisir ... " && villes.value == "Choisir ... " && cps.value == "Choisir ... ") {
        document.getElementById("retour").style.visibility = "hidden";
    } else {
        var xhr = new XMLHttpRequest()
        var reponse;
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('retour').innerHTML = xhr.responseText;
                document.getElementById("retour").style.visibility = "visible";
            }
        }
        xhr.open("post", "/?c=gestionClient&a=rechercheClientsAjax", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        parametres = "titreCli=" + document.getElementById("titres").value;
        parametres += "&villeCli=" + document.getElementById("villes").value;
        parametres += "&cpCli=" + document.getElementById("cps").value;
        xhr.send(parametres);
    }
}

var titres = document.getElementById("titres");
titres.addEventListener('change', filtrerClient, false);
var villes = document.getElementById("villes");
villes.addEventListener('change', filtrerClient, false);
var cps = document.getElementById("cps");
cps.addEventListener('change', filtrerClient, false);


        