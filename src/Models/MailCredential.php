<?php


namespace SethPhat\MailSwitcher\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
 */
class MailCredential extends Model
{
    use HasFactory;
    public static MailCredential $currentInstance;

    protected $table = "mail_switcher_credentials";
    protected $fillable = [
        'email',
        'password',
        'server',
        'port',
        'encryption',
        'threshold',
    ];

    public static function getAvailableCredential(): ?MailCredential
    {
        return MailCredential::query()
            ->whereColumn('current_threshold', '<', 'threshold')
            ->orderBy('current_threshold', 'DESC')
            ->first();
    }

    public function getUsageLeftAttribute(): int
    {
        return $this->threshold - $this->current_threshold;
    }
}
