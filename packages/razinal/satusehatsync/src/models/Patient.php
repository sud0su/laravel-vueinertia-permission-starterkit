<?php

namespace Razinal\Satusehatsync\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Patient extends Model
{
    public function __construct(array $attributes = [])
    {
        if (!isset($this->connection)) {
            $this->setConnection(config('satusehatsimrs.database_connection'));
        }

        if (!isset($this->table)) {
            $this->setTable(config('satusehatsimrs.patient_table_name'));
        }

        parent::__construct($attributes);
    }

    protected $fillable = [
        'resourceType',
        'identifier',
        'active',
        'name',
        'telecom',
        'gender',
        'birthDate',
        'deceasedBoolean',
        'address',
        'maritalStatus',
        'multipleBirthInteger',
        'contact',
        'communication',
        'extension',
        'flag',
        'rsklien_id',
    ];

    protected $casts = [
        'identifier' => 'array',
        'name' => 'array',
        'telecom' => 'array',
        'address' => 'array',
        'maritalStatus' => 'array',
        'contact' => 'array',
        'communication' => 'array',
        'extension' => 'array',
        'rsklien_id' => 'integer',
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

    public function rsklinik()
    {
        return $this->belongsTo(Rsklinik::class);
    }
}
