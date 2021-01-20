<!-- TOC -->

- [SYMFONY](#symfony)
- [Prerequis](#prerequis)
- [Creation du projet](#creation-du-projet)
  - [Demarrer le projet symfony](#demarrer-le-projet-symfony)
  - [Utilisation de la console php](#utilisation-de-la-console-php)
- [Creation du site](#creation-du-site)
  - [Principe de routing et services](#principe-de-routing-et-services)
    - [A l'aide du fichier routes.yaml](#a-laide-du-fichier-routesyaml)
    - [A l'aide d'annotation dans nos controller](#a-laide-dannotation-dans-nos-controller)
  - [Principe de Templates](#principe-de-templates)
    - [Creation de la page Twig](#creation-de-la-page-twig)
    - [Gerer le CSS et Javascript](#gerer-le-css-et-javascript)
    - [Bootstrap](#bootstrap)

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

Pour avoir la liste des comandes de la console.
```cmd
php bin/console
```

Pour controller les routes
```cmd
php bin/console debug:route
```

Pour controller les containers de services
```cmd
php bin/console debug:container
```

Pour controller les injections
```cmd
php bin/console debug:autowiring
```

# Creation du site

## Principe de routing et services

[routing](https://symfony.com/doc/current/routing.html)

[service container](https://symfony.com/doc/current/service_container.html)

Lorsque votre application recoit une demande, elle appelle une action du controleur pour generer la reponse. La configuration de routage definit l'action a executer pour chaque URL entrante. Il fournit egalement d'autres fonctionnalites utiles, telles que la generation d'URL conviviales pour le referencement (par exemple `/read/intro-to-symfony` au lieu de `index.php?article_id=57`).

### A l'aide du fichier routes.yaml

* Creation de la route dans le fichier `MaSuperAgence\config\routes.yaml`
* Creation du controller associe `MaSuperAgence\src\Controller\HomeController.php`
* Tester la reponse de votre serveur en appelant l'url `https://127.0.0.1:8000/`
* Creer votre template Twig


Exemple routes.yaml:

```yaml
# nom de la route (home)
home:
  # chemin de la route (a la racine /)
  path: /
  # controller appele (HomeController) ainsi que la methode appele (methode index)
  controller: App\Controller\HomeController::index
```
Exemple HomeController.php ( : Response correspond au type de retour attendu):

```php
<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    public function index():Response
    {
        return new Response('Salut les gens');
    }
}
```

Cela indique au serveur d'executer le contenu du fichier `HomeController.php` lorsque l'utilisateur interroge localhost (https://127.0.0.1:8000/)

Exemple de creation de template Twig

* Dans votre controller creer un constructeur prenant en parametre `$twig`, fonctionne a l'aide de service et d'injection, explique plus bas.
* Puis dans la reponse de votre methode (retourne par la precedente route (ici `index()`)) retourner le rendu du template twig appele (pages.home.html.twig). 
* Creer votre fichier `twig` correspondant.
* Modifier votre fichier `services.yaml` pour y ajouter le service et la variable twig a injecter.
* 


Dans le fichier HomeController.php

```php
class HomeController
{
    protected $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function index():Response
    {
        return new Response($this->twig->render('pages/home.html.twig'));
    }
}
```

Dans le fichier home.html.twig
```html
<h1>Bienvenue sur la homepage</h1>
```

Dans le fichier services.yaml, ajouter l'instance twig a l'aide de l'annotation @twig, pour connaitre les autres services disponible faire un `php bin/console debug:container` et ajouter  lui un tag `tags: ["controller.service_arguments"]`. Attention un asterisque au path resource, comme ci-dessous.

```yaml
App\:
        resource: "../src/*"
App\Controller\HomeController:
    arguments:
        $twig: '@twig'
        tags: ["controller.service_arguments"]
```

>La variable twig etant en faite l'injection de Twig (alias) verifier a l'aide de la ligne de commande `php bin/console debug:autowiring`, nous pouvons alors supprimer entierement le service precedemment mis en place.


###  A l'aide d'annotation dans nos controller

 **TODO A FAIRE IMPERATIVEMENT A L'AIDE DES COURS OCR**

## Principe de Templates

### Creation de la page Twig

Reprenons notre fichier `home.html.twig` est y ajouter cela:

```html
{% extends "base.html.twig" %}
{% block body %}
<h1>Bienvenue sur la homepage</h1>
{% endblock %}
```

Dans le fichier `home.html.twig` nous faisons un extends, permettant d'heriter de la structure html du template du fichier `base.html.twig`.
Puis, nous creons un block body ou nous inserons notre precedent `<h1>`. Le fichier `home.html.twig` doit respecter le template extends.

### Gerer le CSS et Javascript

[Gerer le CSS et Javascript](https://symfony.com/doc/current/frontend.html)

```cmd
composer require symfony/flex
composer remove symfony/symfony
composer require annotations asset orm-pack twig \
logger mailer form security translation validator
composer require --dev dotenv maker-bundle orm-fixtures profiler
rm -rf vendor/*
composer install
composer require symfony/webpack-encore-bundle
yarn install
```


### Bootstrap

Afin de donner un peu plus de style a notre page nous allons utiliser bootstrat.

[Bootstrap](https://getbootstrap.com/)

[Symfony Bootstrap](https://symfony.com/doc/current/frontend/encore/bootstrap.html)

> Afin de retirer les option de protections d'acces au fichier, executer la commande ci-dessous dans votre powershell en mode administrateur.
```
set-executionpolicy unrestricted
```

Installation

```cmd
yarn add bootstrap --dev
```
Importer bootstrap a votre application, dans le fichier MaSuperAgence\assets\styles\global.scss.

```scss
@import "~bootstrap/scss/bootstrap";
```
Importer du JavaScript Bootstrap

```cmd
 yarn add jquery popper.js --dev
```


composer require symfony/webpack-encore-bundle
yarn install
yarn add @symfony/webpack-encore --dev
yarn add bootstrap --dev
yarn add jquery popper.js --dev
```

Dans le fichier MaSuperAgence\assets\styles\app.css



Dans le fichier base.html.twig

```html
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>{% block title %}Welcome!{% endblock %}</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        {% block stylesheets %}{% endblock %}

    </head>
    <body>
        {% block body %}{% endblock %}
        {% block javascripts %}{% endblock %}
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    </body>
</html>

```






