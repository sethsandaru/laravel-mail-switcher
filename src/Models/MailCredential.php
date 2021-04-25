<?php


namespace SethPhat\MailSwitcher\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SethPhat\MailSwitcher\Database\Factories\MailSwitcherCredentialFactory;

/**
 * Class MailCredential
 * @package SethPhat\MailSwitcher\Models
 * @property int $id
 * @property int $threshold
 * @property int $current_threshold
 * @property string $email
 * @property string $password
 * @property string $server
 * @property string $port
 * @property string $encryption
 * @property int $usageLeft
 */
class MailCredential extends Model
{
    use HasFactory;
    public static ?MailCredential $currentInstance;

    const THRESHOLD_TYPE_DAILY = 'daily';
    const THRESHOLD_TYPE_WEEKLY = 'weekly';
    const THRESHOLD_TYPE_MONTHLY = 'monthly';

    protected $table = "mail_switcher_credentials";
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

    /**
     * Get the available Mail Credential to send out
     *
     * @return MailCredential|null
     */
    public static function getAvailableCredential(): ?MailCredential
    {
        // if cached => prefer to use the cached credental
        if (static::$currentInstance) {
            if (static::$currentInstance->usageLeft === 0) {
                // need to retrieve the new one
                static::$currentInstance = null;
                return static::getAvailableCredential();
            }
        }

        return static::$currentInstance = MailCredential::query()
            ->whereColumn('current_threshold', '<', 'threshold')
            ->orderBy('current_threshold', 'DESC')
            ->first();
    }

    public function scopeType(Builder $query, string $type): Builder
    {
        return $query->where('threshold_type', $type);
    }

    public function getUsageLeftAttribute(): int
    {
        return $this->threshold - $this->current_threshold;
    }

    protected static function newFactory()
    {
        return MailSwitcherCredentialFactory::new();
    }
}
