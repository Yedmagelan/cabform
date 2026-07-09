<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'action', 'model_type', 'model_id', 'old_values', 'new_values', 'ip_address', 'user_agent', 'description'];
    protected function casts(): array { return ['old_values' => 'array', 'new_values' => 'array']; }
    public function user() { return $this->belongsTo(User::class); }
    public function subject() { return $this->morphTo('model'); }

    /**
     * Log an action.
     * Supports: log(action, ?user, ?model_type, ?model_id, ?description)
     */
    public static function log(string $action, $userOrModel = null, $modelTypeOrOldValues = null, $modelIdOrNewValues = null, ?string $description = null): self
    {
        // Flexible calling convention:
        // log('action', $user, 'ModelType', $modelId, 'description')
        // log('action', $model, $oldValues, $newValues, 'description')
        $userId = auth()->id();
        $modelType = null;
        $modelId = null;
        $oldValues = null;
        $newValues = null;

        if ($userOrModel instanceof User) {
            $userId = $userOrModel->id;
            $modelType = is_string($modelTypeOrOldValues) ? $modelTypeOrOldValues : null;
            $modelId = is_int($modelIdOrNewValues) || is_string($modelIdOrNewValues) ? $modelIdOrNewValues : null;
        } elseif ($userOrModel instanceof Model) {
            $modelType = get_class($userOrModel);
            $modelId = $userOrModel->id;
            $oldValues = is_array($modelTypeOrOldValues) ? $modelTypeOrOldValues : null;
            $newValues = is_array($modelIdOrNewValues) ? $modelIdOrNewValues : null;
        }

        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description,
        ]);
    }
}
