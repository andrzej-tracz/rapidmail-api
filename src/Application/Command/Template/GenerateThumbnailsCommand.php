<?php

namespace App\Application\Command\Template;

use App\Domain\Template\Template;
use App\Infrastructure\Queue\InteractsWithQueue;
use App\Infrastructure\Queue\ShouldQueue;

class GenerateThumbnailsCommand implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var Template
     */
    protected $template;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    public function template()
    {
        return $this->template;
    }
}
