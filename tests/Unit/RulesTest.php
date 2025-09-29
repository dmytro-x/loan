<?php

namespace Tests\Unit;

use App\Modules\Client\Domain\Entity\Client;
use App\Modules\Client\Infrastructure\EloquentClientModel;
use App\Modules\Credit\Application\DTO\CreditApplicationData;
use App\Modules\Credit\Application\DTO\CreditDecision;
use App\Modules\Credit\Domain\Region\Regions;
use App\Modules\Credit\Domain\Rules\AgeRule;
use App\Modules\Credit\Domain\Rules\IncomeRule;
use App\Modules\Credit\Domain\Rules\OstravaIncreaseRateRule;
use App\Modules\Credit\Domain\Rules\PragueRandomDenyRule;
use App\Modules\Credit\Domain\Rules\RegionRule;
use App\Modules\Credit\Domain\Rules\ScoreRule;
use PHPUnit\Framework\TestCase;

class RulesTest extends TestCase
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

    //score
    public function test_score_rule_passed()
    {
        $client = $this->getStandardClient(['score' => 501]);

        $application = $this->getStandardApplication();

        $decision = new CreditDecision();
        $scoreRule = new ScoreRule();
        $scoreRule->apply($client, $application, $decision);

        $this->assertTrue($decision->approved);
        $this->assertEmpty($decision->rejectionReasons);
    }

    public function test_score_rule_rejected()
    {
        $client = $this->getStandardClient(['score' => 500]);

        $application = $this->getStandardApplication();

        $decision = new CreditDecision();
        $scoreRule = new ScoreRule();
        $scoreRule->apply($client, $application, $decision);

        $this->assertFalse($decision->approved);
        $this->assertContains(ScoreRule::$rejectionMessage, $decision->rejectionReasons);
    }

    //income
    public function test_income_rule_passed()
    {
        $client = $this->getStandardClient(['income' => 1500]);

        $application = $this->getStandardApplication();

        $decision = new CreditDecision();
        $incomeRule = new IncomeRule();
        $incomeRule->apply($client, $application, $decision);

        $this->assertTrue($decision->approved);
        $this->assertEmpty($decision->rejectionReasons);
    }

    public function test_income_rule_rejected()
    {
        $client = $this->getStandardClient(['income' => 999]);

        $application = $this->getStandardApplication();

        $decision = new CreditDecision();
        $incomeRule = new IncomeRule();
        $incomeRule->apply($client, $application, $decision);

        $this->assertFalse($decision->approved);
        $this->assertContains(IncomeRule::$rejectionMessage, $decision->rejectionReasons);
    }

    //age
    public function test_age_rule_passed()
    {
        $client = $this->getStandardClient(['age' => 30]);

        $application = $this->getStandardApplication();

        $decision = new CreditDecision();
        $ageRule = new AgeRule();
        $ageRule->apply($client, $application, $decision);

        $this->assertTrue($decision->approved);
        $this->assertEmpty($decision->rejectionReasons);
    }

    public function test_age_rule_rejected()
    {
        $client = $this->getStandardClient(['age' => 10]);

        $application = $this->getStandardApplication();

        $decision = new CreditDecision();
        $ageRule = new AgeRule();
        $ageRule->apply($client, $application, $decision);

        $this->assertFalse($decision->approved);
        $this->assertContains(AgeRule::$rejectionMessage, $decision->rejectionReasons);
    }


    //region
    public function test_region_rule_passed()
    {
        $client = $this->getStandardClient(['region' => Regions::BRNO]);

        $application = $this->getStandardApplication();

        $decision = new CreditDecision();
        $regionRule = new RegionRule();
        $regionRule->apply($client, $application, $decision);

        $this->assertTrue($decision->approved);
        $this->assertEmpty($decision->rejectionReasons);
    }

    public function test_region_rule_rejected()
    {
        $client = $this->getStandardClient(['region' => 'BQ']);

        $application = $this->getStandardApplication();

        $decision = new CreditDecision();
        $regionRule = new RegionRule();
        $regionRule->apply($client, $application, $decision);

        $this->assertFalse($decision->approved);
        $this->assertContains(RegionRule::$rejectionMessage, $decision->rejectionReasons);
    }

    //OstravaIncreaseRateRule
    public function test_ostrava_rule_increase_rate()
    {
        $client = $this->getStandardClient(['region' => Regions::OSTRAVA]);

        $application = $this->getStandardApplication(['rate' => 10]);
        $decision = new CreditDecision();
        $decision->interestRate = $application->rate;
        $rule = new OstravaIncreaseRateRule();
        $rule->apply($client, $application, $decision);

        $this->assertTrue($decision->approved);
        $this->assertEquals(($application->rate + $rule::$increaseRateValue), $decision->interestRate);
    }

    //PragueRandomDenyRule
    public function test_prague_random_deny_rule_passed()
    {
        $client = $this->getStandardClient(['region' => Regions::PRAGUE]);

        $application = $this->getStandardApplication();

        $decision = new CreditDecision();

        $rule = new PragueRandomDenyRule();
        $rule::$denyRate = 0;
        $rule->apply($client, $application, $decision);

        $this->assertTrue($decision->approved);
    }

    public function test_prague_random_deny_rule_rejected()
    {
        $client = $this->getStandardClient(['region' => Regions::PRAGUE]);

        $application = $this->getStandardApplication();

        $decision = new CreditDecision();

        $rule = new PragueRandomDenyRule();
        $rule::$denyRate = 100;
        $rule->apply($client, $application, $decision);

        $this->assertFalse($decision->approved);
    }

    public function test_prague_random_deny_rule_not_affect_other_region()
    {
        $client = $this->getStandardClient(['region' => Regions::BRNO]);

        $application = $this->getStandardApplication();

        $decision = new CreditDecision();

        $rule = new PragueRandomDenyRule();
        $rule::$denyRate = 100;
        $rule->apply($client, $application, $decision);

        $this->assertTrue($decision->approved);
    }
}
