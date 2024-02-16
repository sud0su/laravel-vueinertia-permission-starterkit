<?php

namespace Razinal\Satusehatsync\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Getdatasync extends Model
{
    public function __construct(array $attributes = [])
    {
        if (!isset($this->connection)) {
            $this->setConnection(config('satusehatsimrs.database_connection'));
        }

        if (!isset($this->table)) {
            $this->setTable(config('satusehatsimrs.getdata_table_name'));
        }

        parent::__construct($attributes);
    }

    protected $fillable = [
        'resourceType',
        'data',
        'cron_id',
        'identifier',
        'flag'
    ];

    protected $casts = [
        'data' => 'array',
        'cron_id' => 'integer',
        'identifier' => 'string',
        'flag' => 'integer',
    ];

    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])
            ->translatedFormat('l, d-m-Y H:i');
    }

    public function getUpdatedAtAttribute()
    {
        return Carbon::parse($this->attributes['updated_at'])
            ->translatedFormat('l, d-m-Y H:i');
    }

    public function crontask()
    {
        return $this->belongsTo(Crontask::class);
    }
}
