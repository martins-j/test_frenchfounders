# test_frenchfounders

## Installation avec docker

Pour démarrer l'API avec Docker, exécutez :

`docker-compose build`

puis lancez :

`docker-compose up -d`

Le site est alors up et accessible via l'adresse locale :

http://localhost:80

## Base de données

Dans un premier temps, créez le schéma de la base de données :

`docker-compose exec php php bin/console doctrine:schema:create`

Le fichier AppFixtures.php sur le répertoire src/DataFixtures permet d'insérer de fausses données en base de données. Pour cela, exécutez la commande suivante :

`docker-compose exec php php bin/console doctrine:fixtures:load`

La base de données peut être suivie et gérée en utilisant l'applicatif `phpMyAmdin` sur le port 8080 :

http://localhost:8080/

avec les champs de connexion suivants :

- Serveur : `mysql`
- Utilisateur : `root`

---

## Envoie d'e-mails avec MailDev

MailDev permet de lancer un serveur SMTP qui va intercepter tous les emails. Il propose une interface web qui permettra de voir les mails capturés.

L'interface web pour voir les mails capturés est accesible sur le port 1080 :

http://localhost:1080/

---

## Envoie de notifications Slack

Un URL du Webhook entrant pour publier les messages dans Slack a été saisie sur la variable d'environnement :

`SLACK_WEBHOOK_ENDPOINT=`

Pour tester vous même, changez cette variable par l'URL Webhook d'accès à un espace de travail Slack où vous êtes administrateur.

---

## Tests unitaires

Pour lancer les tests unitaires, exécutez :

`docker-compose exec php php vendor/bin/phpunit tests`

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

Pour augmenter la sécurité d’un API, nous pouvons utiliser :

- un jeton (token) JWT, c’est-à-dire, une chaîne de caractères que l’on va envoyer à chaque requête que l’on souhaite effectuer auprès d’une API afin de s’authentifier. Ce jeton contient toutes les informations nécessaires à l’identification.

- une classe Voter du composant Security pour implémenter des règles spécifiques de décision si une action sur un objet est autorisée.

- OAuth2 pour donner un accès à une application tierce et sans avoir besoin d’y stocker en clair les identifiants de l’utilisateur. Son utilisation permet de mettre en place une délégation d’authentification et d’autorisation de l’utilisateur sous la forme d’un jeton pour accorder à une application tierce un accès limité sur une ressource protégées où elle est stockée.
