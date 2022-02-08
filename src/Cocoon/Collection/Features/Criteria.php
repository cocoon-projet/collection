<?php
declare(strict_types=1);
namespace Cocoon\Collection\Features;

trait Criteria
{
    protected function criteria($operator, $key, $value): bool
    {
        switch ($operator) {
            default:
            case '=':
            case '==':
                return $key == $value;
            case '!=':
            case '<>':
                return $key != $value;
            case '<':
                return $key < $value;
            case '>':
                return $key > $value;
            case '<=':
                return $key <= $value;
            case '>=':
                return $key >= $value;
            case '===':
                return $key === $value;
            case '!==':
                return $key !== $value;
        }
    }

    /**
     * Retourne une collection filtrée par clé et valeur
     *
     * @param ...$args
     * @return \Cocoon\Collection\Collection
     */
    public function where(...$args)
    {
        if (count($args) == 2) {
            $key = $args[0];
            $operator = '=';
            $value = $args[1];
            return $this->filter(function ($collect) use ($key, $operator, $value) {
                $keyName = is_object($collect) ? $collect->{$key} : $collect[$key];
                return $this->criteria($operator, $keyName, $value);
            });
        }
        if (count($args) == 3) {
            $key = $args[0];
            $operator = $args[1];
            $value = $args[2];
            return $this->filter(function ($collect) use ($key, $operator, $value) {
                $keyName = is_object($collect) ? $collect->{$key} : $collect[$key];
                return $this->criteria($operator, $keyName, $value);
            });
        }
    }

    /**
     * Supprime les éléments de la collection qui n'ont pas de valeur d'élément spécifiée contenue dans le tableau donné
     *
     * @param $key
     * @param array $search
     * @return \Cocoon\Collection\Collection
     */
    public function whereIn($key, $search = [])
    {
        return $this->filter(function ($collect) use ($key, $search) {
            $keyName = is_object($collect) ? $collect->{$key} : $collect[$key];
            return in_array($keyName, $search);
        });
    }

    public function whereNotIn($key, $search = [])
    {
        return $this->filter(function ($collect) use ($key, $search) {
            $keyName = is_object($collect) ? $collect->{$key} : $collect[$key];
            return !in_array($keyName, $search);
        });
    }

    public function whereBetween($key, $search = [])
    {
        return $this->filter(function ($collect) use ($key, $search) {
            $keyName = is_object($collect) ? $collect->{$key} : $collect[$key];
            return $keyName >= $search[0] and $keyName <= $search[1];
        });
    }

    public function whereNotBetween($key, $search = [])
    {
        return $this->filter(function ($collect) use ($key, $search) {
            $keyName = is_object($collect) ? $collect->{$key} : $collect[$key];
            return $keyName < $search[0] or $keyName > $search[1];
        });
    }
    /**
     * Retourne une collection ordonnée pas clef ascendante ou descendante
     *
     * @param string $key
     * @param string $order
     * @return Criteria
     */
    public function orderBy($key, $order = 'asc')
    {
        $sortable = [];
        foreach ($this->collection as $collect) {
            $keyName = is_object($collect) ? $collect->{$key} : $collect[$key];
            $sortable[$keyName] = $collect;
        }
        
        switch ($order) {
            case 'asc':
                ksort($sortable);
                break;
            case 'desc':
                krsort($sortable);
                break;
        }
        return new static(array_values($sortable));
    }

    /**
     * Retourne une collection groupé par clef
     *
     * @param $key
     * @return Criteria
     */
    public function groupBy($key)
    {
        $collect = [];

        foreach ($this->collection as $value) {
            if (array_key_exists($key, $value)) {
                $keyName = is_object($value) ? $value->{$key} : $value[$key];
                $collect[$keyName][] = $value;
            }
        }

        return new static($collect);
    }
}
