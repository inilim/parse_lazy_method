<?php

function cus_trim($value, $charlist = null)
{
    if ($charlist === null) {
        return \preg_replace('~^[\s\x{FEFF}\x{200B}\x{200E}]+|[\s\x{FEFF}\x{200B}\x{200E}]+$~u', '', $value) ?? \trim($value);
    }

    return \trim($value, $charlist);
}

function replaceLast(string $search, string $replace, string $subject): string
{
    if ($search === '') return $subject;

    $position = \strrpos($subject, $search);

    if ($position !== false) {
        return \substr_replace($subject, $replace, $position, \strlen($search));
    }

    return $subject;
}

function replaceFirst(string $search, string $replace, string $subject): string
{
    if ($search === '') return $subject;

    $position = \strpos($subject, $search);

    if ($position !== false) {
        return \substr_replace($subject, $replace, $position, \strlen($search));
    }

    return $subject;
}

function cus_ltrim($value, $charlist = null)
{
    if ($charlist === null) {
        return \preg_replace('~^[\s\x{FEFF}\x{200B}\x{200E}]+~u', '', $value) ?? \ltrim($value);
    }

    return \ltrim($value, $charlist);
}

function squish(string $value): string
{
    return \preg_replace('~(\s|\x{3164}|\x{1160})+~u', ' ', \cus_trim($value));
}
