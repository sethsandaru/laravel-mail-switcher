<?php


namespace SethPhat\MailSwitcher\Tests\Unit\Models;


use Carbon\Carbon;
use SethPhat\MailSwitcher\Models\MailCredential;
use SethPhat\MailSwitcher\Tests\TestCase;

class MailCredentialTest extends TestCase
{
    public function testAddNewCredentialSuccessfully()
    {
        $credential = MailCredential::factory()->create();

        $this->assertDatabaseHas('mail_switcher_credentials', [
            'id' => $credential->id,
            'email' => $credential->email,
            'password' => $credential->password,
            'port' => $credential->port,
            'server' => $credential->server,
        ]);
    }

    public function testGetAvailableCredentialSuccessfullyAndFullyCached()
    {
        $credential = MailCredential::factory()->create();

        // from query
        $availableCredential = MailCredential::getAvailableCredential();

        $this->assertNotNull($availableCredential);
        $this->assertSame($credential->id, $availableCredential->id);
        $this->assertTrue($credential->is($availableCredential));

        // from cache
        $cachedCredential = MailCredential::getAvailableCredential();

        $this->assertNotNull($availableCredential);
        $this->assertSame($credential->id, $cachedCredential->id);

        $this->assertTrue($credential->is($cachedCredential));
    }

    public function testGetAvailableCredentialCacheDataOutOfUsageAutoSwitchToNewCredential()
    {
        $credential = MailCredential::factory()->create();

        // from query
        $availableCredential = MailCredential::getAvailableCredential();

        $this->assertNotNull($availableCredential);
        $this->assertSame($credential->id, $availableCredential->id);
        $this->assertTrue($credential->is($availableCredential));

        // set to out of threshold
        $credential->current_threshold = $credential->threshold;
        $credential->save();

        // create new credential
        MailCredential::factory()->create();

        $newAvailableCredential = MailCredential::getAvailableCredential();

        $this->assertNotNull($newAvailableCredential);
        $this->assertNotSame($availableCredential->id, $newAvailableCredential->id);
        $this->assertEquals(0, $newAvailableCredential->current_threshold);
        $this->assertFalse($availableCredential->is($newAvailableCredential));
    }

    public function testUsageLeftAttribute() {
        $credential = MailCredential::factory()->create();

        $timesLeft = $credential->threshold - $credential->current_threshold;
        $this->assertEquals($timesLeft, $credential->usageLeft);

        $credential->update([
            'threshold' => 50,
            'current_threshold' => 50,
        ]);

        $this->assertEquals(0, $credential->usageLeft);
    }

    public function testTypeOfThresholdIsCarbonInstance()
    {
        $credential = MailCredential::factory()->create([
            'threshold_type' => MailCredential::THRESHOLD_TYPE_DAILY,
            'threshold_start' => now()
        ]);

        $credential->refresh();

        $this->assertNotNull($credential->threshold_start);
        $this->assertInstanceOf(Carbon::class, $credential->threshold_start);
    }

    /**
     * @covers \SethPhat\MailSwitcher\Models\MailCredential::getIsAvailableToClearThresholdAttribute
     */
    public function testGetAvailableCredentialThresholdToClearDaily()
    {
        $credential = MailCredential::factory()->create([
            'threshold_type' => MailCredential::THRESHOLD_TYPE_DAILY,
            'threshold_start' => now()
        ]);

        Carbon::setTestNow(now()->addDay()->addSecond());

        $this->assertTrue($credential->isAvailableToClearThreshold);
    }

    /**
     * @covers \SethPhat\MailSwitcher\Models\MailCredential::getIsAvailableToClearThresholdAttribute
     */
    public function testGetAvailableCredentialThresholdToClearWeekly()
    {
        $credential = MailCredential::factory()->create([
            'threshold_type' => MailCredential::THRESHOLD_TYPE_WEEKLY,
            'threshold_start' => now()
        ]);

        Carbon::setTestNow(now()->addDays(7));

        $this->assertTrue($credential->isAvailableToClearThreshold);
    }

    /**
     * @covers \SethPhat\MailSwitcher\Models\MailCredential::getIsAvailableToClearThresholdAttribute
     */
    public function testGetAvailableCredentialThresholdToClearMonthly()
    {
        $credential = MailCredential::factory()->create([
            'threshold_type' => MailCredential::THRESHOLD_TYPE_MONTHLY,
            'threshold_start' => now()
        ]);

        Carbon::setTestNow(now()->addDays(31));

        $this->assertTrue($credential->isAvailableToClearThreshold);
    }

    /**
     * @covers \SethPhat\MailSwitcher\Models\MailCredential::getIsAvailableToClearThresholdAttribute
     */
    public function testGetAvailableCredentialThresholdToClearFalseBecauseHasNotStartedYet()
    {
        $credential = MailCredential::factory()->create([
            'threshold_type' => MailCredential::THRESHOLD_TYPE_DAILY,
            'threshold_start' => null
        ]);

        $this->assertFalse($credential->isAvailableToClearThreshold);
    }
}
