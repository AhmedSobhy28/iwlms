<?php

namespace App\Models;

use Database\Factories\LessonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    /** @use HasFactory<LessonFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'content',
        'order',
        'course_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return HasMany<LessonCompletion, $this>
     */
    public function completions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class);
    }

    public function isVideoUrl(): bool
    {
        return filter_var($this->content, FILTER_VALIDATE_URL) !== false
            && preg_match('/\.(mp4|webm|ogg|youtube\.com|youtu\.be|vimeo\.com)/i', $this->content);
    }
}
