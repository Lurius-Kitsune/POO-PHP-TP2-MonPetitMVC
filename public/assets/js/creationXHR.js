function creationXHR() {
    var resultat = null;
    try {//test pour les navigateurs : Mozilla, Opera,
        resultat = new XMLHttpRequest();
    } catch (Erreur) {
        try {//test pour les navigateurs Internet Explorer > 5.0
            resultat = new ActiveXObject("Msxm12. XMLHTTP");
        } catch (Erreur) {
            try {//test pour le navigateur Internet Explorer 5.0
                resultat = new ActiveXObject("Microsoft. XMLHTTP");
            } catch (Erreur) {
                resultat = null;
            }
        }
    }
    return resultat;
}