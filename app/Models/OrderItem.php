<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'course_id', 'session_cohort_id', 'price', 'discount', 'total', 'quantity'];
    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'discount' => 'decimal:2', 'total' => 'decimal:2'];
    }
    public function order() { return $this->belongsTo(Order::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function sessionCohort() { return $this->belongsTo(SessionCohort::class, 'session_cohort_id'); }
}
