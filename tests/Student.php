<?php


namespace Test;

class Student
{
    public $name;
    public $age;
    public $notes;

    public function __construct($name, $age, $notes)
    {
        $this->name = $name;
        $this->age = $age;
        $this->notes = $notes;
    }
}
