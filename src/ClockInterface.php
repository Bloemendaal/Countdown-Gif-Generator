<?php

namespace Mattbarber\CountdownClock;

/**
 *
 * @author tp <https://github.com/tom-power>
 */
interface ClockInterface
{
    public function getAmountOfSeconds(): int;

    public function getDeadlineDateTime(): \DateTime;

    public function getTimezone(): string;

    public function getSeparator(): string;

    public function getSeparatorSpacing(): int;

    public function getDaysLen(): int;

    public function getSpacing(): int;

    public function getFontFilePath(): string;

    public function getFontSize(): int;

    public function getPaddingHorizontal(): int;

    public function getPaddingVertical(): int;

    public function getFontAngle(): int;

    public function getBackgroundImageFilePath(): ?string;

    /**
     * @return array [red, green, blue]
     */
    public function getFontColor(): array;

    /**
     * Used if `getBackgroundImageFilePath()` returns false
     * @return array|false [red, green, blue]
     */
    public function getBackgroundImageColor(): array;
}
