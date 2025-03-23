<?php

declare(strict_types=1);

namespace Cocoon\Collection;

use ArrayAccess;
use ArrayIterator;
use Cocoon\Collection\Features\Criteria;
use Countable;
use IteratorAggregate;
use InvalidArgumentException;
use Traversable;

/**
 * Classe Collection
 *
 * Wrapper moderne pour la manipulation de tableaux avec une interface fluide.
 * Cette classe fournit des méthodes pratiques pour travailler avec des ensembles de données.
 *
 * Fonctionnalités principales :
 * - Manipulation de tableaux (ajout, suppression, modification)
 * - Filtrage et recherche (where, whereIn, whereBetween)
 * - Tri et groupement (sort, orderBy, groupBy)
 * - Agrégation (sum, avg, implode)
 * - Transformation (map, pluck, chunk)
 *
 * Implémente les interfaces :
 * - Countable : pour compter les éléments
 * - ArrayAccess : pour accéder aux éléments comme un tableau
 * - IteratorAggregate : pour itérer sur les éléments
 *
 * @template TKey of array-key
 * @template TValue
 * @implements IteratorAggregate<TKey, TValue>
 * @implements ArrayAccess<TKey, TValue>
 */
class Collection implements Countable, ArrayAccess, IteratorAggregate
{
    use Criteria;

    /**
     * Les éléments contenus dans la collection.
     *
     * @var array<TKey, TValue>
     */
    private array $collection;

    /**
     * Crée une nouvelle collection.
     *
     * @param array<TKey, TValue> $collection Tableau initial de données
     */
    public function __construct(array $collection = [])
    {
        $this->collection = $collection;
    }

    /**
     * Retourne le nombre d'éléments dans la collection.
     *
     * @return int Nombre d'éléments
     */
    public function count(): int
    {
        return count($this->collection);
    }

    /**
     * Vérifie si un élément existe à un index donné.
     *
     * @param TKey $offset Index à vérifier
     * @return bool true si l'élément existe, false sinon
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->collection);
    }

    /**
     * Récupère un élément à un index donné.
     *
     * @param TKey $offset Index de l'élément
     * @return TValue Valeur de l'élément
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->collection[$offset];
    }

    /**
     * Définit un élément à un index donné.
     *
     * @param TKey|null $offset Index de l'élément (null pour ajouter à la fin)
     * @param TValue $value Valeur à définir
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->collection[] = $value;
        } else {
            $this->collection[$offset] = $value;
        }
    }

    /**
     * Supprime un élément à un index donné.
     *
     * @param TKey $offset Index de l'élément à supprimer
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->collection[$offset]);
    }

    /**
     * Définit un élément dans la collection avec une interface fluide.
     *
     * @param TKey $key Clé de l'élément
     * @param TValue $value Valeur à définir
     * @return static Retourne l'instance courante pour le chaînage
     */
    public function set(mixed $key, mixed $value): static
    {
        $this->offsetSet($key, $value);
        return $this;
    }

    /**
     * Récupère un élément de la collection avec une valeur par défaut.
     *
     * @template TDefault
     * @param TKey $key Clé de l'élément
     * @param TDefault|null $default Valeur par défaut si l'élément n'existe pas
     * @return TValue|TDefault|null Valeur de l'élément ou valeur par défaut
     */
    public function get(mixed $key, mixed $default = null): mixed
    {
        return $this->offsetExists($key) ? $this->offsetGet($key) : $default;
    }

    /**
     * Retourne un itérateur pour parcourir la collection.
     *
     * @return \Traversable<TKey, TValue>
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->collection);
    }

    /**
     * Retourne le premier élément de la collection.
     *
     * @return TValue|null Premier élément ou null si la collection est vide
     */
    public function first(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }
        return reset($this->collection);
    }

    /**
     * Retourne le dernier élément de la collection.
     *
     * @return TValue|null Dernier élément ou null si la collection est vide
     */
    public function last(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }
        return end($this->collection);
    }

    /**
     * Retourne tous les éléments de la collection.
     *
     * @return array<TKey, TValue> Tableau contenant tous les éléments
     */
    public function all(): array
    {
        return $this->collection;
    }

    /**
     * Calcule la somme des éléments de la collection ou d'une colonne spécifique.
     *
     * @param string|null $column Nom de la colonne (optionnel)
     * @return float|int Somme calculée
     */
    public function sum(?string $column = null): float|int
    {
        if ($column !== null) {
            return $this->column($column)->sum();
        }
        
        return array_sum($this->collection);
    }

    /**
     * Divise la collection en morceaux de taille spécifiée.
     *
     * @param positive-int $size Taille de chaque morceau
     * @return static<int, static> Collection de collections
     * @throws InvalidArgumentException Si la taille est inférieure ou égale à 0
     */
    public function chunk(int $size): static
    {
        if ($size <= 0) {
            throw new InvalidArgumentException('La taille doit être supérieure à 0');
        }

        return new static(array_map(
            fn(array $chunk) => new static($chunk),
            array_chunk($this->collection, $size)
        ));
    }

    /**
     * Extrait une colonne de la collection.
     *
     * @param string $column Nom de la colonne à extraire
     * @param string|null $index Colonne à utiliser comme index (optionnel)
     * @return static Nouvelle collection avec les valeurs extraites
     */
    public function column(string $column, ?string $index = null): static
    {
        return new static(array_column($this->collection, $column, $index));
    }

    /**
     * Sélectionne aléatoirement un ou plusieurs éléments de la collection.
     *
     * @param positive-int $number Nombre d'éléments à sélectionner
     * @return static Nouvelle collection avec les éléments sélectionnés
     * @throws InvalidArgumentException Si le nombre demandé est invalide
     */
    public function random(int $number = 1): static
    {
        if ($number <= 0) {
            throw new InvalidArgumentException('Le nombre doit être supérieur à 0');
        }

        if ($number > $this->count()) {
            throw new InvalidArgumentException('Pas assez d\'éléments à sélectionner');
        }

        $keys = array_rand($this->collection, $number);
        $keys = is_array($keys) ? $keys : [$keys];

        return new static(array_intersect_key($this->collection, array_flip($keys)));
    }

    /**
     * Calcule la moyenne des éléments de la collection ou d'une colonne spécifique.
     *
     * @param string|null $column Nom de la colonne (optionnel)
     * @return float|int Moyenne calculée
     */
    public function avg(?string $column = null): float|int
    {
        $count = $this->count();
        
        if ($count === 0) {
            return 0;
        }

        return $this->sum($column) / $count;
    }

    /**
     * Retourne une nouvelle collection avec uniquement les valeurs (sans les clés).
     *
     * @return static<int, TValue> Nouvelle collection avec les valeurs réindexées
     */
    public function values(): static
    {
        return new static(array_values($this->collection));
    }

    /**
     * Applique une fonction de rappel à chaque élément de la collection.
     *
     * @template TMapValue
     * @param callable(TValue, TKey): TMapValue $callback Fonction à appliquer
     * @return static<TKey, TMapValue> Nouvelle collection avec les résultats
     */
    public function map(callable $callback): static
    {
        return new static(array_map($callback, $this->collection));
    }

    /**
     * Filtre les éléments de la collection selon une fonction de rappel.
     *
     * @param callable(TValue, TKey): bool $callback Fonction de filtrage
     * @return static<TKey, TValue> Nouvelle collection filtrée
     */
    public function filter(callable $callback): static
    {
        return new static(array_filter($this->collection, $callback, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * Joint les éléments de la collection en une chaîne de caractères.
     *
     * @param string $separator Séparateur entre les éléments
     * @param string|null $field Nom du champ à utiliser (optionnel)
     * @return string Chaîne résultante
     */
    public function implode(string $separator = '', ?string $field = null): string
    {
        if ($field !== null) {
            $values = array_column($this->collection, $field);
        } else {
            $values = $this->collection;
        }

        $values = array_map(function ($value) {
            if (is_scalar($value) || is_null($value)) {
                return (string) $value;
            }
            
            if (is_object($value)) {
                if (method_exists($value, '__toString')) {
                    return $value->__toString();
                }
                return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }, $values);

        return implode($separator, $values);
    }

    /**
     * Supprime et retourne le premier élément de la collection.
     *
     * @return TValue|null Premier élément ou null si la collection est vide
     */
    public function shift(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        return array_shift($this->collection);
    }

    /**
     * Trie la collection.
     *
     * @param 'asc'|'desc' $order Direction du tri ('asc' pour ascendant, 'desc' pour descendant)
     * @return static<TKey, TValue> Nouvelle collection triée
     * @throws InvalidArgumentException Si l'ordre de tri est invalide
     */
    public function sort(string $order = 'asc'): static
    {
        $collection = $this->collection;
        
        match ($order) {
            'asc' => sort($collection),
            'desc' => rsort($collection),
            default => throw new InvalidArgumentException('L\'ordre de tri doit être "asc" ou "desc"')
        };

        return new static($collection);
    }

    /**
     * Prend un nombre spécifié d'éléments de la collection.
     *
     * @param int $limit Nombre d'éléments à prendre (négatif pour prendre depuis la fin)
     * @return static<TKey, TValue> Nouvelle collection avec les éléments sélectionnés
     */
    public function take(int $limit): static
    {
        if ($limit === 0) {
            return new static([]);
        }

        if ($limit < 0) {
            return new static(array_slice($this->collection, $limit, abs($limit), true));
        }

        return new static(array_slice($this->collection, 0, $limit, true));
    }

    /**
     * Extrait une portion de la collection.
     *
     * @param int $offset Position de départ
     * @param int|null $length Nombre d'éléments (optionnel)
     * @return static<TKey, TValue> Nouvelle collection avec la portion extraite
     */
    public function slice(int $offset, ?int $length = null): static
    {
        return new static(array_slice($this->collection, $offset, $length, true));
    }

    /**
     * Convertit la collection en tableau.
     *
     * @return array<TKey, TValue> Tableau des éléments
     */
    public function toArray(): array
    {
        return $this->collection;
    }

    /**
     * Vérifie si la collection est vide.
     *
     * @return bool true si la collection est vide, false sinon
     */
    public function isEmpty(): bool
    {
        return empty($this->collection);
    }

    /**
     * Retourne les clés de la collection.
     *
     * @return static<int, TKey> Nouvelle collection contenant les clés
     */
    public function keys(): static
    {
        return new static(array_keys($this->collection));
    }

      /**
     * Prend une ou plusieurs clés, au hasard dans une collection
     *
     * @param $limit
     * @return $this
     */
    public function rand($limit): Collection
    {
        return new static(array_rand($this->collection, $limit));
    }

    /**
     * Vérifie si une ou plusieurs valeurs existent dans la collection.
     * Si une valeur est une chaîne, vérifie d'abord si elle existe comme clé.
     * Sinon, vérifie si elle existe comme valeur.
     *
     * @param mixed ...$values Valeurs à vérifier
     * @return bool true si toutes les valeurs existent, false sinon
     */
    public function exists(mixed ...$values): bool
    {
        foreach ($values as $value) {
            if (is_callable($value)) {
                if (!$this->filter($value)->isNotEmpty()) {
                    return false;
                }
            } elseif (is_string($value)) {
                if (!array_key_exists($value, $this->collection) && !in_array($value, $this->collection, true)) {
                    return false;
                }
            } else {
                if (!in_array($value, $this->collection, true)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Vérifie si la collection n'est pas vide.
     *
     * @return bool true si la collection n'est pas vide, false sinon
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * Crée une nouvelle collection avec uniquement les clés spécifiées.
     *
     * @param array<array-key>|string $keys Clés à conserver
     * @return static<TKey, TValue> Nouvelle collection filtrée
     */
    public function only(array|string ...$keys): static
    {
        $keys = array_merge(...array_map(
            fn ($key) => is_array($key) ? $key : [$key],
            $keys
        ));

        return new static(array_intersect_key($this->collection, array_flip($keys)));
    }

    /**
     * Crée une nouvelle collection sans les clés spécifiées.
     *
     * @param array<array-key>|string $keys Clés à exclure
     * @return static<TKey, TValue> Nouvelle collection filtrée
     */
    public function except(array|string ...$keys): static
    {
        $keys = array_merge(...array_map(
            fn ($key) => is_array($key) ? $key : [$key],
            $keys
        ));

        return new static(array_diff_key($this->collection, array_flip($keys)));
    }

    /**
     * Extrait une ou plusieurs valeurs de la collection selon une clé.
     *
     * @param string $value Clé des valeurs à extraire
     * @param string|null $key Clé à utiliser comme index (optionnel)
     * @return static<array-key, mixed> Nouvelle collection avec les valeurs extraites
     */
    public function pluck(string $value, ?string $key = null): static
    {
        $results = [];

        foreach ($this->collection as $item) {
            $itemValue = is_object($item) ? $item->$value : $item[$value];

            if ($key !== null) {
                $itemKey = is_object($item) ? $item->$key : $item[$key];
                $results[$itemKey] = $itemValue;
            } else {
                $results[] = $itemValue;
            }
        }

        return new static($results);
    }

    /**
     * Alias de column() pour la compatibilité.
     * Retourne une collection avec les valeurs d'une colonne indexée par une autre colonne.
     *
     * @param string $value Nom de la colonne pour les valeurs
     * @param string $key Nom de la colonne pour les clés
     * @return static|false Nouvelle collection ou false si les paramètres sont invalides
     */
    public function lists($value, $key)
    {
        if (is_string($key) && is_string($value)) {
            return $this->column($value, $key);
        }
        return false;
    }

    /**
     * Calcule la somme des valeurs d'un tableau.
     *
     * @param array<int|string, int|float> $values Tableau de valeurs à sommer
     * @return int|float Somme des valeurs
     */
    public function sumKey(array $values): int|float
    {
        return array_sum($values);
    }

    /**
     * Calcule la moyenne des valeurs d'un tableau.
     *
     * @param array<int|string, int|float> $values Tableau de valeurs
     * @return int|float Moyenne des valeurs
     * @throws \DivisionByZeroError Si le tableau est vide
     */
    public function avgKey(array $values): int|float
    {
        if (empty($values)) {
            throw new \DivisionByZeroError();
        }
        return $this->sumKey($values) / count($values);
    }
}
