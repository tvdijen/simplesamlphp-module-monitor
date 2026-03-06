<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor;

class State
{
    public const int OK = 2;

    public const int WARNING = 1;

    public const int NOSTATE = 0;

    public const int ERROR = -2;

    public const int FATAL = -3;

    public const int SKIPPED = -1;
}
