<?php

namespace App\UI\Http\Api\Subscriber;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Domain\Subscriber\SubscriberList;
use App\Domain\User\User;
use App\Infrastructure\Subscriber\SubscriberManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class ImportCsvAction extends Controller
{
    /**
     * @Route(
     *     name="api_subscriber_list_import_csv",
     *     path="/api/subscriber_lists/{id}/import-csv",
     *     methods={"POST"},
     *     defaults={
     *         "_api_resource_class"=SubscriberList::class,
     *         "_api_receive"=true
     *     }
     * )
     */
    public function __invoke(SubscriberList $list, Request $request, SubscriberManager $manager)
    {
        /** @var $file UploadedFile */
        $file = $request->files->get('csv');
        $this->validateFile($file);

        /**
         * @var User
         */
        $user = $this->getUser();
        $account = $user->getActiveProfile()->getAccount();

        $manager->fromCsv(
            $account,
            $user,
            $list,
            $file->getRealPath()
        );

        return new JsonResponse([
            'status' => $manager->getLastCsvImportReport(),
        ], Response::HTTP_CREATED);
    }

    protected function validateFile($file)
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate($file, [
            'file' => new Assert\File([
                'maxSize' => '2M',
                'mimeTypes' => [
                    'text/plain', 'text/csv',
                ],
                'mimeTypesMessage' => 'Please upload a valid CSV file',
            ]),
        ]);

        if ($violations->count()) {
            throw new ValidationException($violations);
        }
    }
}
