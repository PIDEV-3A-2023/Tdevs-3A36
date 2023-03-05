function calculMensualite() {
  // Récupération des valeurs des champs de saisie
  var montant = document.getElementById("pret_montant").value;

  var duree = document.getElementById("pret_duree").value;
  var taux = document.getElementById("pret_Taux").value;

  // Calcul de la mensualité
  var mensualite =
    (montant * taux) / 100 / (1 - Math.pow(1 + taux / 100, -duree));
 
  // Affichage de la mensualité
  document.getElementById("mensualite").value = mensualite.toFixed(2);
}
