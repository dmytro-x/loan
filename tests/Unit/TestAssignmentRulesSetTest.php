<?php

namespace Tests\Unit;

use App\Modules\Client\Domain\Entity\Client;
use App\Modules\Client\Infrastructure\EloquentClientModel;
use App\Modules\Credit\Application\DTO\CreditApplicationData;
use App\Modules\Credit\Application\DTO\CreditDecision;
use App\Modules\Credit\Domain\Region\Regions;
use App\Modules\Credit\Domain\RuleSets\TestAssignmentRulesSet;
use PHPUnit\Framework\TestCase;

class TestAssignmentRulesSetTest extends TestCase
{
    public function getStandardClient(array $overrides = []): Client
    {
        return new Client(
            id:     $overrides['id']     ?? '01J8J0R1B4J42N8W7E9F0X2VQG',
            name:   $overrides['name']   ?? 'John Doe',
            age:    $overrides['age']    ?? 30,
            region: $overrides['region'] ?? Regions::BRNO,
            pin:    $overrides['pin']    ?? '123-45-6789',
            email:  $overrides['email']  ?? 'john@example.com',
            phone:  $overrides['phone']  ?? '+420123456789',
            income: $overrides['income'] ?? 2000,
            score:  $overrides['score']  ?? 650,
        );
    }

    public function getStandardApplication(array $overrides = []): CreditApplicationData
    {
        return new CreditApplicationData(
            name:      $overrides['name']      ?? 'Personal Loan',
            amount:    $overrides['amount']    ?? 1000,
            rate: (float)($overrides['rate']   ?? '10%'),
            startDate: $overrides['startDate'] ?? now()->toDateString(),
            endDate:   $overrides['endDate']   ?? now()->addYear()->toDateString(),
        );
    }

    public function test_rules_set_returns_credit_decision_instance()
    {
        $client = $this->getStandardClient();

        $application = $this->getStandardApplication();

        $rulesSet = new TestAssignmentRulesSet();
        $decision = $rulesSet->validate($client, $application);

        $this->assertInstanceOf(CreditDecision::class, $decision);
    }

    public function test_it_applies_all_rules_and_modifies_decision()
    {
        $client = $this->getStandardClient();

        $application = new CreditApplicationData(
            name: 'Personal Loan',
            amount: 1000,
            rate: '10',
            startDate: now()->toDateString(),
            endDate: now()->addYear()->toDateString(),
        );

        $rulesSet = new TestAssignmentRulesSet();
        $decision = $rulesSet->validate($client, $application);

        $this->assertTrue($decision->approved);
        $this->assertEquals('10', $decision->interestRate);
        $this->assertEmpty($decision->rejectionReasons);
    }

    public function test_rules_set_rejects_application_with_multiple_errors(): void
    {
        $client = $this->getStandardClient([
            'age' => 10,
            'region' => 'XX',
            'income' => '500',
            'score' => '100',
        ]);

        $application = $this->getStandardApplication();

        $rulesSet = new TestAssignmentRulesSet();
        $decision = $rulesSet->validate($client, $application);

        $this->assertFalse($decision->approved);
        $this->assertCount(4, $decision->rejectionReasons);
    }
}
