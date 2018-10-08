<?php

namespace App\UI\Http\Api\Dashboard;

use App\Domain\User\User;
use App\Infrastructure\Campaign\Repository\MessageRepository;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DashboardController
{
    /**
     * Provides dashboard stats for sending messages from last month.
     *
     * @Route(
     *     path="/api/dashboard/stats",
     *     methods={"GET"},
     *     name="api_dashboard_stats"
     * )
     */
    public function sentMessagesStats(MessageRepository $messageRepository, TokenStorageInterface $tokenStorage)
    {
        /** @var $user User */
        $user = $tokenStorage->getToken()->getUser();
        $account = $user->getActiveProfile()->getAccount();

        $from = Chronos::now()->subMonth();
        $to = Chronos::now()->endOfDay();
        $stats = $messageRepository->getDailySentMessageStats($account, $from, $to);
        $index = [];

        foreach ($stats as $stat) {
            $index[$stat['year']][$stat['month']][$stat['day']][$stat['status']] = $stat['count'] ?? 0;
        }

        $days = [];
        $day = $monthAgo = Chronos::now()->subDays(30)->startOfDay();

        do {
            $days[$day->format('Y-m-d')] =
                $index[$day->year][$day->month][$day->day]
                ?? [
                    'failed' => 0,
                    'sent' => 0,
                ];

            $day = $day->addDay();
        } while ($day <= Chronos::now());

        return new JsonResponse([
            'stats' => $days,
            'sentCount' => $messageRepository->getSentMessagesCount($account, $from, $to),
            'openedCount' => $messageRepository->getOpenedMessagesCount($account, $from, $to),
            'byDate' => $index,
        ]);
    }
}
