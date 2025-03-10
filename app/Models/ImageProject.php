<?php

namespace App\Models;

use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImageProject extends Model
{
    use HasFactory;
    protected $table = 'image_projects';
    protected $fillable = ['project_id', 'image'];
     protected $hidden = ['created_at', 'updated_at'];
    protected $keyType = 'string';
    public $incrementing = false;
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid()->toString();
            }
        });
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}