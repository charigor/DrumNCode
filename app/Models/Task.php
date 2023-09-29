<?php

namespace App\Models;

use App\Enums\TaskStatusEnum;
use App\Http\Services\TaskService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'parent_id',
        'completed_at'
    ];


    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function child(): HasMany
    {
        return $this->hasMany(Task::class,'parent_id','id');
    }

    /**
     * @return HasMany
     */
    public function children() : HasMany
    {
        return $this->child()->with('children');
    }

    public function getNestedField($name = null,$exceptParent = null): array
    {
        $res =  !$exceptParent ? [$name ? $this[$name] : $this->id] : [];
        foreach ($this->children as $child) {
            $res= array_merge($res, $child->getNestedField($name));
        }
        return $res;
    }
    /**
     * @param $query
     * @param $params
     * @return mixed
     */
    public function scopeFilter($query, $params): mixed
    {
       return $query->when(isset($params['status']),fn($query) =>
            $query->where('status',$params['status'])
        )->when(isset($params['title']),fn($query) =>
            $query->whereFullText('tasks.title',$params['title'])
       )->when(isset($params['priorityFrom']),fn($query) =>
            $query->where('priority','>=',$params['priorityFrom'])
       )->when(isset($params['priorityTo']),fn($query) =>
            $query->where('priority','<=',$params['priorityTo'])
       )->when(isset($params['sortBy']),fn($query) =>
            $query->orderBy($params['sortBy'], $params['sortOrder'] ?? 'desc')
       );
    }
    protected $casts = [
        'status' => TaskStatusEnum::class
    ];
}
