<?php
declare(strict_types = 1);

/*
 * This file is part of the FODDBALClickHouse package -- Doctrine DBAL library
 * for ClickHouse (a column-oriented DBMS for OLAP <https://clickhouse.yandex/>)
 *
 * (c) FriendsOfDoctrine <https://github.com/FriendsOfDoctrine/>.
 *
 * For the full copyright and license inflormation, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOD\DBALClickHouse\Types;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;


/**
 * Array(Date) Type class
 */
class ArrayDateType extends AbstractArrayType implements DatableClickHouseTypeInterface
{
    /**
     * @inheritdoc
     */
    public function getBaseClickHouseType(): string
    {
        return DatableClickHouseTypeInterface::TYPE_DATE;
    }

    /**
     * {@inheritDoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return \array_map(
            static function ($stringDatetime) use ($platform) {
                return \DateTime::createFromFormat($platform->getDateFormatString(), $stringDatetime);
            },
            (array) $value
        );
    }

    /**
     * {@inheritDoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return '[' . \implode(
            ', ',
            \array_map(
                static function (\DateTime $datetime) use ($platform) {
                    return "'" . $datetime->format($platform->getDateFormatString()) . "'";
                },
                \array_filter(
                    (array) $value,
                    static function ($datetime) {
                        return $datetime instanceof \DateTime;
                    }
                )
            )
        ) . ']';
    }

    /**
     * {@inheritDoc}
     */
    public function getBindingType(): int
    {
        return ParameterType::INTEGER;
    }
}
