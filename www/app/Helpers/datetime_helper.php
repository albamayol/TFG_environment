<?php

use CodeIgniter\I18n\Time;

function toUserTimezone($utcDate, string $format = 'Y-m-d H:i:s'): string {
    if (empty($utcDate)) {
        return '';
    }

    $tz = getUserTimezone();

    return Time::parse($utcDate, 'UTC')
               ->setTimezone($tz)
               ->format($format);
}

/**
 * Return the user’s timezone from the session, defaulting to UTC
 */
function getUserTimezone(): string {
    return session()->get('user_timezone') ?? 'UTC';
}

/**
 * Convert a datetime string typed in the user’s local timezone to a UTC datetime string
 */
function fromUserTimezone(string $localDate, string $format = 'Y-m-d H:i:s'): string {
    if (empty($localDate)) {
        return '';
    }
    $tz = getUserTimezone();
    return Time::parse($localDate, $tz)->setTimezone('UTC')->format($format);
}

/**
 * Get the current time in UTC as a CodeIgniter Time object
 */
function utcNow(): Time {
    return Time::now('UTC');
}

/**
 * Get the start and end of the current UTC day as Time objects.
 *
 * Returns an array like [$start, $end], where $start is 00:00:00 UTC and $end is 00:00:00 UTC on the following day
 */
function getUtcDayBounds(): array {
    $now = utcNow();
    $start = Time::create($now->getYear(), $now->getMonth(), $now->getDay(), 0, 0, 0, 'UTC');
    $end   = $start->addDays(1);
    return [$start, $end];
}