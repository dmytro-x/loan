<?php

namespace App\Modules\Credit\Domain\Repository;

use App\Modules\Credit\Domain\Entity\Credit;

interface CreditRepository
{
    public function save(Credit $credit): void;
}
