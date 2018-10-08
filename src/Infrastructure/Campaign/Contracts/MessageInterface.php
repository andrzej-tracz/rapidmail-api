<?php

namespace App\Infrastructure\Campaign\Contracts;

use App\Domain\Campaign\Campaign;

interface MessageInterface
{
    public function getCampaign(): ?Campaign;

    public function getTitle(): string;

    public function getPreheader(): string;

    public function getContents(): string;

    public function getFromName(): string;

    public function getFromEmail(): string;

    public function getTo(): string;
}
