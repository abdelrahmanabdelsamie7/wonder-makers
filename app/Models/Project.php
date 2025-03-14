<?php
namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Models\ImageProject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    protected $table = 'projects';
    protected $fillable = ['title', 'imageCover'];
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
        static::deleting(function ($project) {
            foreach ($project->images as $image) {
                $imagePath = public_path('uploads/projects/' . basename($image->image));
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
                $image->delete();
            }
        });
    }
    
    public function images()
    {
        return $this->hasMany(ImageProject::class);
    }
}