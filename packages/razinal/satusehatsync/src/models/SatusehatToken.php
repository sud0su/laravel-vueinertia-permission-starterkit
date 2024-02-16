<?php

namespace Razinal\Satusehatsync\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Satusehat\Integration\Models\SatusehatToken.
 *
 * @property string $environment
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class SatusehatToken extends Model
{
    public $guarded = [];

    public function __construct(array $attributes = [])
    {
        if (!isset($this->connection)) {
            $this->setConnection(config('satusehatsimrs.database_connection'));
        }

        if (!isset($this->table)) {
            $this->setTable(config('satusehatsimrs.token_table_name'));
        }

        parent::__construct($attributes);
    }

    protected $primaryKey = 'token';

    public $incrementing = false;

    protected $casts = ['environment' => 'string', 'token' => 'string'];
}
