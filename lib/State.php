<?php

namespace SimpleSAML\Module\monitor;

class State
{
    const OK = 2;
    const WARNING = 1;
    const NOSTATE = 0;
    const ERROR = -1;
    const FATAL = -2;
    const SKIPPED = -999;
}
