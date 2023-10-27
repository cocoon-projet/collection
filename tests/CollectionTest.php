<?php


namespace Test;

use Cocoon\Collection\Collection;

class CollectionTest extends \PHPUnit\Framework\TestCase
{
    protected $numCollection = [1,2,3,4,5];
    protected $object = [];

    public function testEmptyCollection()
    {
        $collection = new Collection([]);
        $this->assertTrue($collection->isEmpty());
    }

    public function testCountCollection()
    {
        $collection = new Collection($this->numCollection);
        $this->assertSame(5, $collection->count());
    }

    public function testAddSetAndGetCollection()
    {
        $collection = new Collection([]);
        $collection->set('name', 'john doe');
        $this->assertSame('john doe', $collection->get('name'));
    }

    public function testFirstItemCollection()
    {
        $collection = new Collection(['franck', 'didier','maurice', 'henri', 'jean', 'alvin']);
        $this->assertSame('franck', $collection->first());
    }

    public function testLastItemCollection()
    {
        $collection = new Collection(['franck', 'didier','maurice', 'henri', 'jean', 'alvin']);
        $this->assertSame('alvin', $collection->last());
    }

    public function testAllReturnCollection()
    {
        $persons = ['franck', 'didier','maurice', 'henri', 'jean', 'alvin'];
        $collection = new Collection($persons);
        $this->assertSame($persons, $collection->all());
    }

    public function testSumNumberCollection()
    {
        $collection = new Collection($this->numCollection);
        $this->assertSame(array_sum($this->numCollection), $collection->sum());
    }

    public function testSumColumnNumberCollection()
    {
        $collect = [
            ['product' => 'ps5', 'price' => 250 ],
            ['product' => 'xbox', 'price' => 300 ]
        ];
        $collection = new Collection($collect);
        $this->assertSame(550, $collection->sum('price'));
    }

    public function testSumKeyColumnCollection()
    {
        $array = [
            ['name' => 'franck', 'age' => 53, 'notes' => [10, 14, 12]],
            ['name' => 'maurice', 'age' => 63, 'notes' => [15, 15, 15]],
            ['name' => 'henri', 'age' => 45, 'notes' => [5, 10, 15]],

        ];

        $collection = new Collection($array);
        $total = [36, 45, 30];
        $i = 0;
        foreach ($collection as $collect) {
            $this->assertSame($total[$i++], $collection->sumKey($collect['notes']));
        }
    }

    public function testChunkCollection()
    {
        $array = [0,1,2,3,4,5,6,7];
        $collection = new Collection($array);
        $count = count($collection->chunk(2));
        $this->assertSame(4, $count);
    }

    public function testColumnCollection()
    {
        $collect = [
            ['id' => 110, 'product' => 'ps5', 'price' => 250 ],
            ['id' => 111, 'product' => 'xbox', 'price' => 300 ]
        ];
        $result = [0 => 'ps5', 1 => 'xbox'];
        $result_two = [110 => 'ps5', 111 => 'xbox'];
        $collection = new Collection($collect);
        $this->assertSame($result[0], $collection->column('product')[0]);
        $this->assertSame($result_two[111], $collection->column('product', 'id')[111]);
    }

    public function testRandCollection()
    {
        $collection = new Collection(['franck', 'didier','maurice', 'henri', 'jean', 'alvin']);
        $input = $collection->rand(2);
        $this->assertSame(2, count($input));
        $this->assertTrue(is_string($collection[$input[0]]));
    }

    public function testAvgNumberCollection()
    {
        $collection = new Collection([1,2,3,4,5]);
        $this->assertSame(3, $collection->avg());
    }

    public function testAvgColumnCollection()
    {
        $collect = [
            ['product' => 'ps5', 'price' => 250 ],
            ['product' => 'xbox', 'price' => 350 ]
        ];
        $collection = new Collection($collect);
        $this->assertSame(300, $collection->avg('price'));
    }

    public function testAvgKeyColumnCollection()
    {
        $array = [
            ['name' => 'franck', 'age' => 53, 'notes' => [10, 14, 12]],
            ['name' => 'maurice', 'age' => 63, 'notes' => [15, 15, 15]],
            ['name' => 'henri', 'age' => 45, 'notes' => [5, 10, 15]],

        ];

        $collection = new Collection($array);
        $moyenne = [12, 15, 10];
        $i = 0;
        foreach ($collection as $collect) {
            $this->assertSame($moyenne[$i++], $collection->avgKey($collect['notes']));
        }
    }

    public function testValuesCollection()
    {
        $array = array("product" => "xbox", "price" => 500);

        $collection = new Collection($array);
        $this->assertSame([0 => 'xbox', 1 => 500], $collection->values()->toArray());
    }

    public function testFilterCollection()
    {
        $this->object[] = new Student('franck', 53, [10,20,20]);
        $this->object[] = new Student('maurice', 63, [15,15,15]);
        $this->object[] = new Student('henri', 45, [5,10,15]);
        $this->object[] = new Student('didier', 56, [13,18,11]);
        $this->object[] = new Student('jean', 32, [0,13,17]);
        $this->object[] = new Student('alvin', 25, [11,12,13]);
        $this->object[] = new Student('romain', 37, [12,13,14]);
        $this->object[] = new Student('benoit', 22, [7,19,16]);

        $collection = new Collection($this->object);
        $filter = $collection->filter(function ($student) {
            return $student->age > 40;
        });
        $this->assertSame(4, count($filter));
    }

    public function testMapCollection()
    {
        $this->object[] = new Student('franck', 53, [10,20,20]);
        $this->object[] = new Student('maurice', 63, [15,15,15]);
        $this->object[] = new Student('henri', 45, [5,10,15]);
        $this->object[] = new Student('didier', 56, [13,18,11]);
        $this->object[] = new Student('jean', 32, [0,13,17]);
        $this->object[] = new Student('alvin', 25, [11,12,13]);
        $this->object[] = new Student('romain', 37, [12,13,14]);
        $this->object[] = new Student('benoit', 22, [7,19,16]);

        $collection = new Collection($this->object);
        $filter = $collection->filter(function ($student) {
            return $student->age > 40;
        })->map(function ($student) {
            return strtoupper($student->name);
        })->implode(', ');
        $this->assertSame('FRANCK, MAURICE, HENRI, DIDIER', $filter);
    }
    // shift
    public function testShiftCollection()
    {
        $test = new Collection(['un', 'deux', 'trois', 'quatre']);
        $this->assertSame('un', $test->shift());
    }
    // sort
    public function testSortCollection()
    {
        $test = new Collection(['un', 'deux', 'trois', 'quatre']);
        $this->assertEquals(['deux', 'quatre', 'trois', 'un'], $test->sort()->toArray());
        $this->assertEquals(['un', 'trois', 'quatre', 'deux'], $test->sort('desc')->toArray());
    }
    // take
    public function testTakeCollection()
    {
        $test = new Collection(['un', 'deux', 'trois', 'quatre','cinq','six']);
        $this->assertEquals(['un','deux'], $test->take(2)->toArray());
        $this->assertEquals(['cinq','six'], $test->take(-2)->toArray());
    }
    // key
    public function testKeysCollection()
    {
        $test = new Collection(['id' => 100, 'product' => 'banane', 'price'=> 10]);
        $this->assertEquals(['id','product', 'price'], $test->keys()->toArray());
    }
    // exist
    public function testExistCollection()
    {
        $test = new Collection(['id' => 100, 'product' => 'banane', 'price'=> 10]);
        $this->assertTrue($test->exists('product'));
    }
    // only
}
