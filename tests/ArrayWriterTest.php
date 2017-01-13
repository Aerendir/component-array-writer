<?php

/**
 *    @author      Aerendir <hello@aerendir.me>
 *    @copyright   Copyright (C) 2014 SerendipityHQ. All rights reserved.
 */
namespace SHQ\Component\ArrayWriter\Tests;

use SHQ\Component\ArrayWriter\ArrayWriter;
use Symfony\Component\PropertyAccess\Exception\AccessException;

/**
 * Class ArrayWriterTest.
 */
class ArrayWriterTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ArrayWriter $resource */
    private $resource;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->resource = new ArrayWriter();
    }

    public function testGetValue()
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];

        self::assertSame($testArray['level1'], $this->resource->getValue($testArray, '[level1]'));
    }

    public function testGetValueReturnsEntireArrayIfPathIsEmpty()
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];
        self::assertSame($testArray, $this->resource->getValue($testArray, ''));
    }

    public function testIsNode()
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];
        self::assertSame(true, $this->resource->isNode($testArray, '[level1]'));
    }

    public function testIsReadable()
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];
        self::assertTrue($this->resource->isReadable($testArray, '[level1]'));
        self::assertFalse($this->resource->isReadable($testArray, '[non-existent]'));
    }

    public function testInArrayNested()
    {
        $testArray = [
            'level_1' => 'a simple value',
            'level_2' => [
                'a nested value'
            ],
            'level_3' => [
                'nested_1' => [
                    'a really nested value'
                ]
            ]
        ];

        self::assertFalse($this->resource->inArrayNested($testArray, 'non existent value'));
        self::assertTrue($this->resource->inArrayNested($testArray, 'a simple value'));
        self::assertTrue($this->resource->inArrayNested($testArray, 'a nested value'));
        self::assertTrue($this->resource->inArrayNested($testArray, 'a really nested value'));
    }

    public function testAdd()
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];

        $expected = [
            'level1' => [
                'value 1', 'value 2', 'value 3', 'ciao'
            ]
        ];

        $this->resource->add($testArray, '[level1]', 'ciao');
        self::assertSame($expected, $testArray);
    }

    public function testAddCanCreateANewPropertyInTheRoot()
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];

        $expected = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ],
            'level2' => 'ciao'
        ];

        $this->resource->add($testArray, '', 'ciao', 'level2');
        self::assertSame($expected, $testArray);
    }

    public function testAddPreservesAlreadyExistentValueInToPath()
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];

        $expected = [
            'level1' => [
                0 => [
                    'value 1', 'ciao'
                ],
                'value 2',
                'value 3'
            ]
        ];

        $this->resource->add($testArray, '[level1][0]', 'ciao');
        self::assertSame($expected, $testArray);
    }

    public function testAddCanSetPropertyNameForOldAndNewValues()
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];

        $expected = [
            'level1' => [
                0 => [
                    'OldValue' => 'value 1',
                    'NewValue' => 'ciao'
                ],
                'value 2',
                'value 3'
            ]
        ];

        $this->resource->add($testArray, '[level1][0]', 'ciao', 'NewValue', 'OldValue');
        self::assertSame($expected, $testArray);
    }

    public function testCp()
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];

        $result = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ],
            'level2' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];
        $this->resource->cp($testArray, '[level1]', '[level2]');
        self::assertSame($result, $testArray);
    }

    public function testCpToRoot()
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3'
            ],
            'level2' => [
                'key1' => 'value 2.1', 'value 2.2', 'value 2.3'
            ]
        ];
        $result = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3'
            ],
            'level2' => [
                'key1' => 'value 2.1', 'value 2.2', 'value 2.3'
            ],
            'key1' => 'value 2.1',
            'value 2.2',
            'value 2.3'
        ];
        $this->resource->cp($test, '[level2]', '[]');
        self::assertSame($result, $test);
    }

    public function testCpThrowsExceptionIfFromIsNotReadable()
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];
        $this->expectException(AccessException::class);
        $this->resource->cp($testArray, '[not-known]', '[not-known2]');
    }

    public function testCpSafe()
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];
        $result = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ],
            'level2' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];
        $this->resource->cpSafe($testArray, '[level1]', '[level3]');
        self::assertSame($result['level1'], $testArray['level3']);
    }

    /**
     * This exception is not testable as the isWrite method ever returns true.
     */
    public function testCpSafeThrowsExceptionIfToIsNotWritable()
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3'
            ],
            'level2' => [
                'value 2.1', 'value 2.2', 'value 2.3'
            ]
        ];
        $this->expectException(AccessException::class);
        $this->resource->cpSafe($test, '[level1]', '[level2]');
    }

    public function testEdit()
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3'
            ]
        ];
        $result = [
            'value 2.1', 'value 2.2', 'value 2.3'
        ];

        $this->resource->edit($test, '[level1]', $result);
        self::assertSame($test['level1'], $result);
    }

    public function testEditRecognizeRoot()
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3'
            ]
        ];
        $result = [
            'value 2.1', 'value 2.2', 'value 2.3'
        ];

        $this->resource->edit($test, '[]', $result);
        self::assertSame($test, $result);
    }

    public function testEditThrowsExceptionIfPathDoesntExist()
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];
        $this->expectException(AccessException::class);
        $this->resource->edit($testArray, '[non-existent]', ['value 1', 'value 2']);
    }

    public function testMerge()
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3'
            ],
            'level2' => [
                'key1' => 'value 2.1', 'value 2.2', 'value 2.3'
            ]
        ];
        $result = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3'
            ],
            'key1' => 'value 2.1',
            'value 2.2',
            'value 2.3'
        ];
        $this->resource->merge($test, '[level2]', '[]');
        self::assertSame($test, $result);
    }

    public function testMergeCanHandleStrings()
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3'
            ],
            'level2' => 'value 2.1'
        ];
        $result = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3'
            ],
            'value 2.1'
        ];
        $this->resource->merge($test, '[level2]', '[]');
        self::assertSame($test, $result);
    }

    public function testMv()
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];
        $result = [
            'level2' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];
        $this->resource->mv($testArray, '[level1]', '[level2]');
        self::assertSame($result, $testArray);
    }

    public function testMvToRoot()
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3'
            ],
            'level2' => [
                'key1' => 'value 2.1', 'value 2.2', 'value 2.3'
            ]
        ];
        $result = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3'
            ],
            'key1' => 'value 2.1',
            'value 2.2',
            'value 2.3'
        ];
        $this->resource->mv($test, '[level2]', '[]');
        self::assertSame($result, $test);
    }

    public function testMvSafe()
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];
        $result = [
            'level2' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];
        $this->resource->mvSafe($testArray, '[level1]', '[level2]');
        self::assertSame($result, $testArray);
    }

    public function testMvSafeThrowsExceptionIfToIsNotWritable()
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3'
            ],
            'level2' => [
                'value 2.1', 'value 2.2', 'value 2.3'
            ]
        ];
        $this->expectException(AccessException::class);
        $this->resource->mvSafe($test, '[level1]', '[level2]');
    }

    public function testMvUp()
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3'
            ],
            'level2' => [
                'key1' => 'value 2.1', 'value 2.2', 'value 2.3'
            ]
        ];
        $result = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3'
            ],
            'key1' => 'value 2.1',
            'value 2.2',
            'value 2.3'
        ];
        $this->resource->mvUp($test, '[level2]');
        self::assertSame($test, $result);
    }

    public function testMvUpCanHandleStrings()
    {
        $test = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3'
            ],
            'level2' => 'value 2.1'
        ];
        $result = [
            'level1' => [
                'value 1.1', 'value 1.2', 'value 1.3'
            ],
            'value 2.1'
        ];
        $this->resource->mvUp($test, '[level2]');
        self::assertSame($test, $result);
    }

    public function testRm()
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];
        $result = ['level1' => ['value 1', 'value 2']];
        $this->resource->rm($testArray, '[level1][2]');
        self::assertSame($result, $testArray);
    }

    public function testWrap()
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];

        $result = [
            'level1' => [
                'wrapper' => [
                    'value 1', 'value 2', 'value 3'
                ]
            ]
        ];

        $this->resource->wrap($testArray, '[level1]', 'wrapper');
        self::assertSame($result, $testArray);
    }

    public function testWrapRoot()
    {
        $testArray = [
            'level1' => [
                'value 1', 'value 2', 'value 3'
            ]
        ];

        $result = [
            'wrapper' => [
                'level1' => [
                    'value 1', 'value 2', 'value 3'
                ]
            ]
        ];

        $this->resource->wrap($testArray, '', 'wrapper');
        self::assertSame($result, $testArray);
    }

    public function testPathize()
    {
        $test = 'path';
        $expected = '[path]';

        $result = ArrayWriter::pathize($test);

        self::assertSame($expected, $result);
    }
}
