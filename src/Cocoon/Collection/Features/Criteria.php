<?php
declare(strict_types=1);
namespace Cocoon\Collection\Features;

use InvalidArgumentException;

/**
 * Trait Criteria
 * 
 * Fournit des méthodes de filtrage et de tri similaires à SQL pour les collections.
 * 
 * @template TKey of array-key
 * @template TValue
 * @mixin \Cocoon\Collection\Collection
 */
trait Criteria
{
    /**
     * Compare deux valeurs selon l'opérateur spécifié.
     *
     * @param string $operator Opérateur de comparaison ('=', '!=', '<', '>', '<=', '>=', '===', '!==')
     * @param mixed $left Valeur de gauche à comparer
     * @param mixed $right Valeur de droite à comparer
     * @return bool Résultat de la comparaison
     * @throws InvalidArgumentException Si l'opérateur n'est pas valide
     */
    protected function criteria(string $operator, mixed $left, mixed $right): bool
    {
        return match($operator) {
            '=', '==' => $left == $right,
            '!=', '<>' => $left != $right,
            '<' => $left < $right,
            '>' => $left > $right,
            '<=' => $left <= $right,
            '>=' => $left >= $right,
            '===' => $left === $right,
            '!==' => $left !== $right,
            default => throw new InvalidArgumentException("Opérateur '$operator' non valide")
        };
    }

    /**
     * Filtre la collection selon une condition WHERE.
     * 
     * @param string $key Clé à comparer
     * @param string|mixed $operatorOrValue Opérateur de comparaison ou valeur si l'opérateur est '='
     * @param mixed|null $value Valeur à comparer (optionnel si $operatorOrValue est la valeur)
     * @return \Cocoon\Collection\Collection<TKey, TValue> Nouvelle collection filtrée
     */
    public function where(string $key, mixed $operatorOrValue, mixed $value = null): \Cocoon\Collection\Collection
    {
        if ($value === null) {
            $operator = '=';
            $value = $operatorOrValue;
        } else {
            $operator = $operatorOrValue;
        }

        return $this->filter(function ($item) use ($key, $operator, $value) {
            $itemValue = is_object($item) ? $item->$key : $item[$key];
            return $this->criteria($operator, $itemValue, $value);
        });
    }

    /**
     * Filtre la collection pour ne garder que les éléments dont la valeur est dans un tableau.
     *
     * @param string $key Clé à vérifier
     * @param array<array-key, mixed> $values Tableau des valeurs autorisées
     * @return \Cocoon\Collection\Collection<TKey, TValue> Nouvelle collection filtrée
     */
    public function whereIn(string $key, array $values): \Cocoon\Collection\Collection
    {
        return $this->filter(function ($item) use ($key, $values) {
            $itemValue = is_object($item) ? $item->$key : $item[$key];
            return in_array($itemValue, $values, true);
        });
    }

    /**
     * Filtre la collection pour ne garder que les éléments dont la valeur n'est pas dans un tableau.
     *
     * @param string $key Clé à vérifier
     * @param array<array-key, mixed> $values Tableau des valeurs exclues
     * @return \Cocoon\Collection\Collection<TKey, TValue> Nouvelle collection filtrée
     */
    public function whereNotIn(string $key, array $values): \Cocoon\Collection\Collection
    {
        return $this->filter(function ($item) use ($key, $values) {
            $itemValue = is_object($item) ? $item->$key : $item[$key];
            return !in_array($itemValue, $values, true);
        });
    }

    /**
     * Filtre la collection pour ne garder que les éléments dont la valeur est comprise entre deux bornes.
     *
     * @param string $key Clé à vérifier
     * @param array{0: mixed, 1: mixed} $range Tableau contenant les bornes [min, max]
     * @return \Cocoon\Collection\Collection<TKey, TValue> Nouvelle collection filtrée
     */
    public function whereBetween(string $key, array $range): \Cocoon\Collection\Collection
    {
        if (!isset($range[0], $range[1])) {
            throw new InvalidArgumentException('Le tableau de bornes doit contenir exactement deux valeurs [min, max]');
        }

        return $this->filter(function ($item) use ($key, $range) {
            $itemValue = is_object($item) ? $item->$key : $item[$key];
            return $itemValue >= $range[0] && $itemValue <= $range[1];
        });
    }

    /**
     * Filtre la collection pour ne garder que les éléments dont la valeur n'est pas comprise entre deux bornes.
     *
     * @param string $key Clé à vérifier
     * @param array{0: mixed, 1: mixed} $range Tableau contenant les bornes [min, max]
     * @return \Cocoon\Collection\Collection<TKey, TValue> Nouvelle collection filtrée
     */
    public function whereNotBetween(string $key, array $range): \Cocoon\Collection\Collection
    {
        if (!isset($range[0], $range[1])) {
            throw new InvalidArgumentException('Le tableau de bornes doit contenir exactement deux valeurs [min, max]');
        }

        return $this->filter(function ($item) use ($key, $range) {
            $itemValue = is_object($item) ? $item->$key : $item[$key];
            return $itemValue < $range[0] || $itemValue > $range[1];
        });
    }

    /**
     * Filtre la collection pour ne garder que les éléments dont la valeur est NULL.
     *
     * @param string $key Clé à vérifier
     * @return \Cocoon\Collection\Collection<TKey, TValue> Nouvelle collection filtrée
     */
    public function whereNull(string $key): \Cocoon\Collection\Collection
    {
        return $this->filter(function ($item) use ($key) {
            $value = is_object($item) ? $item->$key : ($item[$key] ?? null);
            return $value === null;
        });
    }

    /**
     * Filtre la collection pour ne garder que les éléments dont la valeur n'est pas NULL.
     *
     * @param string $key Clé à vérifier
     * @return \Cocoon\Collection\Collection<TKey, TValue> Nouvelle collection filtrée
     */
    public function whereNotNull(string $key): \Cocoon\Collection\Collection
    {
        return $this->filter(function ($item) use ($key) {
            $value = is_object($item) ? $item->$key : ($item[$key] ?? null);
            return $value !== null;
        });
    }

    /**
     * Filtre la collection pour ne garder que les éléments dont la valeur correspond à un motif.
     *
     * @param string $key Clé à vérifier
     * @param string $pattern Motif à rechercher (ex: '%text%')
     * @return \Cocoon\Collection\Collection<TKey, TValue> Nouvelle collection filtrée
     */
    public function whereLike(string $key, string $pattern): \Cocoon\Collection\Collection
    {
        $pattern = str_replace(['%', '_'], ['.*', '.'], preg_quote($pattern, '/'));
        return $this->filter(function ($item) use ($key, $pattern) {
            $value = is_object($item) ? $item->$key : ($item[$key] ?? '');
            return preg_match('/^' . $pattern . '$/i', (string)$value);
        });
    }

    /**
     * Joint une autre collection sur une clé commune.
     *
     * @param \Cocoon\Collection\Collection $other Collection à joindre
     * @param string $localKey Clé locale
     * @param string $otherKey Clé de l'autre collection
     * @param string $type Type de jointure ('inner', 'left', 'right')
     * @return \Cocoon\Collection\Collection<TKey, TValue> Nouvelle collection avec les éléments joints
     * @throws InvalidArgumentException Si le type de jointure n'est pas valide
     */
    public function join(\Cocoon\Collection\Collection $other, string $localKey, string $otherKey, string $type = 'inner'): \Cocoon\Collection\Collection
    {
        if (!in_array($type, ['inner', 'left', 'right'], true)) {
            throw new InvalidArgumentException("Type de jointure '$type' non valide");
        }

        $result = [];
        foreach ($this->collection as $item) {
            $localValue = is_object($item) ? $item->$localKey : $item[$localKey];
            $matched = false;

            foreach ($other->all() as $otherItem) {
                $otherValue = is_object($otherItem) ? $otherItem->$otherKey : $otherItem[$otherKey];
                
                if ($localValue === $otherValue) {
                    $matched = true;
                    $result[] = array_merge(
                        is_array($item) ? $item : (array)$item,
                        is_array($otherItem) ? $otherItem : (array)$otherItem
                    );
                }
            }

            if (!$matched && ($type === 'left' || $type === 'outer')) {
                $result[] = is_array($item) ? $item : (array)$item;
            }
        }

        return new static($result);
    }

    /**
     * Compte les occurrences uniques d'une valeur.
     *
     * @param string $key Clé à compter
     * @return \Cocoon\Collection\Collection<TKey, TValue> Collection avec les comptages
     */
    public function countBy(string $key): \Cocoon\Collection\Collection
    {
        $counts = [];
        foreach ($this->collection as $item) {
            $value = is_object($item) ? $item->$key : $item[$key];
            $counts[$value] = ($counts[$value] ?? 0) + 1;
        }
        return new static($counts);
    }

    /**
     * Calcule des statistiques sur une colonne numérique.
     *
     * @param string $key Clé à analyser
     * @return array{min: float|int, max: float|int, avg: float|int, sum: float|int, count: int}
     */
    public function stats(string $key): array
    {
        $values = array_map(
            fn($item) => is_object($item) ? $item->$key : $item[$key],
            array_filter($this->collection, fn($item) => 
                is_numeric(is_object($item) ? $item->$key : $item[$key])
            )
        );
        
        return [
            'min' => empty($values) ? 0 : min($values),
            'max' => empty($values) ? 0 : max($values),
            'avg' => empty($values) ? 0 : array_sum($values) / count($values),
            'sum' => array_sum($values),
            'count' => count($values)
        ];
    }

    /**
     * Groupe les éléments par une clé.
     *
     * @param string|callable $key Clé ou callback de groupement
     * @return \Cocoon\Collection\Collection<TKey, \Cocoon\Collection\Collection<TKey, TValue>> Nouvelle collection groupée
     */
    public function groupBy(string|callable $key): \Cocoon\Collection\Collection
    {
        $groups = new \Cocoon\Collection\Collection([]);

        foreach ($this->collection as $item) {
            if (is_callable($key)) {
                $groupKey = $key($item);
            } else {
                $groupKey = is_object($item) ? $item->$key : ($item[$key] ?? null);
            }

            if ($groupKey !== null) {
                if (!isset($groups[$groupKey])) {
                    $groups[$groupKey] = new \Cocoon\Collection\Collection([]);
                }
                $currentGroup = $groups[$groupKey];
                $currentGroup[] = $item;
                $groups[$groupKey] = $currentGroup;
            }
        }

        return $groups;
    }

    /**
     * Groupe les éléments par plusieurs clés.
     *
     * @param string ...$keys Clés de groupement
     * @return \Cocoon\Collection\Collection<TKey, TValue> Nouvelle collection groupée de manière hiérarchique
     */
    public function groupByMultiple(string ...$keys): \Cocoon\Collection\Collection
    {
        $result = [];
        foreach ($this->collection as $item) {
            $current = &$result;
            foreach ($keys as $key) {
                $value = is_object($item) ? $item->$key : $item[$key];
                if (!isset($current[$value])) {
                    $current[$value] = [];
                }
                $current = &$current[$value];
            }
            $current[] = $item;
        }

        // Convertir récursivement les groupes en Collections
        $convertToCollections = function ($array) use (&$convertToCollections) {
            if (!is_array($array)) {
                return $array;
            }
            
            $result = [];
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    if (array_keys($value) === range(0, count($value) - 1)) {
                        // C'est un tableau numéroté, donc un groupe final
                        $result[$key] = new \Cocoon\Collection\Collection($value);
                    } else {
                        // C'est un niveau de groupement
                        $result[$key] = $convertToCollections($value);
                    }
                } else {
                    $result[$key] = $value;
                }
            }
            return new \Cocoon\Collection\Collection($result);
        };

        return $convertToCollections($result);
    }

    /**
     * Groupe les éléments par intervalle de valeurs.
     *
     * @param string $key Clé à utiliser
     * @param int|float $interval Taille de l'intervalle
     * @return \Cocoon\Collection\Collection<TKey, \Cocoon\Collection\Collection<TKey, TValue>> Collection groupée par intervalles
     */
    public function groupByRange(string $key, int|float $interval): \Cocoon\Collection\Collection
    {
        $groups = new \Cocoon\Collection\Collection([]);
        
        foreach ($this->collection as $item) {
            $value = is_object($item) ? $item->$key : $item[$key];
            $start = floor($value / $interval) * $interval;
            $groupKey = sprintf('%s-%s', $start, $start + $interval);
            
            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = new \Cocoon\Collection\Collection([]);
            }
            $currentGroup = $groups[$groupKey];
            $currentGroup[] = $item;
            $groups[$groupKey] = $currentGroup;
        }
        
        return $groups;
    }

    /**
     * Trie la collection selon une clé spécifique.
     *
     * @param string $key Clé de tri
     * @param 'asc'|'desc' $order Direction du tri ('asc' ou 'desc')
     * @return \Cocoon\Collection\Collection<TKey, TValue> Nouvelle collection triée
     * @throws InvalidArgumentException Si l'ordre de tri n'est pas valide
     */
    public function orderBy(string $key, string $order = 'asc'): \Cocoon\Collection\Collection
    {
        if (!in_array($order, ['asc', 'desc'], true)) {
            throw new InvalidArgumentException("L'ordre de tri doit être 'asc' ou 'desc'");
        }

        $sortable = [];
        foreach ($this->collection as $item) {
            $itemValue = is_object($item) ? $item->$key : $item[$key];
            $sortable[$itemValue][] = $item;
        }

        match($order) {
            'asc' => ksort($sortable),
            'desc' => krsort($sortable)
        };

        $result = [];
        foreach ($sortable as $items) {
            foreach ($items as $item) {
                $result[] = $item;
            }
        }

        return new static($result);
    }
}
