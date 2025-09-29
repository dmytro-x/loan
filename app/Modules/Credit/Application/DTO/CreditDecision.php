<?php
namespace App\Modules\Credit\Application\DTO;

class CreditDecision
{
    public bool $approved = true;
    public ?float $interestRate = null;
    public array $rejectionReasons = [];
    public array $notes = [];

    public function reject(string $reason): void
    {
        $this->approved = false;
        $this->rejectionReasons[] = $reason;
    }

    public function increaseInterestRate(float $rate): void
    {
        $this->interestRate += $rate;
    }

    public function reduceInterestRate(float $rate): void
    {
        $this->interestRate -= $rate;

        if ($this->interestRate < 0) {
            $this->interestRate = 0;
        }
    }

    public function setInterestRate(float $rate): void
    {
        $this->interestRate = $this->interestRate ? max($this->interestRate, $rate) : $rate;
    }

    public function addNote(string $note): void
    {
        $this->notes[] = $note;
    }
}
