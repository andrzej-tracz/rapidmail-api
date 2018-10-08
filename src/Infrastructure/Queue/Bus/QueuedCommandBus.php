<?php

namespace App\Infrastructure\Queue\Bus;

use App\Infrastructure\Bus\CommandBus;
use App\Infrastructure\Bus\CommandBusInterface;

class QueuedCommandBus extends CommandBus implements CommandBusInterface
{
}
