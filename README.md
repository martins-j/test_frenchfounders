# test_frenchfounders

## Base de données

Le fichier AppFixtures.php sur le répertoire src/DataFixtures permet d'insérer de fausses données en base de données. Dans un premier temps, exécutez la commande suivante :

`php bin/console doctrine:fixtures:load`

---

## Tester l'envoie d'e-mails avec MailDev

MailDev fonctionne avec NodeJS. Si vous ne l'avez pas déjà fait, vous devez commencer par installer NodeJS sur votre machine et ainsi avoir la commande npm de disponible.
Puis installez MailDev (utilisez `sudo` si nécessaire) :

`npm install -g maildev`

Une fois installé, ne reste plus qu’à le lancer :

`maildev --hide-extensions STARTTLS`

L'interface est accesible sur le port 1080 :

http://127.0.0.1:1080/

---

## Tests unitaires

Pour lancer les tests unitaires, exécutez :

`php vendor/bin/phpunit tests`

---

Sujet:

Développer une API REST : 
- faire un endpoint pour l’enregistrement d’utilisateurs (nom/prénom/email unique/société/password).
- faire un endpoint de connexion (email/password) 
- faire un endpoint /me pour récupérer les informations du user connecté (nom/prenom/email/societe)
- cet utilisateur devra être enregistré en base de données
- une notification de type slack devra être envoyé
- un email de confirmation devra être envoyé à l'utilisateur

Quizz à répondre: 
- Que faudrait-il changer/optimiser sur cette api/infra pour encaisser +500 appels/seconde
- Que faudrait-il faire pour sécuriser au maximum cette api ?
