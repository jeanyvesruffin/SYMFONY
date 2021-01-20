<!-- TOC -->

- [SYMFONY](#symfony)
- [Prerequis](#prerequis)
- [Creation du projet](#creation-du-projet)
    - [Demarrer le projet symfony](#demarrer-le-projet-symfony)
    - [Utilisation de la console php](#utilisation-de-la-console-php)

<!-- /TOC -->

# SYMFONY

# Prerequis

Installer [composer](https://getcomposer.org/)

Verifier l'integrite de l'installation
```cmd
composer -v
```

Installer [Symfony](https://symfony.com/doc/current/setup.html)
Verifier l'integrite de l'installation
```cmd
symfony -v
symfony check:requirements
```

# Creation du projet

```cmd
composer create-project symfony/website-skeleton [NOM PROJET]
```

Puis dans le fichier .env configurer votre base de donnees
```.env
APP_ENV=dev
DATABASE_URL="mysql://root:root@127.0.0.1:3306/symfony?serverVersion=5.7"
```

Configurer Doctrine dans Doctrine.yaml
```yaml
doctrine:
    dbal:
        url: '%env(resolve:DATABASE_URL)%'
        driver: 'pdo_mysql'  
```

Faire pointer votre repertoire Apache de ressource sur le dossier public (vs www)

## Demarrer le projet symfony

```cmd
symfony server:start
```

## Utilisation de la console php

```cmd
php bin/console
// Retournera la liste des commandes utiles
```