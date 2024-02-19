<?php

namespace Razinal\Satusehatsync\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Satusehat\Integration\Models\SatusehatLog.
 *
 * @property string|null $response_id
 * @property string $action
 * @property string $url
 * @property array|null $payload
 * @property array $response
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Rsklinik extends Model
{
    public $table;

    public $guarded = [];

    public function __construct(array $attributes = [])
    {
        if (!isset($this->connection)) {
            $this->setConnection(config('satusehatsimrs.database_connection'));
        }

        if (!isset($this->table)) {
            $this->setTable(config('satusehatsimrs.klien_table_name'));
        }

        parent::__construct($attributes);
    }

    protected $primaryKey = 'created_at';

    public $incrementing = false;

    protected $casts = [
        'clientname' => 'string',
        'address' => 'string',
        'orgid_prod' => 'string',
        'clientid_prod' => 'string',
        'clientsecret_prod' => 'string',
    ];


    public function crontasks()
    {
        return $this->hasMany(Crontask::class, 'rsklien_id', 'id');
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }
}
