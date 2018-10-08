<?php

namespace App\Application\Command\Template;

use App\Domain\Template\Template;
use App\Infrastructure\Validator\Constraints\UniqueIn;
use Webmozart\Assert\Assert;

class ActivateTemplateCommand
{
    /**
     * The template instance to activate.
     *
     * @var Template
     */
    protected $template;

    /**
     * Purchased code.
     *
     * @var string
     *
     * @UniqueIn(entityClass="App\Domain\Template\PurchasedTemplate", field="purchaseCode")
     */
    protected $code;

    public function __construct(Template $template, string $code)
    {
        Assert::notEmpty($code);

        $this->template = $template;
        $this->code = $code;
    }

    public function template()
    {
        return $this->template;
    }

    public function code()
    {
        return $this->code;
    }
}
