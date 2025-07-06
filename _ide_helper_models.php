<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property int $display_order
 * @property string|null $icon_path
 * @property string|null $share_token
 * @property string|null $public_share_token
 * @property int $is_public_shared
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CategoryUserShare $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $sharedUsers
 * @property-read int|null $shared_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereIconPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereIsPublicShared($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category wherePublicShareToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereShareToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUserId($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $category_id
 * @property int $shared_user_id
 * @property string $permission
 * @property string|null $confirmation_token
 * @property bool $is_confirmed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 * @property-read \App\Models\User $sharedUser
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryUserShare newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryUserShare newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryUserShare query()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryUserShare whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryUserShare whereConfirmationToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryUserShare whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryUserShare whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryUserShare whereIsConfirmed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryUserShare wherePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryUserShare whereSharedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryUserShare whereUpdatedAt($value)
 */
	class CategoryUserShare extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShareGroupMember> $members
 * @property-read int|null $members_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ShareGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShareGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShareGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShareGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareGroup whereUserId($value)
 */
	class ShareGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $share_group_id
 * @property int|null $user_id
 * @property string $identifier
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ShareGroup $group
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ShareGroupMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShareGroupMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShareGroupMember query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShareGroupMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareGroupMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareGroupMember whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareGroupMember whereShareGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareGroupMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareGroupMember whereUserId($value)
 */
	class ShareGroupMember extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $category_id
 * @property string $token
 * @property int $can_edit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SharedLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SharedLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SharedLink query()
 * @method static \Illuminate\Database\Eloquent\Builder|SharedLink whereCanEdit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SharedLink whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SharedLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SharedLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SharedLink whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SharedLink whereUpdatedAt($value)
 */
	class SharedLink extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $category_id
 * @property string $title
 * @property string|null $due_date
 * @property string|null $note
 * @property string|null $google_event_id
 * @property int $is_done
 * @property int $used_ai_advisor
 * @property string|null $ai_advice
 * @property string|null $done_comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 * @method static \Illuminate\Database\Eloquent\Builder|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereAiAdvice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDoneComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereGoogleEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereIsDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUsedAiAdvisor($value)
 */
	class Task extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $account_id
 * @property string|null $provider
 * @property string|null $provider_id
 * @property string|null $google_token
 * @property string|null $google_refresh_token
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property int $ai_advisor_used_today
 * @property int $ai_advisor_limit_per_day
 * @property string|null $ai_advisor_last_used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $button_layout
 * @property string|null $google_calendar_color
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $sharedCategories
 * @property-read int|null $shared_categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAiAdvisorLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAiAdvisorLimitPerDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAiAdvisorUsedToday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereButtonLayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGoogleCalendarColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGoogleRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGoogleToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

