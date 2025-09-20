# Bibliothèque de Patterns FSE (UP Pattern Library)

## Description

Le plugin "Bibliothèque de Patterns FSE" est un outil conçu pour WordPress qui permet d'afficher tous les patterns (modèles de blocs) disponibles dans votre thème FSE (Full Site Editing) actuel. Il fournit une interface utilisateur intuitive pour visualiser, rechercher et explorer les patterns par catégorie.

## Fonctionnalités

- **Affichage des patterns** : Visualisez tous les patterns disponibles dans votre thème actuel
- **Filtrage par catégorie** : Naviguez facilement entre les différentes catégories de patterns
- **Recherche** : Trouvez rapidement des patterns spécifiques grâce à la fonction de recherche
- **Prévisualisation** : Visualisez les patterns en taille réelle avant de les utiliser
- **Interface responsive** : Fonctionne sur tous les appareils, du mobile au desktop
- **Intégration simple** : Utilisez un simple shortcode pour afficher la bibliothèque n'importe où

## Installation

1. Téléchargez le plugin et extrayez-le dans le répertoire `/wp-content/plugins/`
2. Activez le plugin via le menu 'Extensions' dans WordPress
3. Utilisez le shortcode `[pattern_library]` dans n'importe quelle page ou article pour afficher la bibliothèque

## Utilisation

### Shortcode de base

Insérez simplement le shortcode suivant dans n'importe quelle page ou article :

```
[pattern_library]
```

### Interface utilisateur

L'interface de la bibliothèque de patterns comprend :

- **Barre latérale** : Affiche toutes les catégories de patterns disponibles
- **Barre de recherche** : Permet de filtrer les patterns par nom
- **Grille de patterns** : Affiche les aperçus des patterns disponibles
- **Bouton de prévisualisation** : Permet de voir le pattern en taille réelle

## Personnalisation

Le plugin utilise automatiquement les styles de votre thème actuel pour que les patterns s'affichent correctement. Les styles CSS et les scripts JavaScript peuvent être personnalisés en modifiant les fichiers dans le répertoire `assets/`.

## Prérequis

- WordPress 5.8 ou supérieur
- Un thème compatible avec l'édition complète de site (FSE)

## Développement

Le plugin est structuré de la manière suivante :

- `up-pattern-library.php` : Fichier principal du plugin
- `assets/css/pattern-library.css` : Styles de l'interface
- `assets/js/pattern-library.js` : Scripts pour l'interactivité

## Auteur

Claude

## Version

1.0.0

## Licence

GPL v2 ou ultérieure
