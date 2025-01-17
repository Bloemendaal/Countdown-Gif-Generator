<?php

namespace Mattbarber\CountdownClock;

/**
 * Countdown gif generator
 * @license : MIT
 *
 * @category : Image production
 * @package : CountdownClock
 * @version : 1.0
 *
 * @author : Matt Barber
 * @created : 3rd July 2015
 * @updated : 4th November 2016
 *
 * @contributors : Tom Power
 */
class Clock
{

    private $dates;
    private $clock;
    private $fixedWidth;
    private $offsets;

    /**
     * Constructor takes clock object as argument
     * @param   $clock  ClockInterface
     * */
    public function __construct(ClockInterface $clock)
    {
        //Convert the deadline to a date time and assign top property
        $this->dates = new \stdClass();
        $this->dates->deadline = $clock->getDeadlineDateTime();
        $this->dates->deadline->setTimeZone(new \DateTimeZone($clock->getTimeZone()));

        $this->dates->now = new \DateTime(date('r', time()));
        $this->dates->now->setTimeZone(new \DateTimeZone($clock->getTimeZone()));

        $this->clock = $clock;

        // get fixed width
        $this->fixedWidth = array_reduce(
            range(0, 9),
            fn ($max, $i) => max($max, $this->getWidth((string) $i)),
            0
        );

        /*
            Create a set of offsets for each potential number [0-9]
            and our separator
        */

        for ($index = 0; $index < 10; $index++) {
            $strIndex = (string) $index;
            $this->offsets[$strIndex] = $this->getOffset($strIndex);
        }

        $this->offsets[$this->clock->getSeparator()] = $this->getOffset($this->clock->getSeparator());
    }

    /**
     * Get an offset used for writing a specific character
     *
     * @param char  $char
     **/
    private function getOffset($char): float
    {
        $width = $this->getWidth($char);
        return ($this->fixedWidth - $width) / 2;
    }

    /**
     * Return the bounding box of a character
     *
     * @param char $char    The character to check
     **/
    private function getBBox($char): array
    {
        return imagettfbbox(
            $this->clock->getFontSize(),
            $this->clock->getFontAngle(),
            $this->clock->getFontFilePath(),
            $char
        );
    }

    /**
     * Return the width of the bounding box
     *
     * @param char $char
     **/
    private function getWidth($char): int
    {
        $bbox = $this->getBBox($char);
        return $bbox[2] - $bbox[0];
    }

    /**
     * Return the height of the bounding box
     *
     * @param char $char
     **/
    private function getHeight($offset = 0): int
    {
        $char = '0123456789' . $this->clock->getSeparator();
        $bbox = imagettfbbox($this->clock->getFontSize() + $offset * 2, $this->clock->getFontAngle(), $this->clock->getFontFilePath(), $char);
        return $bbox[1] - $bbox[7];
    }

    /**
     *  Generates the image using the given design settings and the GifEncoder plugin
     * */
    public function generateImage(): void
    {
        //Some overall variables
        $frames = [];
        $delays = [];
        $delay = 100;

        //Getting some data from the properties
        $dates = $this->dates;

        $separator = $this->clock->getSeparator();

        //Count through our frames
        for ($i = 0; $i <= $this->clock->getAmountOfSeconds(); $i++) {

            $text = '';
            $interval = $dates->deadline->diff($dates->now);

            //If we're at or after the deadline - then just 0 the clock
            if ($dates->deadline < $dates->now) {
                $text = $interval->format(str_pad('0', $this->clock->getDaysLen(), '0') . $separator . '00' . $separator . '00' . $separator . '00');
                $loops = 1;
            }
            //Else format the interval and add a preceeding 0 if it's missing
            else {
                $days = '';
                if ($this->clock->getDaysLen() > 0) {
                    $days = str_pad($interval->days, $this->clock->getDaysLen(), '0', STR_PAD_LEFT);
                }
                $text = $interval->format($days . $separator . '%H' . $separator . '%I' . $separator . '%S');
                $loops = 0;
            }

            //create a new image resource
            if (null !== ($filename = $this->clock->getBackgroundImageFilePath())) {
                $image = \imagecreatefrompng($filename);
            } else {
                /*
                    The width is the fixed width for a single character
                    + the spacing we have around each character, multipled by the amount of characters.
                    + the spacing around the sepeartors * each separator
                    - the spacing lost because of the separators
                */
                $width = (($this->fixedWidth + $this->clock->getSpacing()) * strlen($text)) + (($this->clock->getSeparatorSpacing() - $this->clock->getSpacing()) * 6) + $this->clock->getPaddingHorizontal() * 2;
                $image = @imagecreate($width, $this->getHeight($this->clock->getPaddingVertical()))
                    or die("Cannot Initialize new GD image stream");
                $bg = $this->clock->getBackgroundImageColor();
                imagefill($image, 0, 0, imagecolorallocate($image, $bg[0], $bg[1], $bg[2]));
            }

            $fontColors = $this->clock->getFontColor();

            //overlay the text on this resource
            $this->imagettftextSp(
                $image,
                $this->clock->getFontSize(),
                $this->clock->getFontAngle(),
                imagecolorallocate($image, $fontColors[0] ?? 0, $fontColors[1] ?? 0, $fontColors[2] ?? 0),
                $this->clock->getFontFilePath(),
                $text,
                $this->clock->getSpacing(),
                $this->clock->getSeparator(),
                $this->clock->getSeparatorSpacing()
            );

            //buffer...
            ob_start();
            imagegif($image);
            $frames[] = ob_get_contents();
            $delays[] = $delay;
            ob_end_clean();
            //if we're after the deadline - break
            if ($dates->deadline < $dates->now) {
                break;
            }
            //else add a second and the next frame
            $dates->now->modify('+1 second');
        }
        //expire this image instantly
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        //generate a new GIF given the frames, the delay and the loop counter
        $gif = new AnimatedGif($frames, $delays, $loops);
        $gif->display();
    }

    /**
     * Create the text layer for the image (char by char)
     *
     * @param \GdImage  $image              The image resource we are creating the text on
     * @param float     $size               The font size
     * @param float     $angle              The angle in degrees
     * @param int       $colour             The colour for the layer being painted
     * @param string    $font               The font file path
     * @param string    $text               The base text string
     * @param int       $spacing            Char spacing in the string
     * @param char      $separator          The sepeartor for the date parts
     * @param int       $separatorSpacing   The spacing around the separator
     *
     **/
    private function imagettftextSp($image, $size, $angle, $color, $font, $text, $spacing = 0, $separator = null, $separatorSpacing = 0): void
    {
        $x = $this->clock->getPaddingHorizontal();
        $y = round(imagesy($image) / 2 + $this->getHeight() / 2) - 1;

        foreach (str_split($text) as $i => $char) {
            // increment x by the offset for the given character
            $x += $this->offsets[$char];
            // then write that specific character to the image
            imagettftext($image, $size, $angle, $x, $y, $color, $font, $char);
            //  remove the offset
            $x -= $this->offsets[$char];
            // then space by fixed width, plus any additional spacing
            $thisSpacing = $this->isSeperatorSpacing($text, $i, $separator) ? $separatorSpacing : $spacing;
            $x += $thisSpacing + $this->fixedWidth;
        }
    }

    /**
     * Check if the character is a sepeartor
     *
     * @param string    $text       Text string
     * @param int       $i          The current index
     * @param char      $separator  The separator symbol
     **/
    private function isSeperatorSpacing($text, $i, $separator): bool
    {
        return $this->getNextChar($text, $i) === $separator || $text[$i] === $separator;
    }

    /**
     * Return the next character in the string, or null
     *
     * @param string    $text       Text string
     * @param int       $i          The current index
     *
     * @return char
     **/
    private function getNextChar($text, $i): ?string
    {
        return $i + 1 < strlen($text) ? $text[$i + 1] : null;
    }
}
