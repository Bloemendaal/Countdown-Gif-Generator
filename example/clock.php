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
    public function getName()
    {
        return "My Super Clock";
    }

    /**
     * @return /DateTime deadline date and time
     */
    public function getDeadlineDateTime()
    {
        return DateTime::createFromFormat('d/m/Y H:i:s', "31/12/2017 00:00:01");
    }

    /**
     * @return string time zone
     */
    public function getTimezone()
    {
        return "Europe/London";
    }

    /**
     * @return string symbol between countdown date elements
     */
    public function getSeparator()
    {
        return "|";
    }

    /**
     * @return integer spacing between symbol between countdown date elements
     */
    public function getSeparatorSpacing()
    {
        return 8;
    }

    /**
     * @return integer length of days countdown date element
     */
    public function getDaysLen()
    {
        return 2;
    }

    /**
     * @return integer spacing between characters
     */
    public function getSpacing()
    {
        return 2;
    }

    /**
     * @return string font file path
     */
    public function getFontFilePath()
    {
        return "/Library/Fonts/Verdana.ttf";
    }

    /**
     * @return integer size of font
     */
    public function getFontsize()
    {
        return 20;
    }

    /**
     * @return integer font start x
     */
    public function getFontx()
    {
        return 25;
    }

    /**
     * @return integer font start y
     */
    public function getFonty()
    {
        return 25;
    }

    /**
     * @return array [r, g, b]
     */
    public function getFontColor()
    {
        return [120, 0, 0];
    }

    /**
     * @return integer font angle
     */
    public function getFontangle()
    {
        return 0;
    }

    /**
     * @return string background image file path
     */
    public function getBackgroundImageFilePath()
    {
        return false;
    }

    /**
     * @return array [r, g, b] integer array if getBackgroundImageFilePath returns false
     *
     */
    public function getBackgroundImageColor()
    {
        return [200, 255, 255];
    }
}


$clockItf = new MyClock();
$countdown = new Clock($clockItf);
//Call specifically (incase any further changes)
$countdown->generateImage();
