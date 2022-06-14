<?php

namespace SimpleSAML\Module\monitor;

class State
{
    public const OK = 2;
    public const WARNING = 1;
    public const NOSTATE = 0;
    public const ERROR = -2;
    public const FATAL = -3;
    public const SKIPPED = -1;
}
