<?php


namespace Test;

use Cocoon\Collection\Collection;

class CollectionTest extends \PHPUnit\Framework\TestCase
{
    protected $numCollection = [1, 2, 3, 4, 5];
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
        $collection = new Collection(['franck', 'didier', 'maurice', 'henri', 'jean', 'alvin']);
        $this->assertSame('franck', $collection->first());
    }

    public function testLastItemCollection()
    {
        $collection = new Collection(['franck', 'didier', 'maurice', 'henri', 'jean', 'alvin']);
        $this->assertSame('alvin', $collection->last());
    }

    public function testAllReturnCollection()
    {
        $persons = ['franck', 'didier', 'maurice', 'henri', 'jean', 'alvin'];
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
            ['product' => 'ps5', 'price' => 250],
            ['product' => 'xbox', 'price' => 300]
        ];
        $collection = new Collection($collect);
        $this->assertSame(550, $collection->sum('price'));
    }

    public function testSumKeyColumnCollection()
    {
        $array = [
            ['name' => 'franck', 'age' => 53, 'notes' => [10, 14, 12]],
            ['name' => 'maurice', 'age' => 63, 'notes' => [15, 15, 15]],
            ['name' => 'henri', 'age' => 45, 'notes' => [5, 10, 15]]
        ];

        $collection = new Collection($array);
        $total = [36, 45, 30];
        $i = 0;
        foreach ($collection as $collect) {
            $this->assertSame($total[$i++], $collection->sumKey($collect['notes']));
        }
    }

    /**
     * Test direct de la méthode sumKey avec différents types de tableaux
     */
    public function testSumKey()
    {
        $collection = new Collection([]);
        
        // Test avec un tableau simple de nombres
        $this->assertEquals(15, $collection->sumKey([5, 5, 5]));
        
        // Test avec un tableau vide
        $this->assertEquals(0, $collection->sumKey([]));
        
        // Test avec des nombres décimaux
        $this->assertEquals(6.5, $collection->sumKey([1.5, 2.5, 2.5]));
        
        // Test avec des nombres négatifs
        $this->assertEquals(0, $collection->sumKey([-5, 5]));
    }

    public function testChunkCollection()
    {
        $array = [0, 1, 2, 3, 4, 5, 6, 7];
        $collection = new Collection($array);
        $count = count($collection->chunk(2));
        $this->assertSame(4, $count);
    }

    public function testColumnCollection()
    {
        $collect = [
            ['id' => 110, 'product' => 'ps5', 'price' => 250],
            ['id' => 111, 'product' => 'xbox', 'price' => 300]
        ];
        $result = [0 => 'ps5', 1 => 'xbox'];
        $result_two = [110 => 'ps5', 111 => 'xbox'];
        $collection = new Collection($collect);
        $this->assertSame($result[0], $collection->column('product')[0]);
        $this->assertSame($result_two[111], $collection->column('product', 'id')[111]);
    }

    public function testRandCollection()
    {
        $collection = new Collection(['franck', 'didier', 'maurice', 'henri', 'jean', 'alvin']);
        $input = $collection->rand(2);
        $this->assertSame(2, count($input));
        $this->assertTrue(is_string($collection[$input[0]]));
    }

    public function testAvgNumberCollection()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);
        $this->assertSame(3, $collection->avg());
    }

    public function testAvgColumnCollection()
    {
        $collect = [
            ['product' => 'ps5', 'price' => 250],
            ['product' => 'xbox', 'price' => 350]
        ];
        $collection = new Collection($collect);
        $this->assertSame(300, $collection->avg('price'));
    }

    public function testValuesCollection()
    {
        $array = array("product" => "xbox", "price" => 500);

        $collection = new Collection($array);
        $this->assertSame([0 => 'xbox', 1 => 500], $collection->values()->toArray());
    }

    public function testFilterCollection()
    {
        $this->object[] = new Student('franck', 53, [10, 20, 20]);
        $this->object[] = new Student('maurice', 63, [15, 15, 15]);
        $this->object[] = new Student('henri', 45, [5, 10, 15]);
        $this->object[] = new Student('didier', 56, [13, 18, 11]);
        $this->object[] = new Student('jean', 32, [0, 13, 17]);
        $this->object[] = new Student('alvin', 25, [11, 12, 13]);
        $this->object[] = new Student('romain', 37, [12, 13, 14]);
        $this->object[] = new Student('benoit', 22, [7, 19, 16]);

        $collection = new Collection($this->object);
        $filter = $collection->filter(function ($student) {
            return $student->age > 40;
        });
        $this->assertSame(4, count($filter));
    }

    public function testMapCollection()
    {
        $this->object[] = new Student('franck', 53, [10, 20, 20]);
        $this->object[] = new Student('maurice', 63, [15, 15, 15]);
        $this->object[] = new Student('henri', 45, [5, 10, 15]);
        $this->object[] = new Student('didier', 56, [13, 18, 11]);
        $this->object[] = new Student('jean', 32, [0, 13, 17]);
        $this->object[] = new Student('alvin', 25, [11, 12, 13]);
        $this->object[] = new Student('romain', 37, [12, 13, 14]);
        $this->object[] = new Student('benoit', 22, [7, 19, 16]);

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
        $test = new Collection(['un', 'deux', 'trois', 'quatre', 'cinq', 'six']);
        $this->assertEquals(['un', 'deux'], array_values($test->take(2)->toArray()));
        $this->assertEquals(['cinq', 'six'], array_values($test->take(-2)->toArray()));
    }
    // key
    public function testKeysCollection()
    {
        $test = new Collection(['id' => 100, 'product' => 'banane', 'price' => 10]);
        $this->assertEquals(['id', 'product', 'price'], $test->keys()->toArray());
    }
    // exist
    public function testExistCollection()
    {
        $test = new Collection(['id' => 100, 'product' => 'banane', 'price' => 10]);
        $this->assertTrue($test->exists('product'));
        $this->assertTrue($test->exists('product', 'id'));
        $this->assertFalse($test->exists('product', 'none'));
    }
    // only
    public function testOnlyCollection()
    {
        $test = new Collection(['id' => 100, 'product' => 'play station', 'price' => 500, 'quantite' => 35]);
        $this->assertEquals(['product' => 'play station', 'quantite' => 35], $test->only('product', 'quantite')->toArray());
    }
    // except
    public function testExcepCollection()
    {
        $test = new Collection(['id' => 100, 'product' => 'play station', 'price' => 500, 'quantite' => 35]);
        $this->assertEquals(['id' => '100', 'price' => 500], $test->except('product', 'quantite')->toArray());
    }
    // implode
    public function testImplodeCollection()
    {
        // Test avec tableau simple
        $test = new Collection(['un', 'deux', 'trois', 'quatre']);
        $this->assertEquals('un,deux,trois,quatre', $test->implode(','));
        $this->assertEquals('un-deux-trois-quatre', $test->implode('-'));
        
        // Test avec tableau associatif
        $shop = [
            ['id' => 100, 'product' => 'Play Station', 'price' => 450, 'quantite' => 35],
            ['id' => 101, 'product' => 'Sony TV', 'price' => 865, 'quantite' => 9],
            ['id' => 102, 'product' => 'Samsung TV', 'price' => 500, 'quantite' => 35],
            ['id' => 103, 'product' => 'TCL TV', 'price' => 600, 'quantite' => 12],
        ];
        $test = new Collection($shop);
        
        // Test avec une clé spécifique
        $this->assertEquals('Play Station,Sony TV,Samsung TV,TCL TV', $test->pluck('product')->implode(','));
        $this->assertEquals('Play Station-Sony TV-Samsung TV-TCL TV', $test->pluck('product')->implode('-'));
    }
    // where not in
    public function testWhereNotINCollection()
    {
        $shop = [
            ['id' => 100, 'product' => 'Play Station', 'price' => 450, 'quantite' => 35],
            ['id' => 101, 'product' => 'Sony TV', 'price' => 865, 'quantite' => 9],
            ['id' => 102, 'product' => 'Samsung TV', 'price' => 500, 'quantite' => 35],
            ['id' => 103, 'product' => 'TCL TV', 'price' => 600, 'quantite' => 12],
        ];
        $test = new Collection($shop);
        $this->assertEquals([
            0 => ['id' => 100, 'product' => 'Play Station', 'price' => 450, 'quantite' => 35],
            2 => ['id' => 102, 'product' => 'Samsung TV', 'price' => 500, 'quantite' => 35]
        ], $test->whereNotIn('price', [865, 600])->all());
    }
    // where between
    public function testWhereBetweenCollection()
    {
        $shop = [
            ['id' => 100, 'product' => 'Play Station', 'price' => 450, 'quantite' => 35],
            ['id' => 101, 'product' => 'Sony TV', 'price' => 865, 'quantite' => 9],
            ['id' => 102, 'product' => 'Samsung TV', 'price' => 500, 'quantite' => 35],
            ['id' => 103, 'product' => 'TCL TV', 'price' => 600, 'quantite' => 12],
        ];
        $test = new Collection($shop);
        $this->assertEquals([
            2 => ['id' => 102, 'product' => 'Samsung TV', 'price' => 500, 'quantite' => 35],
            3 => ['id' => 103, 'product' => 'TCL TV', 'price' => 600, 'quantite' => 12]
        ], $test->whereBetween('price', [500, 800])->all());
    }

    // where between
    public function testWhereNotBetweenCollection()
    {
        $shop = [
            ['id' => 100, 'product' => 'Play Station', 'price' => 450, 'quantite' => 35],
            ['id' => 101, 'product' => 'Sony TV', 'price' => 865, 'quantite' => 9],
            ['id' => 102, 'product' => 'Samsung TV', 'price' => 500, 'quantite' => 35],
            ['id' => 103, 'product' => 'TCL TV', 'price' => 600, 'quantite' => 12],
        ];
        $test = new Collection($shop);
        $this->assertEquals([
            0 => ['id' => 100, 'product' => 'Play Station', 'price' => 450, 'quantite' => 35],
            1 => ['id' => 101, 'product' => 'Sony TV', 'price' => 865, 'quantite' => 9]
        ], $test->whereNotBetween('price', [500, 800])->all());
    }
    // where between
    public function testOrderByCollection()
    {
        $shop = [
            ['id' => 100, 'product' => 'Play Station', 'price' => 450, 'quantite' => 43],
            ['id' => 101, 'product' => 'Sony TV', 'price' => 865, 'quantite' => 9],
            ['id' => 102, 'product' => 'Samsung TV', 'price' => 500, 'quantite' => 35],
            ['id' => 103, 'product' => 'TCL TV', 'price' => 600, 'quantite' => 12],
        ];
        $test = new Collection($shop);
        $this->assertEquals([
            0 => ['id' => 100, 'product' => 'Play Station', 'price' => 450, 'quantite' => 43],
            1 => ['id' => 102, 'product' => 'Samsung TV', 'price' => 500, 'quantite' => 35],
            2 => ['id' => 103, 'product' => 'TCL TV', 'price' => 600, 'quantite' => 12],
            3 => ['id' => 101, 'product' => 'Sony TV', 'price' => 865, 'quantite' => 9]
        ], $test->orderBy('price')->all());
        $this->assertEquals([
            0 => ['id' => 101, 'product' => 'Sony TV', 'price' => 865, 'quantite' => 9],
            1 => ['id' => 103, 'product' => 'TCL TV', 'price' => 600, 'quantite' => 12],
            2 => ['id' => 102, 'product' => 'Samsung TV', 'price' => 500, 'quantite' => 35],
            3 => ['id' => 100, 'product' => 'Play Station', 'price' => 450, 'quantite' => 43]
        ], $test->orderBy('price', 'desc')->all());
    }
    // groupBy
    public function testWheregroupByCollection()
    {
        $shop = [
            ['id' => 100, 'product' => 'Play Station', 'price' => 450, 'quantite' => 35],
            ['id' => 101, 'product' => 'Sony TV', 'price' => 865, 'quantite' => 9],
            ['id' => 102, 'product' => 'Samsung TV', 'price' => 500, 'quantite' => 35],
            ['id' => 103, 'product' => 'TCL TV', 'price' => 600, 'quantite' => 12],
        ];
        $test = new Collection($shop);
        $result = $test->groupBy('quantite');
        
        $this->assertInstanceOf(Collection::class, $result[35]);
        $this->assertInstanceOf(Collection::class, $result[9]);
        $this->assertInstanceOf(Collection::class, $result[12]);
        
        $this->assertCount(2, $result[35]);
        $this->assertCount(1, $result[9]);
        $this->assertCount(1, $result[12]);
    }
    public function testSlice()
    {
        $test = new Collection(['1','2','3','4','5','6']);
        $this->assertEquals(['2' => '3', '3' => '4', '4' => '5', '5' => '6'], $test->slice(2)->all());
        $this->assertEquals(['2' => '3', '3' => '4'], $test->slice(2,2)->all());
    }

    /**
     * Test whereNull method
     */
    public function testWhereNullCollection()
    {
        $test = new Collection([
            ['id' => 1, 'name' => 'Test 1', 'value' => null],
            ['id' => 2, 'name' => 'Test 2', 'value' => 'not null'],
            ['id' => 3, 'name' => 'Test 3', 'value' => null]
        ]);
        
        $result = array_values($test->whereNull('value')->all());
        $this->assertCount(2, $result);
        $this->assertEquals('Test 1', $result[0]['name']);
        $this->assertEquals('Test 3', $result[1]['name']);
    }

    /**
     * Test whereNotNull method
     */
    public function testWhereNotNullCollection()
    {
        $test = new Collection([
            ['id' => 1, 'name' => 'Test 1', 'value' => null],
            ['id' => 2, 'name' => 'Test 2', 'value' => 'not null'],
            ['id' => 3, 'name' => 'Test 3', 'value' => null]
        ]);
        
        $result = array_values($test->whereNotNull('value')->all());
        $this->assertCount(1, $result);
        $this->assertEquals('Test 2', $result[0]['name']);
    }

    /**
     * Test whereLike method
     */
    public function testWhereLikeCollection()
    {
        $test = new Collection([
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Doe'],
            ['id' => 3, 'name' => 'Jim Smith']
        ]);
        
        $result = $test->whereLike('name', '%Doe%')->all();
        $this->assertCount(2, $result);
        $this->assertEquals('John Doe', $result[0]['name']);
        $this->assertEquals('Jane Doe', $result[1]['name']);
    }

    /**
     * Test join method
     */
    public function testJoinCollection()
    {
        $users = new Collection([
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jane']
        ]);
        
        $orders = new Collection([
            ['user_id' => 1, 'product' => 'Book'],
            ['user_id' => 1, 'product' => 'Pen'],
            ['user_id' => 2, 'product' => 'Notebook']
        ]);
        
        $result = $users->join($orders, 'id', 'user_id', 'inner')->all();
        $this->assertCount(3, $result);
        $this->assertEquals('John', $result[0]['name']);
        $this->assertEquals('Book', $result[0]['product']);
    }

    /**
     * Test countBy method
     */
    public function testCountByCollection()
    {
        $test = new Collection([
            ['category' => 'A', 'value' => 1],
            ['category' => 'A', 'value' => 2],
            ['category' => 'B', 'value' => 3],
            ['category' => 'B', 'value' => 4],
            ['category' => 'B', 'value' => 5]
        ]);
        
        $result = $test->countBy('category')->all();
        $this->assertEquals(2, $result['A']);
        $this->assertEquals(3, $result['B']);
    }

    /**
     * Test groupByRange method
     */
    public function testGroupByRangeCollection()
    {
        $test = new Collection([
            ['price' => 10],
            ['price' => 15],
            ['price' => 30],
            ['price' => 35],
            ['price' => 50]
        ]);
        
        $result = $test->groupByRange('price', 20);
        
        // Vérifier que chaque groupe est une Collection
        $this->assertInstanceOf(Collection::class, $result['0-20']);
        $this->assertInstanceOf(Collection::class, $result['20-40']);
        $this->assertInstanceOf(Collection::class, $result['40-60']);
        
        // Vérifier le nombre d'éléments dans chaque groupe
        $this->assertCount(2, $result['0-20']->all(), "Le groupe 0-20 devrait contenir 2 éléments");
        $this->assertCount(2, $result['20-40']->all(), "Le groupe 20-40 devrait contenir 2 éléments");
        $this->assertCount(1, $result['40-60']->all(), "Le groupe 40-60 devrait contenir 1 élément");
        
        // Vérifier le contenu des groupes
        $this->assertEquals([10, 15], $result['0-20']->pluck('price')->all());
        $this->assertEquals([30, 35], $result['20-40']->pluck('price')->all());
        $this->assertEquals([50], $result['40-60']->pluck('price')->all());
    }

    /**
     * Test groupByMultiple method
     */
    public function testGroupByMultipleCollection()
    {
        $test = new Collection([
            ['category' => 'A', 'status' => 1, 'value' => 10],
            ['category' => 'A', 'status' => 2, 'value' => 20],
            ['category' => 'B', 'status' => 1, 'value' => 30],
            ['category' => 'B', 'status' => 2, 'value' => 40]
        ]);
        
        $result = $test->groupByMultiple('category', 'status')->all();
        $this->assertArrayHasKey('A', $result);
        $this->assertArrayHasKey('B', $result);
        $this->assertArrayHasKey(1, $result['A']);
        $this->assertArrayHasKey(2, $result['A']);
        $this->assertEquals(10, $result['A'][1][0]['value']);
        $this->assertEquals(40, $result['B'][2][0]['value']);
    }

    /**
     * Test stats method
     */
    public function testStatsCollection()
    {
        $test = new Collection([
            ['value' => 10],
            ['value' => 20],
            ['value' => 30],
            ['value' => 40],
            ['value' => 50]
        ]);
        
        $stats = $test->stats('value');
        $this->assertEquals(10, $stats['min']);
        $this->assertEquals(50, $stats['max']);
        $this->assertEquals(30, $stats['avg']);
        $this->assertEquals(150, $stats['sum']);
        $this->assertEquals(5, $stats['count']);
    }

    public function testAvgKeyColumnCollection()
    {
        $array = [
            ['name' => 'franck', 'age' => 53, 'notes' => [10, 14, 12]],
            ['name' => 'maurice', 'age' => 63, 'notes' => [15, 15, 15]],
            ['name' => 'henri', 'age' => 45, 'notes' => [5, 10, 15]]
        ];

        $collection = new Collection($array);
        $moyenne = [12, 15, 10];
        $i = 0;
        foreach ($collection as $collect) {
            $this->assertSame($moyenne[$i++], $collection->avgKey($collect['notes']));
        }
    }

    /**
     * Test direct de la méthode avgKey avec différents types de tableaux
     */
    public function testAvgKey()
    {
        $collection = new Collection([]);
        
        // Test avec un tableau simple de nombres
        $this->assertEquals(5, $collection->avgKey([5, 5, 5]));
        
        // Test avec des nombres décimaux
        $this->assertEquals(2.5, $collection->avgKey([1.5, 2.5, 3.5]));
        
        // Test avec des nombres négatifs et positifs
        $this->assertEquals(0, $collection->avgKey([-5, 0, 5]));
        
        // Test avec un seul élément
        $this->assertEquals(10, $collection->avgKey([10]));
        
        // Test avec des grands nombres
        $this->assertEquals(1000, $collection->avgKey([1000, 1000, 1000]));
    }

    /**
     * Test des cas d'erreur pour avgKey
     */
    public function testAvgKeyErrors()
    {
        $collection = new Collection([]);
        
        $this->expectException(\DivisionByZeroError::class);
        $collection->avgKey([]);
    }
}
