<?php

namespace App\UI\Http\Api\Project;

use App\Domain\Project\Project;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SendPreviewAction
{
    /**
     * @Route(
     *     name="api_project_send_preview",
     *     path="/api/projects/send-preview/{id}",
     *     methods={"POST"},
     *     defaults={
     *          "_api_resource_class"=Project::class,
     *          "_api_item_operation_name"="api_project_send_preview",
     *          "_api_receive"=false
     *      }
     * )
     *
     * @return Project
     */
    public function __invoke(Project $project, Request $request, \Swift_Mailer $mailer)
    {
        $email = filter_var($request->request->get('email'), FILTER_VALIDATE_EMAIL);
        $container = $request->request->get('container');
        $sections = $container['sections'] ?? null;
        $sections = array_map(function ($item) {
            return $item['contents'] ?? '';
        }, $sections);

        if (!$email) {
            throw new \RuntimeException('Invalid email address');
        }

        $template = $project->getTemplate();
        $header = $template->getLayout()->getHeader();
        $footer = $template->getLayout()->getFooter();
        $sections = count($sections) ? $sections : $this->getTemplateSections($project);
        $body = join("\n", $sections);

        $message = (new \Swift_Message())
            ->setContentType('text/html')
            ->setFrom(['office@api.local' => 'mail-api.local'])
            ->setSubject("{$project->getName()} - template preview")
            ->setTo($email)
            ->setBody(
                $header.$body.$footer
            );

        $mailer->send($message);

        return $project;
    }

    /**
     * @param Project $project
     *
     * @return array
     */
    private function getTemplateSections(Project $project)
    {
        $contents = $project->getContents();
        $sections = array_map(function ($item) {
            return $item['contents'];
        }, $contents['sections']);

        return $sections;
    }
}
