<?php declare(strict_types=1);

namespace Pehapkari\Website\Tests\Posts\Year2017\Iterators;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use SplFixedArray;

final class IteratorsTest extends TestCase
{
    public function test01SimpleArray(): void
    {
        // Arrange
        $object = [];
        $object[0] = 'first-value';
        $object[1] = 'second-value';

        $accumulator = [];

        // Act
        foreach ($object as $key1 => $val1) {
            foreach ($object as $key2 => $val2) {
                $accumulator[] = [$val1, $val2];
            }
        }

        // Assert
        $this->assertCount(2 * 2, $accumulator); // cartesian product
        $this->assertSame([
            ['first-value', 'first-value'],
            ['first-value', 'second-value'],
            ['second-value', 'first-value'],
            ['second-value', 'second-value'],
        ], $accumulator);
    }

    public function test02SplFixedArrayWtf(): void
    {
        // Arrange
        $object = new SplFixedArray(2);
        $object[0] = 'first-value';
        $object[1] = 'second-value';

        $accumulator = [];

        // Act
        foreach ($object as $key1 => $val1) {
            foreach ($object as $key2 => $val2) {
                $accumulator[] = [$val1, $val2];
            }
        }

        // Assert
        $this->assertCount(2, $accumulator); // cartesian product
        $this->assertSame([
            ['first-value', 'first-value'],
            ['first-value', 'second-value'],
        ], $accumulator);
    }

    public function test03SplFixedArrayWtf(): void
    {
        // Arrange
        $object = new class(2) extends SplFixedArray {
            /**
             * @return mixed[]
             */
            public function __debugInfo(): array
            {
                $return = [];
                /* @noinspection ForeachSourceInspection */
                foreach ($this as $key => $val) {
                    $return[(string) $key] = (string) $val;
                }

                return $return;
            }
        };
        $object[0] = 'first-value';
        $object[1] = 'second-value';

        $accumulator = [];

        // Act
        foreach ($object as $key1 => $val1) {
            $accumulator[] = [$val1];
        }

        // Assert
        $this->assertCount(2, $accumulator); // cartesian product
        $this->assertSame([
            ['first-value'],
            ['second-value'],
        ], $accumulator);
    }

    public function test04SplFixedArrayWtf(): void
    {
        // Arrange
        $object = new class(2) extends SplFixedArray {
            /**
             * @return mixed[]
             */
            public function __debugInfo(): array
            {
                $return = [];
                /* @noinspection ForeachSourceInspection */
                foreach ($this as $key => $val) {
                    $return[(string) $key] = (string) $val;
                }

                return $return;
            }
        };
        $object[0] = 'first-value';
        $object[1] = 'second-value';

        $accumulator = [];

        // Act
        foreach ($object as $key1 => $val1) {
            $object->__debugInfo(); // simulate what happens when you stop on breakpoint on this line
            // same as `var_dump($object)`
            $accumulator[] = [$val1];
        }

        // Assert
        $this->assertCount(1, $accumulator); // cartesian product
        $this->assertSame([
            ['first-value'],
        ], $accumulator);
    }

    public function test05ForeachWrittenAsWhile(): void
    {
        // Arrange
        $object = new SplFixedArray(2);
        $object[0] = 'first-value';
        $object[1] = 'second-value';

        $accumulator = [];

        // Act
        reset($object); // PHPStorm's static analysis is crying here; it is intentional
        while (list($key1, $val1) = each($object)) {
            reset($object);
            while (list($key2, $val2) = each($object)) {
                $accumulator[] = [$val1, $val2];
            }
        }

        // Assert
        $this->assertCount(2, $accumulator); // cartesian product
        $this->assertSame([
            ['first-value', 'first-value'],
            ['first-value', 'second-value'],
        ], $accumulator);
    }

    public function test06QuickFixUsingClone(): void
    {
        // Arrange
        $object = new SplFixedArray(2);
        $object[0] = 'first-value';
        $object[1] = 'second-value';

        $accumulator = [];

        // Act
        foreach (clone $object as $key1 => $val1) {
            foreach (clone $object as $key2 => $val2) {
                $accumulator[] = [$val1, $val2];
            }
        }

        // Assert
        $this->assertCount(2 * 2, $accumulator); // cartesian product
        $this->assertSame([
            ['first-value', 'first-value'],
            ['first-value', 'second-value'],
            ['second-value', 'first-value'],
            ['second-value', 'second-value'],
        ], $accumulator);
    }

    public function test07ArrayObject(): void
    {
        // Arrange
        $object = new ArrayObject;
        $object[0] = 'first-value';
        $object[1] = 'second-value';

        $accumulator = [];

        // Act
        foreach ($object as $key1 => $val1) {
            foreach ($object as $key2 => $val2) {
                $accumulator[] = [$val1, $val2];
            }
        }

        // Assert
        $this->assertCount(2 * 2, $accumulator); // cartesian product
        $this->assertSame([
            ['first-value', 'first-value'],
            ['first-value', 'second-value'],
            ['second-value', 'first-value'],
            ['second-value', 'second-value'],
        ], $accumulator);
    }

    public function test08NewIteratorIsReturnedEveryTime(): void
    {
        // Arrange
        $object = new ArrayObject;

        // Act
        $iterator1 = $object->getIterator();
        $iterator2 = $object->getIterator();

        // Assert
        $this->assertNotSame($iterator1, $iterator2);
    }

    public function test09BonusInfiniteLoop(): void
    {
        // Arrange
        $object = new SplFixedArray(2);
        $object[0] = 'first-value';
        $object[1] = 'second-value';

        $i = 0;

        // Act
        foreach ($object as $key1 => $val1) {
            foreach ($object as $key2 => $val2) {
                if ($i >= 1000) {
                    continue;
                } // prevent looping to infinity
                ++$i;

                // this is how you make this loop infinite:
                // Task: rewrite this loops as a while loops (see above) and get the idea what is happening
                break;
            }
        }

        // Assert
        $this->assertSame(1000, $i);
    }
}