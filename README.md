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

## Tester l'envoie de notifications Slack

Un URL du Webhook entrant pour publier les messages dans Slack a été saisie sur la variable d'environnement :

`SLACK_WEBHOOK_ENDPOINT=`

Pour tester vous même, changez cette variable par l'URL Webhook d'accès à un espace de travail Slack où vous êtes administrateur.

---

## Tests unitaires

Pour lancer les tests unitaires, exécutez :

`php vendor/bin/phpunit tests`

---

# Sujet du test :

Développer une API REST : 
- faire un endpoint pour l’enregistrement d’utilisateurs (nom/prénom/email unique/société/password).
- faire un endpoint de connexion (email/password) 
- faire un endpoint /me pour récupérer les informations du user connecté (nom/prenom/email/societe)
- cet utilisateur devra être enregistré en base de données
- une notification de type slack devra être envoyé
- un email de confirmation devra être envoyé à l'utilisateur

# Quizz à répondre: 


## Que faudrait-il changer/optimiser sur cette api/infra pour encaisser +500 appels/seconde

Pour optimiser/changer les nombreux appels, je donne deux exemples :

- La mise en cache qui fournit un stockage en mémoire précompilé. De cette manière, l’applicatif n'aura pas besoin de charger et d'analyser les méthodes/scripts à chaque fois qu'ils sont appelés. Nous pouvons également mettre en cache des données pour un accès plus rapide en activant le cache des résultats de la requête sur les requêtes fréquemment exécutées où les données changent rarement. Enfin, nous pouvons aussi mettre en cache HTTP entre notre client et nos services back-end. C’est-à-dire, mettre en cache une page entière qui ne change pas souvent (le cas où nos applications ne nécessitent pas des données ni d’un chargement dynamique) et éviter d'appeler notre serveur d'application pour tout sauf le premier appel.

- Le composant Symfony Messenger aide les applicatifs à échanger des messages qui peuvent être transmis de manière asynchrone via des systèmes de file d’attente. Ainsi, au lieu de lancer directement le traitement des demandes, Messenger donne la possibilité de le reporter à plus tard.


## Que faudrait-il faire pour sécuriser au maximum cette api ?

