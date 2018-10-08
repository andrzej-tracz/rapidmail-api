<?php

namespace App\Infrastructure\Queue;

abstract class Queueable implements ShouldQueue
{
    use InteractsWithQueue;
}
