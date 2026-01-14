<?php

declare(strict_types=1);

namespace App\Adapter\Out\Persistence;

use App\Domain\Consultant\Consultant;
use App\Domain\Consultant\ValueObject\DailyCapacity;
use App\Domain\Shared\ValueObject\ConsultantId;
use App\Domain\Shared\ValueObject\Email;
use App\Application\Port\Out\LoadConsultantPort;
use Illuminate\Support\Facades\DB;

final readonly class ConsultantRepository implements LoadConsultantPort
{
    public function findById(ConsultantId $id): ?Consultant
    {
        $row = DB::table('consultants')
            ->where('id', $id->toString())
            ->first();

        if ($row === null) {
            return null;
        }

        return Consultant::create(
            ConsultantId::fromString($row->id),
            $row->name,
            Email::fromString($row->email),
            DailyCapacity::fromMinutes($row->daily_capacity_minutes)
        );
    }
}

