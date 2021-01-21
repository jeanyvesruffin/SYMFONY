<!-- TOC -->

- [SYMFONY](#symfony)
- [Prerequis](#prerequis)
- [Creation de notre environnement de developpement symfony](#creation-de-notre-environnement-de-developpement-symfony)
  - [Demarrer le projet symfony](#demarrer-le-projet-symfony)
  - [Utilisation de la console php](#utilisation-de-la-console-php)
- [Creation de nos premieres page de site](#creation-de-nos-premieres-page-de-site)
  - [Principe de routing et services](#principe-de-routing-et-services)
    - [A l'aide du fichier routes.yaml](#a-laide-du-fichier-routesyaml)
    - [A l'aide d'annotation dans nos controller](#a-laide-dannotation-dans-nos-controller)
  - [Principe de Templates](#principe-de-templates)
    - [Creation de la page Twig](#creation-de-la-page-twig)
    - [Bootstrap](#bootstrap)
    - [Gerer le CSS et Javascript](#gerer-le-css-et-javascript)
  - [Ajout d'une barre de navigation](#ajout-dune-barre-de-navigation)
  - [Creation de la second page](#creation-de-la-second-page)
  - [Refactorisation de nos controllers a l'aide `extends AbstractController`](#refactorisation-de-nos-controllers-a-laide-extends-abstractcontroller)
  - [Ajout du lien actif "Acheter" dans la navBar lorsque l'on est sur la page acheter.](#ajout-du-lien-actif-acheter-dans-la-navbar-lorsque-lon-est-sur-la-page-acheter)
- [Doctrine](#doctrine)
  - [Creation de la base de donnees](#creation-de-la-base-de-donnees)
  - [Ajout de champs dans la base de donnees](#ajout-de-champs-dans-la-base-de-donnees)
  - [interaction avec la base de donnees Create Read Update Delete](#interaction-avec-la-base-de-donnees-create-read-update-delete)
    - [`Create` Creer un enregistrement par le codage (instance Entity et initialisation de ces attributs)](#create-creer-un-enregistrement-par-le-codage-instance-entity-et-initialisation-de-ces-attributs)
    - [`Read` Recuperer un enregistrement](#read-recuperer-un-enregistrement)
      - [1er methode](#1er-methode)
      - [2eme methode Recuperer un enregistrement a l'aide des methodes injections (verifier au prealable avec `php bin/console:autowiring`)](#2eme-methode-recuperer-un-enregistrement-a-laide-des-methodes-injections-verifier-au-prealable-avec-php-binconsoleautowiring)
      - [3eme methode Recuperer un enregistrement a l'aide d'une methode cree qui utilisera la fonction `createQueryBuilder()`](#3eme-methode-recuperer-un-enregistrement-a-laide-dune-methode-cree-qui-utilisera-la-fonction-createquerybuilder)
    - [`Update` Mise a jour d'un enregistrement](#update-mise-a-jour-dun-enregistrement)

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

# Creation de notre environnement de developpement symfony

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

Pour connaitre les commandes de creation automatique de code
```cmd
php bin/console make [option]
```

# Creation de nos premieres page de site

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

Nous utiliserons cette methode plus loin lors de la creation de la seconde page.

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

### Bootstrap

Afin de donner un peu plus de style a notre page nous allons utiliser bootstrat.

[Bootstrap](https://getbootstrap.com/)

[Symfony Bootstrap](https://symfony.com/doc/current/frontend/encore/bootstrap.html)

> Afin de retirer les option de protections d'acces au fichier, executer la commande ci-dessous dans votre powershell en mode administrateur.


```cmd
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
Importer votre configuration sur MaSuperAgence\assets\app.js
```js
// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
// start the Stimulus application
import './bootstrap';
require('.\styles\global.scss');
const $ = require('jquery');
global.$ = global.jQuery = $;
require('bootstrap');
```

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

Dans le fichier base.html.twig

```html
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>
			{% block title %}Welcome at Home!
			{% endblock %}
		</title>
		{% block stylesheets %}
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		{% endblock %}
	</head>
	<body>
		{% block body %}{% endblock %}
		{% block javascripts %}{% endblock %}
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	</body>
</html>
```

Dans le fichier home.html.twig, a la'aide de lm'autocompletion de l'ide, faire un jumbotron (template bootstrap).

```html
{% extends "base.html.twig" %}
{% block body %}
	<div class="jumbotron text-center">
		<h1 class="display-4">Immo RUFFIN</h1>
		<p class="lead">Bienvenue, sur le site N°1 de la location immobilière</p>
		<hr class="my-4">
		</div>
{% endblock %}
```

## Ajout d'une barre de navigation

[Navbar Component Bootstrap](https://getbootstrap.com/docs/4.0/components/navbar/)

Copier/coller le code de l'adresse si dessus dans notre templet de `MaSuperAgence\templates\base.html.twig` et modifier le a votre quise.
Vous pouvez utiliser Twig pour la redirection lors du clic sur Mon agence, en ajoutant dans le href le helper twig path `href="{{ path('home')}}"`

## Creation de la second page

Celle-ci nous retournera la liste des biens (lien "Acheter").

Dans le fichier MaSuperAgence\templates\base.html.twig. Ajout du href pointant sur `path('property.index')`.

```html
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>
			{% block title %}Welcome at Home!
			{% endblock %}
		</title>
		{% block stylesheets %}
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

		{% endblock %}
	</head>
	<body>
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
			<a class="navbar-brand" href="{{ path('home')}}">Mon agence</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item">
						<a class="nav-link" href="{{ path('property.index')}}">Acheter
						</a>
					</li>
				</ul>
			</div>
		</nav>
		{% block body %}{% endblock %}
		{% block javascripts %}{% endblock %}
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	</body>
</html>
```

Nous allons ici creer la route a l'aide des annotations dans le controller.

Dans le fichier MaSuperAgence\src\Controller\PropertyController.php
```php
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController
{

    /**
     * Undocumented function
     * @Route("/biens",  name="property.index")
     * @return Response
     */
    public function index(): Response
    {
        return new Response('Les biens');
    }
}

```

Et creons le template associe, dans MaSuperAgence\templates\property\index.html.twig

```html
{% extends "base.html.twig" %}
{% block title 'Voir tous nos biens' %}
{% block body %}
<div class="container mt-4">
    <h1>Voir les biens</h1>
</div>
{% endblock %}
```

Commentons notre fichier routes et faisons de meme pour la page d'accueil.

Dans MaSuperAgence\src\Controller\HomeController.php

```php
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HomeController
{
    /**
     * Undocumented variable
     * 
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }
    /**
     * Undocumented function
     * @Route ("/",  name="home")
     * @return Response
     */
    public function index(): Response
    {
        return new Response($this->twig->render('pages/home.html.twig'));
    }
}
```

## Refactorisation de nos controllers a l'aide `extends AbstractController`

A l'aide de la classe AbstractController, nous pouvons heriter des methodes nous permettant d'afficher une vue (render), entre autre.

MaSuperAgence\src\Controller\HomeController.php ==> Deviens alors:

```php
class HomeController extends AbstractController
{
    /**
     * @Route ("/",  name="home")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('pages/home.html.twig');
    }
}
```
MaSuperAgence\src\Controller\PropertyController.php ==> Deviens alors:

```php
class PropertyController extends AbstractController
{
    /**
     * @Route("/biens",  name="property.index")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('property/index.html.twig');
    }
}
```

## Ajout du lien actif "Acheter" dans la navBar lorsque l'on est sur la page acheter.

Au niveau de notre controller `PropertyController` nous pouvons ajouter au render de templet des parametres ici l'ajout du controller ou nous pouvons envoyer la page `current_menu` et nous y affectons la valeur `properties` comme cela.

Dans PropertyController.

```php
class PropertyController extends AbstractController
{
    /**
     * @Route("/biens",  name="property.index")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('property/index.html.twig',[
			'current_menu'=> 'properties'
			
        ]);
    }
}
```

Puis dans notre templet base.html.twig nous ajoutons une condition twig qui rend le lien Acheter de la navBar actif.
```html
<a class="nav-link {% if current_menu is defined and current_menu == 'properties' %}active{% endif %}" href="{{ path('property.index') }}">Acheter</a>
```

# Doctrine

Le framework Symfony utilise par defaut l’ORM Doctrine qui permet d'interagir avec la base de donnees plus facilement.

## Creation de la base de donnees

>**Pour cette partie nous utilisons [Wampserver](https://www.wampserver.com/) pour gerer le serveur. Bien suivre son installation. Enfin utiliser un driver plugin de votre ide pour visualiser votre base de donnees autrement que par phpmyadmin par exemple**


Nous allons creer la base de donnees a l'aide d'une ligne de commande, celle-ci prendra en configuration les parametres indiques dans notre fichier .env 

```env
DATABASE_URL="mysql://root:root@127.0.0.1:3306/masuperagence?serverVersion=5.7"
```

Exemple de creation de l'entity Property ayant comme champs title de type string varchar 255, not nullable et description de type txt, nullable
```cmd
php bin/console doctrine:database:create
```

Nous ajoutons ensuite la base de donnee dans l'IDE (VSCODE) a l'aide de plugin (driver) ici mysql.

Ensuite, nos allons utiliser la console php pour generer une entite automatiquement a l'aide de la commande suivante, une entite est une classe qui represente un enregistrement dans la base de donnee:

```cmd
 php bin/console make:entity

 Class name of the entity to create or update (e.g. TinyElephant):
 > Property

 created: src/Entity/Property.php
 created: src/Repository/PropertyRepository.php
  Entity generated! Now let's add some fields!
 You can always add more fields later manually or by re-running this command.
 New property name (press <return> to stop adding fields):
 > title

 Field type (enter ? to see all types) [string]:
 > string

 Field length [255]:
 >

 Can this field be null in the database (nullable) (yes/no) [no]:
 >

 updated: src/Entity/Property.php
 Add another property? Enter the property name (or press <return> to stop adding fields):
 > description

 Field type (enter ? to see all types) [string]:
 > text
 
 Can this field be null in the database (nullable) (yes/no) [no]:
 > yes

 updated: src/Entity/Property.php
 Add another property? Enter the property name (or press <return> to stop adding fields):
 >
  Success! 
  Next: When you're ready, create a migration with php bin/console make:migration
```
Puis faire la commande de migration (Entity -->database) suivante, celle-ci generera le fichier necessaire a la migration.

```cmd
php bin/console make:migration
```

Nous pouvons examiner le fichier genere (MaSuperAgence\migrations\Version20210121101413.php) qui defini les etapes de creation et migration du projet, nous pouvons remarquer la sequence de script Sql pour la creation de la table etc...
Une fois le fichier verifier nous pouvons effectuer la migration.

```cmd
php bin/console doctrine:migrations:migrate
 WARNING! You are about to execute a migration in database "masuperagence" that could result in schema changes and data loss. Are you sure you wish to continue? (yes/no) [yes]:
 >

[notice] Migrating up to DoctrineMigrations\Version20210121101413
[notice] finished in 722.9ms, used 20M memory, 1 migrations executed, 1 sql queries

```


<img src="ressources\database_vscode.PNG"  >

**Attention au besoin renommer l'annotaion de votre entity telquelle sinon peux generer une erreur**

```php
/**
 * @ORM\Entity(repositoryClass="App\Repository\PropertyRepository")
 */
class Property
{
}
```

## Ajout de champs dans la base de donnees

La premiere methode consiste a modifier le fichier Property.php et y ajouter un attribut (qui sera notre champ), y mettre l'annotation voulu (ici `type="integer"`) puis creer les getters et setters.

L'autre methode bien plus rapide consiste a utiliser la console comme ci-dessous.

```cmd
php bin/console make:entity Property
// Generer les champs surface, rooms, bedrooms, floor, price, heat, city, address, postal_code, sold et created_at
php bin/console make:migration
```

Si en controlant le fichier de migration vous souhaitez le modifier , c'est le moment!! Avant d'effectuer celle-ci sur le serveur.
Exemple pour le champs `sold` nous souahitons par defaut qu'il soit a `false`. Alors nous ajouterons dans son annotation dans le fichier Property.php

```php
/**
 * @ORM\Column(type="boolean", options={"default": false} )
 */
private $sold;
```

On supprime le fichier Version, puis on le regenere avac la commande
```cmd
php bin/console make:migration
```

une fois regener nous pouvons constater que desormais `ADD sold TINYINT(1) DEFAULT \'0\' NOT NULL`.
Executer la commande de migration:

```cmd
php bin/console doctrine:migrations:migrate
```

Controler votre base de donnees:

<img src="ressources\database_vscode_2.PNG"  >

## interaction avec la base de donnees Create Read Update Delete

### `Create` Creer un enregistrement par le codage (instance Entity et initialisation de ces attributs)

* Creer une instance de votre entite dans le fichier controller.
* Affecter des valeurs aux attributs de l'entite a l'aide des setters dans le fichier controller.
* Nous integrerons une constante pour le champs heat, ainsi qu'initialiserons le champs created_at a travers le constructeur de la class Property et sold a travers son initialisation `private $sold = false`.
* Affectons dans une variable `entityManager`, l'envoi de cette entite vers la base de donnees a l'aide de `ManagerRegistry` retourne par la methode `getDoctrine` de la classe AbstractController. Puis nous utilisons cette variable pour utiliser la methode `persist()` pour faire persister notre entite. Et enfin nous mettons a jour la base de donnee a l'aide de la methode `flash` qui portera tous les changement d'entity dans notre base de donnees.
* Controller sur votre site que tous se passe bien. A l'aide de la barre de debug cliquer sur databaseQuery pour visualiser les requetes effectuees. Nous pouvons voir notre bien qui est bien creer dans notre base de donnees. Nosu pouvons controller a l'aide de l'IDE ou phpmyadmin que tous est ok.

Dans PropertyController.php
```php
<?php
class PropertyController extends AbstractController
{
    /**
     * @Route("/biens",  name="property.index")
     * @return Response
     */
    public function index(): Response
    {
        $property = new Property();
        $property->setTitle('Mon premier bien')
        ->setPrice(200000)
        ->setRooms(4)
        ->setBedrooms(3)
        ->setDescription('Une petite description')
        ->setSurface(60)
        ->setFloor(4)
        ->setHeat(1)
        ->setCity('Montpellier')
        ->setAddress('64 rue Gambetta')
        ->setPostalCode('34000');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($property);
        $entityManager->flush();
        return $this->render('property/index.html.twig',[
            'current_menu'=> 'properties'
        ]);
    }
}
```

Integration constante et initialisation created_at dans Property.php
```php
const HEAT = [
    0=>'electric',
    1=>'gaz'
];
private $sold = false;
```

>Nous allons commenter notre enregistrement precedent afin de ne pas le regenerer at allons recuperer notre enregistrement.

### `Read` Recuperer un enregistrement

#### 1er methode

* Doit faire appel au repository. Initialiser a l'aide de doctrine nous l'affectons a notre variable `$repository`.
* Puis faire un `dump($repository)` pour visualiser le retour. Tester le retour et visualiser son contenu dans la barre de debugage de votre page.
  
```php
class PropertyController extends AbstractController
{
    /**
     * @Route("/biens",  name="property.index")
     * @return Response
     */
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Property::class);
        dump($repository);
        return $this->render('property/index.html.twig',[
            'current_menu'=> 'properties'
        ]);
    }
}
```

<img src="ressources\reponse_1.PNG"  >


> Commenter ces dernieres lignes afin de proceder a la methode par injection


#### 2eme methode Recuperer un enregistrement a l'aide des methodes injections (verifier au prealable avec `php bin/console:autowiring`)

* 1er methode a l'aide du constructeur. Plus interressant si le repository est utilise sur plusieurs fonction.

```php
class PropertyController extends AbstractController
{
    /**
     *
     * @var PropertyRepository
     */
    private $repository;

    public function __construct(PropertyRepository $repository)
    {
      $this->repository = $repository;  
    }

    /**
     * @Route("/biens",  name="property.index")
     * @return Response
     */
    public function index(): Response
    {
        $property = $this->repository->find(1);
        dump($property);
        return $this->render('property/index.html.twig', [
            'current_menu' => 'properties'
        ]);
    }
}
```

* 2eme methode a l'aide de parametre sur la fonction index

```php
class PropertyController extends AbstractController
{
      /**
     * @Route("/biens",  name="property.index")
     * @return Response
     */
    public function index(PropertyRepository $repository): Response
    {
        $property = $this->repository->find(1);
        dump($property);
        return $this->render('property/index.html.twig', [
            'current_menu' => 'properties'
        ]);
    }
}
```

Nous utilisons la methode `find(1)` pour recuperer le 1er enregistrement.

<img src="ressources\reponse_2.PNG"  >

Nous utilisons la methode `findAll()` pour recuperer tous les enregistrements (stocke dans un tableau).

<img src="ressources\reponse_3.PNG"  >

Nous utilisons la methode `findOneBy(['floor'=>4])` pour recuperer tous les enregistrements repondant au critere floor a 4.

<img src="ressources\reponse_4.PNG"  >

#### 3eme methode Recuperer un enregistrement a l'aide d'une methode cree qui utilisera la fonction `createQueryBuilder()` 

Exemple si nous desirons recuperer tout les sold a false.

* Methode a creer dans le repository
Exemple:
```php
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    public  function findAllSisible()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
```
Descrtiption de la methode ci-dessus:
* `p` correspondera a l'alias de notre table ici Property alias p
* `andWhere` permet d'ajouter des conditions a notre requete
* `setParameter` permet d'ajouter des parametres a notre requete
* `orderBy` correspond a ORDER BY pour notre requete
* `setMaxResults` correspond a LIMIT pour notre requete
* `getQuery` pour recuperer la requete
* `getResult` pour recuperer les resultats

Dans PropertyRepository.php
```php
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    public  function findAllVisible()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.sold = false')
            ->getQuery()
            ->getResult();
    }
}
```

Et enfin dans notre PropertyController.php
```php
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    public  function findAllVisible()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.sold = false')
            ->getQuery()
            ->getResult();
    }
}
```

Nous pouvons controler votre site et votre reponse.

### `Update` Mise a jour d'un enregistrement