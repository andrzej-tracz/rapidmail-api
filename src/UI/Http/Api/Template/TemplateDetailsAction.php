<?php

namespace App\UI\Http\Api\Template;

use App\Domain\Template\Template;
use App\Infrastructure\Template\Repository\TemplateRepository;
use Symfony\Component\Routing\Annotation\Route;

class TemplateDetailsAction
{
    const ACTION_NAME = 'details';

    /**
     * @var TemplateRepository
     */
    protected $templates;

    public function __construct(TemplateRepository $repository)
    {
        $this->templates = $repository;
    }

    /**
     * @Route(
     *     name="api_template_details",
     *     path="/api/template/details/{id}",
     *     methods={"GET"},
     *     defaults={
     *      "_api_resource_class"=Template::class,
     *      "_api_receive"=false,
     *      "_api_item_operation_name"="api_template_details"
     *      }
     * )
     */
    public function details($id)
    {
        $template = $this->templates->findByNameWithDetails($id);

        return $template;
    }

    /**
     * @Route(
     *     name="api_template_demo_details",
     *     path="/api/demo/template/details/{name}",
     *     methods={"GET"},
     *     defaults={
     *      "_api_resource_class"=Template::class,
     *      "_api_receive"=false,
     *      "_api_item_operation_name"="api_template_demo_details"
     *      }
     * )
     */
    public function demoDetails($name)
    {
        $template = $this->templates->findByNameWithDetails($name);

        return $template;
    }
}
