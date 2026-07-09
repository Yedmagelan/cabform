<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;

class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('courses.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('courses.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        // An instructor can only edit their own course, admins can edit any
        return $user->hasPermissionTo('courses.edit') && ($user->isAdmin() || $user->id === $course->instructor_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->hasPermissionTo('courses.delete') && ($user->isAdmin() || $user->id === $course->instructor_id);
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, Course $course): bool
    {
        return $user->hasPermissionTo('courses.publish');
    }
}
