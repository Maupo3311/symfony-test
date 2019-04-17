<?php

namespace AppBundle\Enum;

/**
 * Class BaseEnum
 * @package AppBundle\Enum
 */
abstract class BaseEnum
{
    /**
     * @return array
     * @throws \ReflectionException
     */
    public static function toArray()
    {
        return (new \ReflectionClass(static::class))->getConstants();
    }

    /**
     * @param bool $withLabels
     * @return array|null
     * @throws \ReflectionException
     */
    public static function getChoices($withLabels = false)
    {
        $constants = static::toArray();
        $titles    = static::getTitles();

        return $withLabels ? ($titles ? array_flip($titles) : $constants) : array_values($constants);
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public static function getDescription()
    {
        $constants = static::toArray();
        $titles    = static::getTitles();

        return implode(', ', array_map(
            function ($k, $v) {
                return $k . ' - ' . $v;
            },
            array_keys($constants),
            $titles ? array_flip($titles) : $constants
        ));
    }

    /**
     * @return array
     */
    public static function getTitles()
    {
        return [];
    }

    /**
     * @param $key
     * @return string
     */
    public static function getTitle($key)
    {
        $titles = static::getTitles();

        if (in_array($key, array_keys($titles))) {
            return $titles[$key];
        } else {
            throw new \InvalidArgumentException(sprintf('Enum has not title for key "%s"', $key));
        }
    }
}
