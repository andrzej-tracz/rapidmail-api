<?php

namespace App\UI\Http\Api\Media;

use App\Infrastructure\Media\MediaEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UploadAction extends Controller
{
    /**
     * @Route(
     *     name="api_media_upload",
     *     path="/api/media",
     *     methods={"POST"}
     * )
     */
    public function __invoke(Request $request, MediaEncoder $encoder)
    {
        $media = $request->request->get('media');
        $base64 = $media['base64'] ?? null;
        $width = $media['width'] ?? 1600;
        $url = false;

        if ($base64) {
            $encoder->setOptions([
                'max_width' => $width * 2,
            ]);
            $url = $encoder->encodeFromBase($base64);
        }

        return new JsonResponse([
            'url' => $url,
        ], Response::HTTP_CREATED);
    }
}
