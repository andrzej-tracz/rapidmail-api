<?php

namespace App\Infrastructure\Subscriber;

use App\Domain\Account\Account;
use App\Domain\Subscriber\Subscriber;
use App\Domain\Subscriber\SubscriberList;
use App\Domain\User\User;
use App\Infrastructure\Subscriber\Dto\Subscriber as SubscriberDto;
use App\Infrastructure\Subscriber\Repository\SubscriberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubscriberManager
{
    /**
     * @var SubscriberRepository
     */
    protected $repository;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var array
     */
    protected $lastCsvImportReport;

    public function __construct(SubscriberRepository $repository, ValidatorInterface $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * Create single subscriber instance.
     *
     * @param Account        $account
     * @param User           $user
     * @param SubscriberList $list
     * @param array          $attributes
     *
     * @return Subscriber
     */
    public function create(Account $account, User $user, SubscriberList $list, array $attributes = [])
    {
        $subscriber = new Subscriber();
        $subscriber->setAccount($account);
        $subscriber->setUser($user);
        $subscriber->setSubscribersList($list);

        $subscriber->setName($attributes['name'] ?? '');
        $subscriber->setSurname($attributes['surname'] ?? '');
        $subscriber->setEmail($attributes['email'] ?? '');

        $this->repository->save($subscriber);

        return $subscriber;
    }

    /**
     * Import subscribers from CSV file.
     *
     * @param Account        $account
     * @param User           $user
     * @param SubscriberList $list
     * @param string         $path
     *
     * @return ArrayCollection
     */
    public function fromCsv(Account $account, User $user, SubscriberList $list, string $path)
    {
        $this->resetLastCsvReport();

        $raw = file($path);
        $csv = array_map('str_getcsv', $raw);
        array_walk($csv, function (&$a) use ($csv) {
            $a = array_combine($csv[0], $a);
        });
        array_shift($csv);

        $items = array_filter(array_map(function ($row) use ($account, $user, $list) {
            $dto = SubscriberDto::fromArray($row);
            $errors = $this->validator->validate($dto);

            if ($errors && $errors->count()) {
                $this->hintCsvError();

                return false;
            }

            $exists = $this->repository->findOneBy([
                'email' => $dto->email(),
                'subscribersList' => $list,
            ]);

            if ($exists) {
                $this->hintCsvDuplicated();

                return false;
            }

            $entry = $this->create($account, $user, $list, $row);
            $this->hintCsvImported();

            return $entry;
        }, $csv));

        return new ArrayCollection($items);
    }

    public function getLastCsvImportReport()
    {
        return $this->lastCsvImportReport;
    }

    protected function resetLastCsvReport()
    {
        $this->lastCsvImportReport = [
            'error' => 0,
            'imported' => 0,
            'duplicated' => 0,
        ];
    }

    protected function hintCsvError()
    {
        $this->hintCsvReport('error');
    }

    protected function hintCsvImported()
    {
        $this->hintCsvReport('imported');
    }

    protected function hintCsvDuplicated()
    {
        $this->hintCsvReport('duplicated');
    }

    protected function hintCsvReport($key)
    {
        ++$this->lastCsvImportReport[$key];
    }
}
