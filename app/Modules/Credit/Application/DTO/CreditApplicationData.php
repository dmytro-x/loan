<?php

namespace App\Modules\Credit\Application\DTO;

class CreditApplicationData
{
    public \DateTimeImmutable $startDate;
    public \DateTimeImmutable $endDate;

    public function __construct(
        public string $name,
        public float $amount,
        public float $rate,
        mixed $startDate,
        mixed $endDate
    ) {
        $this->startDate = $this->normalizeDate($startDate);
        $this->endDate   = $this->normalizeDate($endDate);
    }

    private function normalizeDate(mixed $value): \DateTimeImmutable
    {
        return match (true) {
            $value instanceof \DateTimeImmutable => $value,
            $value instanceof \DateTime          => \DateTimeImmutable::createFromMutable($value),
            is_string($value)                    => new \DateTimeImmutable($value),
            default                              => throw new \InvalidArgumentException(
                'Invalid date value: ' . gettype($value)
            ),
        };
    }

    public static function fromArray(array $data): self
    {
        $rate = is_string($data['rate'])
            ? (float) str_replace('%', '', $data['rate'])
            : (float) $data['rate'];

        return new self(
            name: $data['name'],
            amount: (float) $data['amount'],
            rate: $rate,
            startDate: $data['start_date'],
            endDate: $data['end_date'],
        );
    }

    public function toArray(): array
    {
        return [
            'name'       => $this->name,
            'amount'     => $this->amount,
            'rate'       => $this->rate,
            'start_date' => $this->startDate->format('Y-m-d'),
            'end_date'   => $this->endDate->format('Y-m-d'),
        ];
    }
}
