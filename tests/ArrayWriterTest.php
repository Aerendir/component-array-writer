<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Array Writer Component.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Component\ArrayWriter\Tests;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Component\ArrayWriter\ArrayWriter;
use Symfony\Component\PropertyAccess\Exception\AccessException;

/**
 * Class ArrayWriterTest.
 */
final class ArrayWriterTest extends TestCase
{
    private ArrayWriter $resource;

    protected function setUp(): void
    {
        $this->resource = new ArrayWriter();
    }

    public function testGetValue(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];

        self::assertSame($testArray['level1'], $this->resource->getValue($testArray, '[level1]'));
    }

    public function testGetValueReturnsEntireArrayIfPathIsEmpty(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];
        self::assertSame($testArray, $this->resource->getValue($testArray, ''));
    }

    public function testGetValueAndForget(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
            'level2' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];

        $expectedTestArray = [
            'level2' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];

        $expectedReturnedArray = ['value 1', 'value 2', 'value 3'];

        $returnedArray = $this->resource->getValueAndForget($testArray, '[level1]');

        self::assertSame($expectedReturnedArray, $returnedArray);
        self::assertSame($expectedTestArray, $testArray);
    }

    public function testIsNode(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];
        self::assertTrue($this->resource->isNode($testArray, '[level1]'));
    }

    public function testIsReadable(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];
        self::assertTrue($this->resource->isReadable($testArray, '[level1]'));
        self::assertFalse($this->resource->isReadable($testArray, '[non-existent]'));
    }

    public function testAdd(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];

        $expected = [
            'level1' => [
                'value 1', 'value 2', 'value 3', 'ciao',
            ],
        ];

        $this->resource->add($testArray, '[level1]', 'ciao');
        self::assertSame($expected, $testArray);
    }

    public function testAddCanCreateANewPropertyInTheRoot(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];

        $expected = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
            'level2' => 'ciao',
        ];

        $this->resource->add($testArray, '', 'ciao', 'level2');
        self::assertSame($expected, $testArray);
    }

    public function testAddPreservesAlreadyExistentValueInToPath(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];

        $expected = [
            'level1' => [
                0 => [
                    'value 1', 'ciao',
                ],
                'value 2',
                'value 3',
            ],
        ];

        $this->resource->add($testArray, '[level1][0]', 'ciao');
        self::assertSame($expected, $testArray);
    }

    public function testAddCanSetPropertyNameForOldAndNewValues(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];

        $expected = [
            'level1' => [
                0 => [
                    'OldValue' => 'value 1',
                    'NewValue' => 'ciao',
                ],
                'value 2',
                'value 3',
            ],
        ];

        $this->resource->add($testArray, '[level1][0]', 'ciao', 'NewValue', 'OldValue');
        self::assertSame($expected, $testArray);
    }

    public function testCp(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];

        $result = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
            'level2' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];
        $this->resource->cp($testArray, '[level1]', '[level2]');
        self::assertSame($result, $testArray);
    }

    public function testCpToRoot(): void
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3',
            ],
            'level2' => [
                'key1' => 'value 2.1', 'value 2.2', 'value 2.3',
            ],
        ];
        $result = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3',
            ],
            'level2' => [
                'key1' => 'value 2.1', 'value 2.2', 'value 2.3',
            ],
            'key1'   => 'value 2.1',
            'value 2.2',
            'value 2.3',
        ];
        $this->resource->cp($test, '[level2]', '[]');
        self::assertSame($result, $test);
    }

    public function testCpThrowsExceptionIfFromIsNotReadable(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];
        $this->expectException(AccessException::class);
        $this->resource->cp($testArray, '[not-known]', '[not-known2]');
    }

    public function testCpSafe(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];
        $result = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
            'level2' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];
        $this->resource->cpSafe($testArray, '[level1]', '[level3]');
        self::assertSame($result['level1'], $testArray['level3']);
    }

    /**
     * This exception is not testable as the isWrite method ever returns true.
     */
    public function testCpSafeThrowsExceptionIfToIsNotWritable(): void
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3',
            ],
            'level2' => [
                'value 2.1', 'value 2.2', 'value 2.3',
            ],
        ];
        $this->expectException(AccessException::class);
        $this->resource->cpSafe($test, '[level1]', '[level2]');
    }

    public function testEdit(): void
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3',
            ],
        ];
        $result = [
            'value 2.1', 'value 2.2', 'value 2.3',
        ];

        $this->resource->edit($test, '[level1]', $result);
        self::assertSame($test['level1'], $result);
    }

    public function testEditRecognizeRoot(): void
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3',
            ],
        ];
        $result = [
            'value 2.1', 'value 2.2', 'value 2.3',
        ];

        $this->resource->edit($test, '[]', $result);
        self::assertSame($test, $result);
    }

    public function testEditThrowsExceptionIfPathDoesntExist(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];
        $this->expectException(AccessException::class);
        $this->resource->edit($testArray, '[non-existent]', ['value 1', 'value 2']);
    }

    public function testMerge(): void
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3',
            ],
            'level2' => [
                'key1' => 'value 2.1', 'value 2.2', 'value 2.3',
            ],
        ];
        $result = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3',
            ],
            'key1'   => 'value 2.1',
            'value 2.2',
            'value 2.3',
        ];
        $this->resource->merge($test, '[level2]', '[]');
        self::assertSame($test, $result);
    }

    public function testMergeCanHandleStrings(): void
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3',
            ],
            'level2' => 'value 2.1',
        ];
        $result = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3',
            ],
            'value 2.1',
        ];
        $this->resource->merge($test, '[level2]', '[]');
        self::assertSame($test, $result);
    }

    public function testMv(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];
        $result = [
            'level2' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];
        $this->resource->mv($testArray, '[level1]', '[level2]');
        self::assertSame($result, $testArray);
    }

    public function testMvToRoot(): void
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3',
            ],
            'level2' => [
                'key1' => 'value 2.1', 'value 2.2', 'value 2.3',
            ],
        ];
        $result = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3',
            ],
            'key1'   => 'value 2.1',
            'value 2.2',
            'value 2.3',
        ];
        $this->resource->mv($test, '[level2]', '[]');
        self::assertSame($result, $test);
    }

    public function testMvSafe(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];
        $result = [
            'level2' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];
        $this->resource->mvSafe($testArray, '[level1]', '[level2]');
        self::assertSame($result, $testArray);
    }

    public function testMvSafeThrowsExceptionIfToIsNotWritable(): void
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3',
            ],
            'level2' => [
                'value 2.1', 'value 2.2', 'value 2.3',
            ],
        ];
        $this->expectException(AccessException::class);
        $this->resource->mvSafe($test, '[level1]', '[level2]');
    }

    public function testMvUp(): void
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3',
            ],
            'level2' => [
                'key1' => 'value 2.1', 'value 2.2', 'value 2.3',
            ],
        ];
        $result = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3',
            ],
            'key1'   => 'value 2.1',
            'value 2.2',
            'value 2.3',
        ];
        $this->resource->mvUp($test, '[level2]');
        self::assertSame($test, $result);
    }

    public function testMvUpCanHandleStrings(): void
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3',
            ],
            'level2' => 'value 2.1',
        ];
        $result = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3',
            ],
            'value 2.1',
        ];
        $this->resource->mvUp($test, '[level2]');
        self::assertSame($test, $result);
    }

    public function testRm(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];
        $result = ['level1' => ['value 1', 'value 2']];
        $this->resource->rm($testArray, '[level1][2]');
        self::assertSame($result, $testArray);
    }

    public function testWrap(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];

        $result = [
            'level1' => [
                'wrapper' => [
                    'value 1', 'value 2', 'value 3',
                ],
            ],
        ];

        $this->resource->wrap($testArray, '[level1]', 'wrapper');
        self::assertSame($result, $testArray);
    }

    public function testWrapRoot(): void
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3',
            ],
        ];

        $result = [
            'wrapper' => [
                'level1' => [
                    'value 1', 'value 2', 'value 3',
                ],
            ],
        ];

        $this->resource->wrap($testArray, '', 'wrapper');
        self::assertSame($result, $testArray);
    }

    public function testPathize(): void
    {
        $test     = 'path';
        $expected = '[path]';

        $result = ArrayWriter::pathize($test);

        self::assertSame($expected, $result);
    }
}
