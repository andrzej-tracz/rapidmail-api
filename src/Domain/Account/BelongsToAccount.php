<?php

namespace App\Domain\Account;

interface BelongsToAccount
{
    /**
     * @return Account
     */
    public function getAccount(): ?Account;

    /**
     * @param Account $account
     *
     * @return mixed
     */
    public function setAccount(?Account $account);
}
