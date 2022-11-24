<?php

namespace SethPhat\MailSwitcher\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use SethPhat\MailSwitcher\Database\Factories\MailSwitcherCredentialFactory;

/**
 * Class MailCredential.
 *
 * @property int    $id
 * @property int    $threshold
 * @property int    $current_threshold
 * @property string $email
 * @property string $password
 * @property string $server
 * @property string $port
 * @property string $encryption
 * @property string $threshold_type
 * @property Carbon $threshold_start
 * @property int    $usageLeft
 */
class MailCredential extends Model
{
    use HasFactory;
    public static ?MailCredential $currentInstance = null;

    public const THRESHOLD_TYPE_DAILY = 'daily';
    public const THRESHOLD_TYPE_WEEKLY = 'weekly';
    public const THRESHOLD_TYPE_MONTHLY = 'monthly';

    protected $table = 'mail_switcher_credentials';
    protected $fillable = [
        'email',
        'password',
        'server',
        'port',
        'encryption',
        'threshold',
        'current_threshold',
        'threshold_type',
    ];

    protected $casts = [
        'threshold_start' => 'datetime',
    ];

    /**
     * Get the available Mail Credential to send out.
     */
    public static function getAvailableCredential(): ?MailCredential
    {
        // if cached => prefer to use the cached credential
        if (!is_null(static::$currentInstance)) {
            // if out of usage
            if (0 === static::$currentInstance->usageLeft) {
                // need to retrieve the new one
                static::$currentInstance = null;

                return static::getAvailableCredential();
            }

            return static::$currentInstance;
        }

        static::$currentInstance = self::available()
            ->orderBy('current_threshold', 'DESC')
            ->first();

        return static::$currentInstance;
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereColumn('current_threshold', '<', 'threshold');
    }

    public function getUsageLeftAttribute(): int
    {
        $this->refresh();

        return $this->threshold - $this->current_threshold;
    }

    public function getIsAvailableToClearThresholdAttribute(): bool
    {
        if ($this->threshold_start) {
            $cNow = now();

            switch ($this->threshold_type) {
                case self::THRESHOLD_TYPE_DAILY:
                    $nextThreshold = $this->threshold_start->addDay();
                    break;

                case self::THRESHOLD_TYPE_WEEKLY:
                    $nextThreshold = $this->threshold_start->addDays(7);
                    break;

                case self::THRESHOLD_TYPE_MONTHLY:
                default:
                    $nextThreshold = $this->threshold_start->addDays(31);
            }

            return $cNow->greaterThanOrEqualTo($nextThreshold);
        }

        return false;
    }

    protected static function newFactory(): MailSwitcherCredentialFactory
    {
        return MailSwitcherCredentialFactory::new();
    }
}
