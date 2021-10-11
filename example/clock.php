<?php

//namespace mattbarber\Example;
include '../vendor/autoload.php';

use Mattbarber\CountdownClock\Clock;
use Mattbarber\CountdownClock\ClockInterface;

/**
 * Example class to inject into the countdown clock
 * Implements ClockInterface
 **/
class MyClock implements ClockInterface
{

    /**
     * @return string clock name
     */
    public function getAmountOfSeconds(): int
    {
        return 60;
    }

    /**
     * @return /DateTime deadline date and time
     */
    public function getDeadlineDateTime(): \DateTime
    {
        return \DateTime::createFromFormat('d/m/Y H:i:s', "31/12/2017 00:00:01");
    }

    /**
     * @return string time zone
     */
    public function getTimezone(): string
    {
        return "Europe/London";
    }

    /**
     * @return string symbol between countdown date elements
     */
    public function getSeparator(): string
    {
        return "|";
    }

    /**
     * @return integer spacing between symbol between countdown date elements
     */
    public function getSeparatorSpacing(): int
    {
        return 8;
    }

    /**
     * @return integer length of days countdown date element
     */
    public function getDaysLen(): int
    {
        return 2;
    }

    /**
     * @return integer spacing between characters
     */
    public function getSpacing(): int
    {
        return 2;
    }

    /**
     * @return string font file path
     */
    public function getFontFilePath(): string
    {
        return "/Library/Fonts/Verdana.ttf";
    }

    /**
     * @return integer size of font
     */
    public function getFontSize(): int
    {
        return 20;
    }

    /**
     * @return integer font start x
     */
    public function getPaddingHorizontal(): int
    {
        return 25;
    }

    /**
     * @return integer font start y
     */
    public function getPaddingVertical(): int
    {
        return 25;
    }

    /**
     * @return array [r, g, b]
     */
    public function getFontColor(): array
    {
        return [120, 0, 0];
    }

    /**
     * @return integer font angle
     */
    public function getFontAngle(): int
    {
        return 0;
    }

    /**
     * @return string background image file path
     */
    public function getBackgroundImageFilePath(): ?string
    {
        return null;
    }

    /**
     * @return array [r, g, b] integer array if getBackgroundImageFilePath returns false
     *
     */
    public function getBackgroundImageColor(): array
    {
        return [200, 255, 255];
    }
}


$clockItf = new MyClock();
$countdown = new Clock($clockItf);
//Call specifically (incase any further changes)
$countdown->generateImage();
