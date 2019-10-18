<?php

namespace SimpleSAML\Module\Monitor;

class State
{
    const OK = 2;
    const WARNING = 1;
    const NOSTATE = 0;
    const ERROR = -2;
    const FATAL = -3;
    const SKIPPED = -1;
}
