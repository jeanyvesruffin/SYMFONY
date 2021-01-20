<!-- TOC -->

- [SYMFONY](#symfony)
- [Prerequis](#prerequis)
- [Creation du projet](#creation-du-projet)
  - [Demarrer le projet symfony](#demarrer-le-projet-symfony)
  - [Utilisation de la console php](#utilisation-de-la-console-php)
- [Creation du site](#creation-du-site)
  - [Principe de routing et services](#principe-de-routing-et-services)
    - [A l'aide du fichier routes.yaml](#a-laide-du-fichier-routesyaml)
      - [Controller vos services containers et routes avec la console](#controller-vos-services-containers-et-routes-avec-la-console)
    - [A l'aide d'annotation dans nos controller](#a-laide-dannotation-dans-nos-controller)

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

Puis preciser le type de notre precedente variable twig;

Dans le fichier HomeController.php

```php
/**
    * Undocumented variable
    *
    * @var Environment
    */
  protected $twig;
```


#### Controller vos services containers et routes avec la console

Pour controller les routes
```cmd
php bin/console debug:route
```

Pour controller les containers de services
```cmd
php bin/console debug:container
```


###  A l'aide d'annotation dans nos controller

 **TODO A FAIRE IMPERATIVEMENT A L'AIDE DES COURS OCR**

