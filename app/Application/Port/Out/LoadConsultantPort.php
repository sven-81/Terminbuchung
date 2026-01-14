<?php

declare(strict_types=1);

namespace App\Application\Port\Out;

use App\Domain\Consultant\Consultant;
use App\Domain\Shared\ValueObject\ConsultantId;

interface LoadConsultantPort
{
    public function findById(ConsultantId $id): ?Consultant;
}

