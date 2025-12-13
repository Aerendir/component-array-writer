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

namespace SerendipityHQ\Component\ArrayWriter;

use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\PropertyAccess\PropertyPathBuilder;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * Manages some writing operations on the passed array.
 *
 * It uses a *UNIX like syntax:
 * - cp: copy a value to another path (and left intact the value in the original location)
 * - mv: moves a value to another path
 * - rm: removes a value from the given path
 *
 * @see Tests\ArrayWriterTest
 */
final class ArrayWriter
{
    /** The PropertyAccessor used to manipulate the array */
    private PropertyAccessorInterface $pa;

    /**
     * Builds an instance of PropertyAccessor.
     */
    public function __construct()
    {
        $accessorBuilder = PropertyAccess::createPropertyAccessorBuilder();
        $accessorBuilder->enableExceptionOnInvalidIndex();

        $this->pa = $accessorBuilder->getPropertyAccessor();
    }

    /**
     * Get the value of the given path from the array graph.
     */
    public function getValue(array $array, string $path)
    {
        // If the $path value is empty, return the entire array graph
        if ($this->isRoot($path)) {
            return $array;
        }

        // If $path doesn't exist returns null. It is not possible to distinghuish between a path that exists and has a
        // null value and a path that doesn't exist at all.
        return $this->pa->getValue($array, $path);
    }

    /**
     * Get the value of the given path from the array graph and removes it from the array.
     */
    public function getValueAndForget(array &$array, string $path)
    {
        // If the $path value is empty, return the entire array graph
        if ($this->isRoot($path)) {
            return $array;
        }

        // If $path doesn't exist returns null. It is not possible to distinghuish between a path that exists and has a
        // null value and a path that doesn't exist at all.
        $value = $this->pa->getValue($array, $path);

        // Remove the array from the source array
        $this->rm($array, $path);

        return $value;
    }

    /**
     * This just searches in the first level, not in deeper ones.
     *
     * @param array<int|string, mixed> $array
     */
    public function getValueByPartialKey(array $array, string $searchingKey)
    {
        foreach ($array as $key => $value) {
            if (false !== \stripos((string) $key, $searchingKey)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Checks if a given path is a node or not.
     */
    public function isNode(array $array, string $path): bool
    {
        return \is_array($this->getValue($array, $path));
    }

    /**
     * Checks if a path exists in the given array.
     *
     * @param array<int|string, mixed> $array
     */
    public function isReadable(array $array, string $path): bool
    {
        try {
            return $this->pa->isReadable($array, $path);
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function isRoot(string $path): bool
    {
        return '[]' === $path || '' === $path || '.' === $path;
    }

    /**
     * Returns true if the $path is null, false instead.
     */
    public function isWritable(array $array, string $path): bool
    {
        if ($this->isRoot($path)) {
            return false;
        }

        try {
            $this->pa->getValue($array, $path);
        } catch (NoSuchIndexException $noSuchIndexException) {
            // If the exception is thrown, the value can be written at given $path as it hasn't already a value
            return true;
        }

        return false;
    }

    /**
     * @param array<int|string, mixed> $array  The array in which the the key is searched for
     * @param string                   $needle The key to search for
     */
    public function keyExistsNested(array $array, string $needle): bool
    {
        // If the key exists in the first level...
        if (\array_key_exists($needle, $array)) {
            // Return true
            return true;
        }

        /**
         * Search in the deeper levels of the array.
         */
        foreach ($array as $value) {
            // If this value is an array...
            if (\is_array($value)) {
                // ... First search for the key and if found...
                if (\array_key_exists($needle, $value)) {
                    // ... Return true
                    return true;
                }

                // The $needle is not found: continue the search again
                $this->keyExistsNested($value, $needle);
            }
        }

        // Nothing: the $needle were not found: return false
        return false;
    }

    /**
     * Adds a value to a node.
     *
     * The method can recognize if the current value at $toPath is a string: if it is, the method first transforms the
     * current value into an array and then adds the new value to this new array, so preserving the already present
     * value.
     *
     * Passing $propertyForNewValue and $propertyForOldValue it is possible to set the property names of the already
     * present value and of the newly created value.
     *
     * If the key doesn't exist, the method simply adds it.
     *
     * @param string $toPath              The location where to add the value taken $fromPath
     * @param mixed  $value               The value to add
     * @param string $propertyForNewValue The name to give to the new property
     * @param string $propertyForOldValue The old value is now assigned to a property: this is its property name
     */
    public function add(array &$array, string $toPath, $value, string $propertyForNewValue = '', string $propertyForOldValue = ''): void
    {
        /** @var mixed $currentValue Get the value at destination path (to preserve it if isn't an array) */
        $currentValue = $this->getValue($array, $toPath);

        // If the current value isn't yet an array...
        if ( ! \is_array($currentValue)) {
            // ... Transform the current value into an array
            $currentValue = '' === $propertyForOldValue
                // Use the autogenerated keys
                ? [$currentValue]
                // Use the passed key name
                : [$propertyForOldValue => $currentValue];
        }

        // Add the new value to the array
        if ('' === $propertyForNewValue) {
            // If no property name is set for the new value, simply add it
            $currentValue[] = $value;
        } else {
            // Remove "[" and "]"
            $propertyForNewValue = \rtrim(\ltrim($propertyForNewValue, '['), ']');

            // ! ! ! THIS MAY OVERWRITE A YET EXISTENT VALUE ! ! !
            $currentValue[$propertyForNewValue] = $value;
        }

        $this->edit($array, $toPath, $currentValue);
    }

    /**
     * Copy a value from $from path to $to path.
     *
     * If the $to path has already a value, it will be overwritten.
     *
     * @param array<int|string, mixed> $array
     */
    public function cp(array &$array, string $from, string $to): void
    {
        // If $from is not readable
        if ( ! $this->pa->isReadable($array, $from)) {
            throw new AccessException(sprintf('The $from path "%s" isn\'t readable.', $from));
        }

        // Get the value to move
        $value = $this->getValue($array, $from);

        // Check if $to is the root and if it is
        if ($this->isRoot($to)) {
            // Merge the value in the passed $array
            $array = [...$array, ...self::forceArray($value)];

            return;
        }

        // Do the copy
        /** @phpstan-ignore parameterByRef.type */
        $this->pa->setValue($array, $to, $value);
    }

    /**
     * Copy a value from $from path to $to path.
     *
     * If the $to path already has a value, an AccessException is thrown.
     */
    public function cpSafe(array &$array, string $from, string $to): void
    {
        // If $from is not readable
        if ( ! $this->isWritable($array, $to)) {
            throw new AccessException(sprintf('The $to path "%s" isn\'t writable as it already has a value.', $to));
        }

        $this->cp($array, $from, $to);
    }

    /**
     * Edits the value at the given path.
     */
    public function edit(array &$array, string $path, mixed $value): void
    {
        // If $path is not writable
        if ( ! $this->isRoot($path) && ! $this->pa->isReadable($array, $path)) {
            throw new AccessException(sprintf('The path "%s" you are trying to edit isn\'t readable and so cannot be edited.', $path));
        }

        if ($this->isRoot($path)) {
            $array = $value;
        } else {
            /** @phpstan-ignore parameterByRef.type */
            $this->pa->setValue($array, $path, $value);
        }
    }

    /**
     * Merges $from values into $in path.
     *
     * @param array<int|string, mixed> $array
     */
    public function merge(array &$array, string $from, string $in): void
    {
        $fromValue = $this->getValue($array, $from);
        $this->rm($array, $from);
        $inValue   = $this->getValue($array, $in);

        $merged = [...self::forceArray($inValue), ...self::forceArray($fromValue)];

        $this->edit($array, $in, $merged);
    }

    /**
     * Moves an element from $from path to $to path.
     *
     * If $to path already has a value, it will be overwritten.
     *
     * @psalm-suppress InvalidArgument
     */
    public function mv(array &$array, string $from, string $to): void
    {
        // Do the moving
        $this->cp($array, $from, $to);

        // Remove the original value
        $this->rm($array, $from);
    }

    /**
     * Moves an element from $from path to $to path.
     *
     * If $to path already has a value, an AccessException will be thrown.
     *
     * @param array<int|string, mixed> $array
     */
    public function mvSafe(array &$array, string $from, string $to): void
    {
        // Do the moving
        $this->cpSafe($array, $from, $to);

        // Remove the original value
        $this->rm($array, $from);
    }

    /**
     * Moves a value one level up in the array.
     *
     * Example:
     *
     *     $array = [
     *         'level1' => ['value 1.1', 'value 1.2', 'value 1.3'],
     *         'level2' => ['key1' => 'value 2.1', 'value 2.2', 'value 2.3']
     *     ];
     *
     * is transformed into the array:
     *
     *     $array = [
     *         'level1' => ['value 1.1', 'value 1.2', 'value 1.3'],
     *         'key' => 'value 2.1',
     *         1 => 'value 2.2',
     *         2 => 'value 2.3'
     *     ];
     *
     * @param array<int|string, mixed> $array
     */
    public function mvUp(array &$array, string $path): void
    {
        // Build the path object
        $pathObject = new PropertyPath($path);

        // get the values to move one level up
        $values = $this->pa->getValue($array, $path);

        // Remove the key to move one level up
        $this->rm($array, $path);

        /*
         * Fails on
         * - phpstan (ubuntu-latest, 8.0, --prefer-stable --prefer-lowest, ~3.4)
         * - phpstan (ubuntu-latest, 7.3, --prefer-stable --prefer-lowest, ~3.4)
         * - phpstan (ubuntu-latest, 7.4, --prefer-stable --prefer-lowest, ~3.4)
         *
         * @phpstan-ignore-next-line
         */
        $parentPath = $pathObject->getParent() ?? '[]';

        // Get the values of the up level
        $parentValues = $this->getValue($array, $parentPath);

        $mergedArray = [...self::forceArray($parentValues), ...self::forceArray($values)];

        $this->edit($array, $parentPath, $mergedArray);
    }

    /**
     * Removes an element from the array.
     *
     * @see http://stackoverflow.com/a/16698855/1399706
     *
     * @param array<int|string, mixed> $array
     *
     * @psalm-suppress PossiblyNullArrayOffset
     */
    public function rm(array &$array, string $path): void
    {
        // This way it will trigger an error if the calculated value is not correct
        $node         = null;
        $propertyPath = new PropertyPathBuilder($path);
        $parentLevel  = null;
        $currentLevel = &$array;
        $nodes        = $propertyPath->getPropertyPath();

        if ( ! $nodes instanceof PropertyPathInterface) {
            throw new \InvalidArgumentException(sprintf('The path "%s" doesn\'t contain any node.', $path));
        }

        foreach ($nodes->getElements() as &$node) {
            $parentLevel  = &$currentLevel;
            $currentLevel = &$currentLevel[$node];
        }

        if (null !== $parentLevel) {
            unset($parentLevel[$node]);
        }
    }

    /**
     * Adds a parent key to the current array.
     *
     * For example, given this array:
     *
     *     $array = [
     *         0 => 'element 0', 1 => 'element 1', 2 => 'element 2', ...
     *     ];
     *
     * calling ArrayWriter('', 'root') will rearrange the array in this way:
     *
     *     $array = [
     *         'root' => [
     *             0 => 'element 0', 1 => 'element 1', 2 => 'element 2', ...
     *         ]
     *     ];
     */
    public function wrap(array &$array, string $path, string $wrapperName): void
    {
        // Get the value to move: if path is empty, get the full array graph
        $value = ('' === $path || '[]' === $path) ? $array : $this->pa->getValue($array, $path);

        // Remove eventual [ or ] from the $wrapperName
        $wrapperName = self::unpathize($wrapperName);

        $value = [$wrapperName => $value];

        // Set the new value: if path is empty, edit the full Array Graph, edit only the given path instead
        if ('' === $path) {
            $array = $value;
        } else {
            $this->edit($array, $path, $value);
        }
    }

    /**
     * Transforms a GraphObject name into a PropertyAccess path.
     *
     * @param int|string $string Can be a position or an object name
     *
     * @psalm-suppress PossiblyInvalidOperand
     */
    public static function pathize(string $string): string
    {
        return '[' . $string . ']';
    }

    /**
     * Removes "[" and "]" from path.
     */
    public static function unpathize(string $path): string
    {
        return \str_replace(['[', ']'], '', $path);
    }

    /**
     * Forces a value to be an array.
     *
     * @return array<int|string, mixed>
     */
    public static function forceArray($value): array
    {
        // If the $value is not an array...
        if ( ! \is_array($value)) {
            // Make it an array
            return [$value];
        }

        return $value;
    }
}
