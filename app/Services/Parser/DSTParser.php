<?php

declare(strict_types=1);

namespace App\Services\Parser;

use App\Services\Decoder\ControlWordDecoder;
use App\Services\Decoder\ControlWordDecoderInterface;
use App\Entities\DST\DST;
use App\Entities\DST\DSTInterface;
use App\Entities\DST\Position;
use Illuminate\Filesystem\FilesystemManager;

class DSTParser implements DSTParserInterface
{
    private FilesystemManager $filesystemManager;

    private ControlWordDecoderInterface $controlWordDecoder;

    private int $stitchIndicator = 0;

    public function __construct(
        FilesystemManager $filesystemManager,
        ControlWordDecoder $controlWordDecoder
    ) {
        $this->filesystemManager = $filesystemManager;
        $this->controlWordDecoder = $controlWordDecoder;
    }

    public function parse(string $fileName): DSTInterface
    {
        $fileContent = $this->filesystemManager->disk(config('filesystems.default'))->get($fileName);

        $fileContentHex = $this->stringToHexadecimal($fileContent);

        if (substr($fileContent, 0, 2) === 'LA') {
            $newFileContent = substr($fileContent, 512);

            $fileContentHex = $this->stringToHexadecimal($newFileContent);
        }

        $dst = new DST();

        /** @var string $command */
        foreach (str_split($fileContentHex, DSTInterface::COMMAND_LENGTH) as $command) {
            if (strlen($command) !== DSTInterface::COMMAND_LENGTH) {
                continue;
            }

            $bytes = str_split($command, DSTInterface::BYTE_LENGTH);

            foreach ($bytes as & $byte) {
                $byte = $this->hexadecimalToBinary($byte);
            }

            $this->addStitchToDSTWithControlBytes($dst, $bytes);
        }

        return $dst;
    }

    private function stringToHexadecimal(string $string): string
    {
        $hex = '';

        foreach (str_split($string) as $character) {
            $ord = ord($character);

            $hexCode = dechex($ord);

            $hex .= substr('0'.$hexCode, -2);
        }

        return strtoupper($hex);
    }

    private function hexadecimalToBinary(string $hexadecimal): string
    {
        $binary = '';

        foreach (str_split($hexadecimal) as $character) {
            if (array_key_exists(strtolower($character), self::HEXADECIMAL_BINARY_MAP)) {
                $binary .= self::HEXADECIMAL_BINARY_MAP[strtolower($character)];
            }
        }

        return $binary;
    }

    private function getStitchType(string $character): string
    {
        if (!(boolean)substr($character, 0, 1) && !(boolean)substr($character, 1, 1)) {
            return DSTInterface::STITCH_TYPE_NORMAL;
        }


        if (substr($character, 0, 1) && substr($character, 1, 1)) {
            return DSTInterface::STITCH_TYPE_COLOR_CHANGE;
        }

        if (substr($character, 0, 1) && !(boolean)substr($character, 1, 1) ) {
            return DSTInterface::STITCH_TYPE_JUMP;
        }

        return DSTInterface::STITCH_TYPE_OTHER;
    }

    private function getPositionChange(Position $position, array $bytes): Position
    {
        return $this->controlWordDecoder->decode($position, $bytes);
    }

    private function addStitchToDSTWithControlBytes(DSTInterface $dst, array $controlBytes): void
    {
        $stitchType = $this->getStitchType(substr($controlBytes[2], 0, 2));

        switch ($stitchType) {
            case DSTInterface::STITCH_TYPE_JUMP:
            {
                $this->stitchIndicator = 0;

                $dst->setCurrentPosition(
                    $this->getPositionChange(
                        $dst->getCurrentPosition(), $controlBytes
                    )
                );

                break;
            }

            case DSTInterface::STITCH_TYPE_COLOR_CHANGE:
            {
                $this->stitchIndicator = 0;

                $dst->setCurrentPosition(
                    $this->getPositionChange(
                        $dst->getCurrentPosition(), $controlBytes
                    )
                );

                $dst->incrementColorCount();

                break;
            }

            case DSTInterface::STITCH_TYPE_OTHER:
            case DSTInterface::STITCH_TYPE_NORMAL:
            {
                $newPosition = $this->getPositionChange($dst->getCurrentPosition(), $controlBytes);

                $this->stitchIndicator++;

                if ($this->stitchIndicator > 1) {
                    $dst->incrementStitchCount();

                    $dst->addStitchByNextPosition($newPosition);
                }

                $dst->setCurrentPosition($newPosition);


                $this->setLimits($dst, $newPosition);

                break;
            }

            default: break;
        }
    }

    private function setLimits(DSTInterface $dst, Position $newPosition): void
    {
        if ($dst->getMaxPosition()->getVertical() < $newPosition->getVertical()) {
            $dst->getMaxPosition()->setVertical($newPosition->getVertical());
        }

        if ($dst->getMaxPosition()->getHorizontal() < $newPosition->getHorizontal()) {
            $dst->getMaxPosition()->setHorizontal($newPosition->getHorizontal());
        }

        if ($dst->getMinPosition()->getVertical() > $newPosition->getVertical()) {
            $dst->getMinPosition()->setVertical($newPosition->getVertical());
        }

        if ($dst->getMinPosition()->getHorizontal() > $newPosition->getHorizontal()) {
            $dst->getMinPosition()->setHorizontal($newPosition->getHorizontal());
        }
    }
}
