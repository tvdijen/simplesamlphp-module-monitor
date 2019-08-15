<?php

namespace SimpleSAML\Modules\Monitor;

class State
{
    const OK = 2;
    const WARNING = 1;
    const EMPTY = 0;
    const SKIPPED = -1;
    const ERROR = -2;
    const FATAL = -3;
}
