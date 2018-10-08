<?php

namespace App\Infrastructure\Project;

use App\Domain\Campaign\Message;
use App\Domain\Project\Project;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Parser
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->urlGenerator = $generator;
    }

    /**
     * Creates single html output from saved project.
     *
     * @param Project $project
     *
     * @return string
     */
    public function parseFullHtml(Project $project)
    {
        $template = $project->getTemplate();
        $contents = $project->getContents();
        $header = $template->getLayout()->getHeader();
        $footer = $template->getLayout()->getFooter();

        $sections = key_exists('sections', $contents) ? $contents['sections'] : [];
        $body = join("\n", array_map(function ($item) {
            return $item['contents'];
        }, $sections));

        $html = join("\n", [
            $header,
            $body,
            '<img src="{{trackingToken}}" />',
            $footer,
        ]);

        return $html;
    }

    /**
     * Makes final changes to the html message.
     *
     * @param Message $message
     *
     * @return Message
     */
    public function processMessageContents(Message $message): Message
    {
        $subscriber = $message->getSubscriber();
        $account = $message->getCampaign()->getAccount();

        $replaces = [
            '{{firstName}}' => $subscriber->getName(),
            '{{lastName}}' => $subscriber->getSurname(),
            '{{receiver}}' => $subscriber->getEmail(),
            '{{ownerName}}' => $account->getName(),
            '{{ownerEmail}}' => $account->getEmail(),
            '{{trackingToken}}' => $this->urlGenerator->generate('web_messages_collect', [
                'token' => $message->getToken(),
            ], UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        $contents = $message->getContents();
        $contents = str_replace(array_keys($replaces), array_values($replaces), $contents);

        $message->setContents(
            $this->sanitizeHtml($contents)
        );

        return $message;
    }

    /**
     * Sanitize HTML and compress the output.
     *
     * @param $html
     *
     * @return null|string|string[]
     */
    public function sanitizeHtml($html)
    {
        $search = [
            '/\>[^\S ]+/s',
            '/[^\S ]+\</s',
            '/(\s)+/s',
        ];

        $replace = [
            '>',
            '<',
            '\\1',
        ];

        $html = preg_replace($search, $replace, $html);

        return $html;
    }
}
