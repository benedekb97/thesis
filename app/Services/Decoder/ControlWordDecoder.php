<?php

declare(strict_types=1);

namespace App\Services\Decoder;

use App\Entities\DST\Position;

class ControlWordDecoder implements ControlWordDecoderInterface
{
    public function decode(?Position $currentPosition, array $bytes): Position
    {
        $positionDifference = clone $currentPosition ?? new Position();

        foreach (self::MOVEMENT_BYTE_MAP as $index => $values) {

            foreach ($values as $bitIndex => $value) {
                if ($bytes[$index][$bitIndex]) {
                    $positionDifference->setHorizontal(
                        $positionDifference->getHorizontal() + $value[0] / self::DECODE_FACTOR
                    );

                    $positionDifference->setVertical(
                        $positionDifference->getVertical() + $value[1] / self::DECODE_FACTOR
                    );
                }
            }

        }

        return $positionDifference;
    }
}
