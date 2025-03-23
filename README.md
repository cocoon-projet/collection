[![PHP Composer](https://github.com/cocoon-projet/collection/actions/workflows/ci.yml/badge.svg)](https://github.com/cocoon-projet/collection/actions/workflows/ci.yml) [![codecov](https://codecov.io/gh/cocoon-projet/collection/graph/badge.svg?token=0R7HW7AMX7)](https://codecov.io/gh/cocoon-projet/collection) ![License](https://img.shields.io/badge/Licence-MIT-green)

# Cocoon Collection

Une bibliothèque PHP moderne pour manipuler des collections de données avec une interface fluide.

## 🚀 Installation

```bash
composer require cocoon-projet/collection
```

## 📋 Prérequis

- PHP 8.0 ou supérieur
- Composer

## 🎯 Caractéristiques

- Interface fluide pour la manipulation de tableaux
- Support complet du typage PHP 8
- Méthodes de filtrage style SQL (where, whereIn, etc.)
- Fonctions d'agrégation (sum, avg, etc.)
- Manipulation de collections (map, filter, etc.)
- Compatible avec les interfaces PHP standards (Countable, ArrayAccess, IteratorAggregate)

## 📖 Documentation

### Création d'une collection

```php
use Cocoon\Collection\Collection;

// Collection vide
$collection = new Collection();

// Collection avec des données initiales
$collection = new Collection(['un', 'deux', 'trois']);

// Collection d'objets ou tableaux associatifs
$collection = new Collection([
    ['id' => 1, 'nom' => 'Alice'],
    ['id' => 2, 'nom' => 'Bob']
]);
```

### Méthodes de Base

#### Accès aux éléments

```php
// Obtenir tous les éléments
$tous = $collection->all();

// Obtenir le premier élément
$premier = $collection->first();

// Obtenir le dernier élément
$dernier = $collection->last();

// Obtenir un élément spécifique
$element = $collection->get('cle', 'valeur_par_defaut');
```

#### Manipulation

```php
// Ajouter un élément
$collection->set('cle', 'valeur');

// Vérifier l'existence d'un élément
$existe = $collection->exists('valeur');

// Supprimer le premier élément
$premier = $collection->shift();

// Obtenir une portion de la collection
$portion = $collection->slice(0, 2);

// Prendre N éléments
$elements = $collection->take(3);  // 3 premiers éléments
$elements = $collection->take(-2); // 2 derniers éléments
```

### Filtrage et Recherche

```php
// Filtrage simple
$filtree = $collection->where('age', '>', 25);

// Filtrage avec IN
$filtree = $collection->whereIn('id', [1, 2, 3]);

// Filtrage avec BETWEEN
$filtree = $collection->whereBetween('age', [25, 35]);

// Filtrage avec NOT IN
$filtree = $collection->whereNotIn('id', [4, 5, 6]);

// Filtrage avec NOT BETWEEN
$filtree = $collection->whereNotBetween('age', [40, 50]);
```

### Tri et Groupement

```php
// Tri simple
$triee = $collection->sort('asc');  // ou 'desc'

// Tri par clé
$triee = $collection->orderBy('age', 'desc');

// Groupement
$groupee = $collection->groupBy('categorie');
```

### Transformation

```php
// Mapper les éléments
$mappee = $collection->map(fn($item) => strtoupper($item));

// Filtrer les éléments
$filtree = $collection->filter(fn($item) => $item > 10);

// Extraire une colonne
$noms = $collection->pluck('nom');

// Extraire une colonne avec index personnalisé
$noms = $collection->pluck('nom', 'id');

// Diviser en morceaux
$morceaux = $collection->chunk(2);
```

### Agrégation

```php
// Compter les éléments
$total = $collection->count();

// Calculer une somme
$somme = $collection->sum();
$somme = $collection->sum('prix');

// Calculer une moyenne
$moyenne = $collection->avg();
$moyenne = $collection->avg('age');

// Joindre les éléments
$chaine = $collection->implode(', ');
$chaine = $collection->implode(', ', 'nom');
```

### Vérifications

```php
// Vérifier si vide
$estVide = $collection->isEmpty();

// Vérifier si non vide
$nonVide = $collection->isNotEmpty();

// Vérifier l'existence d'une valeur
$existe = $collection->exists('valeur');
$existe = $collection->exists(fn($item) => $item->age > 25);
```

### Sélection de Clés

```php
// Sélectionner certaines clés
$selection = $collection->only('id', 'nom');

// Exclure certaines clés
$exclusion = $collection->except('password', 'token');

// Obtenir toutes les clés
$cles = $collection->keys();
```

## 🔄 Conversion

```php
// Convertir en tableau
$tableau = $collection->toArray();

// Convertir en chaîne
$chaine = $collection->implode(', ');
```

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :

1. Fork le projet
2. Créer une branche pour votre fonctionnalité
3. Commiter vos changements
4. Pousser vers la branche
5. Ouvrir une Pull Request

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 🔍 Exemples Complets

### Exemple 1 : Gestion d'une liste de produits

```php
$produits = new Collection([
    ['id' => 1, 'nom' => 'Laptop', 'prix' => 1200, 'stock' => 5],
    ['id' => 2, 'nom' => 'Smartphone', 'prix' => 800, 'stock' => 10],
    ['id' => 3, 'nom' => 'Tablette', 'prix' => 400, 'stock' => 3]
]);

// Produits en stock avec prix > 500€
$produitsDisponibles = $produits
    ->where('stock', '>', 0)
    ->where('prix', '>', 500)
    ->orderBy('prix', 'desc');

// Calculer la valeur totale du stock
$valeurStock = $produits
    ->map(fn($item) => $item['prix'] * $item['stock'])
    ->sum();

// Liste des noms de produits
$noms = $produits->pluck('nom')->implode(', ');
```

### Exemple 2 : Traitement de données utilisateurs

```php
$utilisateurs = new Collection([
    ['id' => 1, 'nom' => 'Alice', 'age' => 25, 'role' => 'admin'],
    ['id' => 2, 'nom' => 'Bob', 'age' => 30, 'role' => 'user'],
    ['id' => 3, 'nom' => 'Charlie', 'age' => 35, 'role' => 'user']
]);

// Grouper par rôle
$parRole = $utilisateurs->groupBy('role');

// Moyenne d'âge
$moyenneAge = $utilisateurs->avg('age');

// Utilisateurs de 25 à 32 ans
$trancheAge = $utilisateurs->whereBetween('age', [25, 32]);

// Extraire emails et noms
$contacts = $utilisateurs->pluck('email', 'nom');
```

### Nouvelles Fonctionnalités de Filtrage

```php
// Filtrer les éléments NULL
$sansStock = $collection->whereNull('stock');

// Filtrer les éléments non NULL
$enStock = $collection->whereNotNull('stock');

// Filtrer avec LIKE (style SQL)
$recherche = $collection->whereLike('nom', '%Dell%');     // Contient 'Dell'
$recherche = $collection->whereLike('nom', 'Dell%');      // Commence par 'Dell'
$recherche = $collection->whereLike('nom', '%Dell');      // Termine par 'Dell'
```

### Jointures

```php
// Jointure interne (INNER JOIN)
$resultat = $produits->join($ventes, 'id', 'produit_id', 'inner');

// Jointure gauche (LEFT JOIN)
$resultat = $produits->join($ventes, 'id', 'produit_id', 'left');

// Jointure droite (RIGHT JOIN)
$resultat = $produits->join($ventes, 'id', 'produit_id', 'right');
```

### Statistiques et Agrégation

```php
// Compter les occurrences par valeur
$parCategorie = $collection->countBy('categorie');

// Statistiques complètes
$stats = $collection->stats('prix');
// Retourne : [
//     'min' => valeur minimale,
//     'max' => valeur maximale,
//     'avg' => moyenne,
//     'sum' => somme,
//     'count' => nombre d'éléments
// ]
```

### Groupement Avancé

```php
// Groupement multi-niveaux
$groupes = $collection->groupByMultiple('categorie', 'marque', 'annee');

// Groupement par intervalles
$parPrix = $collection->groupByRange('prix', 500); // Groupes de 500€
```

### Exemple Complet

```php
$produits = new Collection([
    ['id' => 1, 'nom' => 'Laptop Dell', 'prix' => 1200, 'categorie' => 'Informatique'],
    ['id' => 2, 'nom' => 'iPhone 13', 'prix' => 999, 'categorie' => 'Téléphonie'],
    // ...
]);

// Analyse complexe
$analyse = $produits
    ->whereNotNull('stock')                    // Uniquement les produits en stock
    ->whereBetween('prix', [300, 1000])       // Prix entre 300€ et 1000€
    ->groupBy('categorie')                     // Grouper par catégorie
    ->map(function ($groupe) {                 // Analyser chaque groupe
        return [
            'count' => $groupe->count(),       // Nombre de produits
            'stats' => $groupe->stats('prix'), // Statistiques des prix
            'total_stock' => $groupe->sum('stock')  // Stock total
        ];
    });
```

### Bonnes Pratiques

1. **Performance**
   - Utilisez `whereNotNull()` avant d'autres filtres pour réduire le jeu de données
   - Préférez `groupByMultiple()` à des `groupBy()` imbriqués
   - Utilisez `stats()` plutôt que plusieurs appels individuels aux méthodes d'agrégation

2. **Jointures**
   - Spécifiez le type de jointure approprié pour éviter les résultats inattendus
   - Utilisez 'left' pour préserver les données de la collection principale
   - Vérifiez les clés de jointure avant d'effectuer l'opération

3. **Filtrage**
   - `whereLike()` est sensible à la casse par défaut
   - Les motifs de recherche suivent la syntaxe SQL (`%` pour zéro ou plusieurs caractères)
   - Combinez plusieurs conditions de filtrage pour des recherches précises

