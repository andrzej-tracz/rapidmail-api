<?php

namespace App\UI\Http\Api\Template;

use App\Application\Command\Template\ActivateTemplateCommand;
use App\Domain\Template\Template;
use App\Infrastructure\Bus\CommandBusInterface;
use App\Infrastructure\Template\Repository\TemplateRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ActivateTemplateAction
{
    const ACTION_NAME = 'api_template_activate';

    /**
     * @Route(
     *     name=ActivateTemplateAction::ACTION_NAME,
     *     path="/api/template/activate/{id}",
     *     methods={"PUT"},
     *     defaults={
     *      "_api_resource_class"=Template::class,
     *      "_api_receive"=false,
     *      "_api_item_operation_name"=ActivateTemplateAction::ACTION_NAME
     *      }
     * )
     *
     * @throws EntityNotFoundException
     */
    public function activate($id, Request $request, CommandBusInterface $bus, TemplateRepository $repository)
    {
        /** @var $template Template */
        $template = $repository->find($id);
        $code = $request->request->get('purchaseCode');

        if (!$template) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(Template::class, $id);
        }

        $command = new ActivateTemplateCommand($template, $code);
        $bus->handle($command);

        return $template;
    }
}
