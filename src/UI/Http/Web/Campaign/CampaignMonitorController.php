<?php

namespace App\UI\Http\Web\Campaign;

use App\Domain\Campaign\Message;
use App\Infrastructure\Campaign\Repository\MessageRepository;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampaignMonitorController
{
    /**
     * @Route(path="/collect/{token}", methods={"GET"}, name="web_messages_collect", )
     */
    public function collect($token, MessageRepository $messages)
    {
        /**
         * @var Message
         */
        $message = $messages->findOneBy([
            'token' => $token,
            'openedAt' => null,
        ]);

        if ($message) {
            $message->setOpenedAt(Chronos::now()->toMutable());
            $messages->save($message);
        }

        return $this->cretePixelResponse();
    }

    protected function cretePixelResponse()
    {
        return new Response(
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII='),
            200,
            [
                'Content-Type' => 'image/png',
            ]
        );
    }
}
