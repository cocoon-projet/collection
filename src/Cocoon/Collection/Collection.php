<?php

declare(strict_types=1);

namespace Cocoon\Collection;

use ArrayAccess;
use ArrayIterator;
use Cocoon\Collection\Features\Criteria;
use Countable;
use IteratorAggregate;

/**
 * Class Collection
 * @package Cocoon\Collection
 */
class Collection implements Countable, ArrayAccess, IteratorAggregate
{
    /**
     *  Les éléments de la collection
     *
     * @var array
     */
    private $collection = [];

    use Criteria;

    /**
     * Collection constructor.
     * @param array $collection
     */
    public function __construct($collection = [])
    {
        $this->collection = $collection;
    }
    /**
     * Retourne le nombre d'élément de la collection
     *
     * @return int
     */
    public function count(): int
    {
        return sizeof($this->collection);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->collection);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->collection[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->collection[] = $value;
        } else {
            $this->collection[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->collection[$offset]);
    }

    /**
     * Enregistre un nouvel élément dans la collection
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Retourne un élément de la collection
     *
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        if ($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }
        return $default;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->collection);
    }
    /**
     * Retourne le premier élément de la collection
     * @return array
     */
    public function first()
    {
        return reset($this->collection);
    }

    /**
     * Retourne le dernier élément de la collection
     *
     * @return false|mixed
     */
    public function last()
    {
        return end($this->collection);
    }
    /**
     * Convertir un tableau multidimensionnel en un simple tableau
     *
     * @param array $array
     * @return array
     */
    public function flatten($array = []): array
    {
        if (sizeof($array) == 0) {
            $array = $this->collection;
        }
        $flattened = [];
        array_walk_recursive($array, function ($value, $key) use (&$flattened) {
            if (!is_array($key) && !is_int($key)) {
                $flattened[$key] = $value;
            } else {
                $flattened[] = $value;
            }
        });
        return $flattened;
    }

    /**
     * Retourne la collection ou la collection modifiée.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->collection;
    }

    /**
     * Retourne la somme d'une collection ou d'une clef
     *
     * @param null $column
     * @return float|int
     */
    public function sum($column = null)
    {
        if ($column != null && is_string($column)) {
            $result = $this->column($column);
            return $result->sum();
        } else {
            return array_sum($this->collection);
        }
    }
    /**
     *  Calcule la somme d'une clé de la collection
     *
     * @param array $value
     * @return float|int
     */
    public function sumKey(array $value = [])
    {
        return array_sum($value);
    }

    /**
     *  Sépare une collection en tableaux de taille inférieure
     *
     * @param $limit
     * @return array
     */
    public function chunk($limit): array
    {
        return array_chunk($this->collection, $limit);
    }

    /**
     * Retourne les valeurs d'une colonne d'une collection
     *
     * @param ...$args
     * @return $this
     */
    public function column(...$args): Collection
    {
        return new static(array_column($this->collection, ...$args));
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
     * Calcul la moyenne d'une collection ou d'une colonne de la collection
     *
     * @param null $column
     * @return float|int
     */
    public function avg($column = null)
    {
        if ($column != null) {
            $result = $this->column($column);
            return $this->sum($column) / count($result);
        } else {
            $count = $this->count();
            return $this->sum() / $count;
        }
    }
    // TODO a voir
    public function avgKey($value = [])
    {
        return $this->sumKey($value) / count($value);
    }

    /**
     * Retourne une nouvelle collection des valeurs de la collection
     *
     * @return $this
     */
    public function values(): Collection
    {
        return new static(array_values($this->collection));
    }

    /**
     * Applique une fonction sur les éléments d'une collection
     *
     * @param $callback
     * @return $this
     */
    public function map($callback): Collection
    {
        return new static(array_map($callback, $this->collection));
    }

    /**
     * Filtre les éléments d'une collection
     *
     * @param $callback
     * @return $this
     */
    public function filter($callback): Collection
    {
        return new static(array_filter($this->collection, $callback, ARRAY_FILTER_USE_BOTH));
    }

    /**
     *  Rassemble les éléments d'une collection en une chaîne
     *
     * @param ...$args
     * @return string
     */
    public function implode(...$args): string
    {
        if (empty($args[0])) {
            $sep = ',';
        }
        if (count($args) == 1) {
            if (strlen(trim($args[0], ' ')) == 1) {
                $sep = $args[0];
                return implode($sep, $this->all());
            } else {
                if (strlen($args[0]) > 1) {
                    $sep = ',';
                    $key = $args[0];
                    $new = $this->map(function ($collect) use ($key) {
                        return is_object($collect) ? $collect->{$key} : $collect[$key];
                    });
                    return implode($sep, $new->all());
                }
            }
        } elseif (count($args) === 2) {
            $sep = $args[1];
            $key = $args[0];
            $new = $this->map(function ($collect) use ($key) {
                return is_object($collect) ? $collect->{$key} : $collect[$key];
            });
            return implode($sep, $new->all());
        }
        return implode($sep, $this->all());
    }
    /**
     *  Dépile un élément au début d'une collection et le retourne
     *
     * @return void
     */
    public function shift()
    {
        return array_shift($this->collection);
    }

    /**
     * Trie une collection en ordre croissant ou décroissant
     *
     * @param string $order
     * @return $this
     */
    public function sort($order = 'asc'): Collection
    {
        if ($order === 'desc') {
            rsort($this->collection);
        } else {
            sort($this->collection);
        }
        return $this;
    }

    /**
     * Retourne une nouvelle collection du nombre d'éléments définient.
     *
     * @param $limit
     * @return $this
     */
    public function take($limit): Collection
    {
        $offset = 0;
        if ($limit < 0) {
            $offset = $limit;
            $limit = abs($limit);
        }
        return new static(array_slice($this->collection, $offset, $limit));
    }
    /**
     * Retourne la collection
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->collection;
    }

    /**
     * Vérifie si une une collection vide
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->collection);
    }

    /**
     * Retourne une nouvelle collection avec les clés de la collection
     *
     * @return $this
     */
    public function keys(): Collection
    {
        return new static(array_keys($this->collection));
    }
    // TODO a tester
    /**
     * Vérifie si une clé de la collection existe
     *
     * @param ...$args
     * @return bool
     */
    public function exists(...$args)
    {
        if (count($args) > 1) {
            foreach ($args as $key) {
                if (!$this->offsetExists($key)) {
                    return false;
                }
            }
        }
        return $this->offsetExists($args[0]);
    }

    /**
     * Retoune uen nouvelle collection avec les clés spécifiées
     *
     * @param ...$args
     * @return $this
     */
    public function only(...$args): Collection
    {
        $collect = [];
        foreach ($this->collection as $key => $value) {
            if (in_array($key, $args)) {
                $collect[$key] = $value;
            }
        }
        return new static($collect);
    }

    /**
     * Retourne une nouvelle collection sans les clés spécifiées
     *
     * @param ...$args
     * @return $this
     */
    public function except(...$args): Collection
    {
        $collect = [];
        foreach ($this->collection as $key => $value) {
            if (!in_array($key, $args)) {
                $collect[$key] = $value;
            }
        }
        return new static($collect);
    }
    /**
     * Retourne toutes les valeurs d'une clé
     *
     * @param string $key
     * @return object Collection
     */
    public function pluck(...$args)
    {
        $collect = [];
        if (count($args) == 1) {
            $return = $this->map(function ($item) use ($args) {
                return is_object($item) ? $item->$args[0] : $item[$args[0]];
            });
            return $return;
        }
        if(count($args) == 2) {
            $return = $this->map(function ($item) use ($args) {
                return is_object($item) ? [$item->$args[1] => $item->$args[0]] : [$item[$args[1]] => $item[$args[0]]];
            });
            return $return;
        }
    }
}
