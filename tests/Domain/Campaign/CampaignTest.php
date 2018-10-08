<?php

namespace App\Test\Application\Domain\Campaign;

use App\Domain\Campaign\Campaign;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class CampaignTest extends TestCase
{
    /**
     * @test
     */
    public function it_is_instantiable()
    {
        $campaign = new Campaign();

        $this->assertTrue($campaign instanceof Campaign);
    }

    /**
     * @test
     */
    public function it_has_valid_constants()
    {
        $this->assertSame('draft', Campaign::STATUS_DRAFT);
        $this->assertSame('sending', Campaign::STATUS_SENDING);
        $this->assertSame('paused', Campaign::STATUS_PAUSED);
        $this->assertSame('done', Campaign::STATUS_SENT);
    }

    /**
     * @test
     */
    public function it_has_default_collections()
    {
        $campaign = new Campaign();
        $this->assertTrue($campaign->getMessages() instanceof ArrayCollection);
        $this->assertSame(0, $campaign->getMessages()->count());
    }

    /**
     * @test
     */
    public function it_has_default_draft_status()
    {
        $campaign = new Campaign();

        $this->assertSame(Campaign::STATUS_DRAFT, $campaign->getStatus());
        $this->assertTrue($campaign->isDraft());
    }
}
