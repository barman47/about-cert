<?php
namespace App\Repositories;

use App\Exceptions\UserRepositoryException;
use App\Handlers\Alerts\FollowUserHandler;
use App\Handlers\Alerts\UnfollowUserHandler;
use App\Repositories\AlertRepository;
use App\User;

class UserRepository
{
    private $user;

    public function user(User $user): UserRepository
    {
        $this->user = $user;
        return $this;
    } //end method user

    public function search(string $seed, int $perpage = 10, int $page = 1)
    {
        return User::search($seed)->query(function ($query) {
            $query->select("id", "name", "username", "email", "thumbnail", "display_photo");
        })->paginate($perpage, $page);
    } //return method search

    public function follow(User $user): UserRepository
    {
        if (!$this->isFollowing($user)) {
            $this->user->following()->attach($user->id);

            $alertRepository = new AlertRepository();

            $alertRepository
                ->sender($this->user)
                ->receiver($user)
                ->data($user)
                ->handler(new FollowUserHandler())
                ->create();
        }
        return $this;
    } //end method follow

    public function unfollow(User $user): UserRepository
    {
        if ($this->isFollowing($user)) {
            $this->user->following()->detach($user->id);

            $alertRepository = new AlertRepository();

            $alertRepository
                ->sender($this->user)
                ->receiver($user)
                ->data($user)
                ->handler(new UnfollowUserHandler())
                ->create();
        }
        return $this;
    } //end method unfollow

    public function isFollowing(User $user): bool
    {
        return !($this->user->following()->wherePivot("host", $user->id)->first() == null);
    } //end method isFollowing

    public function isFollowedBy(User $user): bool
    {
        return !($this->user->followers()->wherePivot("parasite", $user->id)->first() == null);
    } //end method isFollowedBy

    public function getUser($id)
    {
        $user = User::where("id", $id)->select(
            "id",
            "name",
            "email",
            "display_photo",
            "cover_image",
            "entity_type",
            "entity_id",
            "thumbnail"
        )->withCount(["followers", "following"])->first();

        if ($user == null) {
            throw new UserRepositoryException("User does not exist", 404);
        }

        $this->user($user);
        $user->is_following_user = $this->isFollowedBy(auth()->user()) ? 1 : 0;

        $user->entity->makeHidden(["id", "created_at", "updated_at"]);
        $user = array_merge($user->toArray(), $user->entity->toArray());

        unset($user["entity"]);
        unset($user["entity_id"]);
        unset($user["entity_type"]);


        return $user;
    } //end method getUser

    public function getProfileData(bool $isAuthenticatedUser)
    {
        if (!$this->user) {
            throw new UserRepositoryException("User must be specified", 404);
        }

        $user = $this->user;

        $extractForOnlyAuthenticated = [
            "certificates" => function ($query) {$query->oldest();},
        ];

        $extractForAll = [
            "workExperiences" => function ($query) {$query->oldest();},
            "interests" => function ($query) {$query->oldest();},
            "SocialAccounts" => function ($query) {$query->oldest();},
            "academicDetails" => function ($query) {$query->oldest();},
            "hobbies" => function ($query) {$query->oldest();},
            "languages" => function ($query) {$query->oldest();},
            "skills" => function ($query) {$query->oldest();},
            "interests" => function ($query) {$query->oldest();},
            "projects" => function ($query) {$query->oldest();},
        ];

        if ($isAuthenticatedUser) {
            $extractForAll = array_merge($extractForAll, $extractForOnlyAuthenticated);
        }

        $user = User::where("id", $user->id)
            ->with($extractForAll)
            ->withCount(["followers", "following"])
            ->first();

        $data = [
            "social_accounts" => $user->socialAccounts,
            "academic_details" => $user->academicDetails,
            "hobbies" => $user->hobbies,
            "languages" => $user->languages,
            "skills" => $user->skills,
            "projects" => $user->projects,
            "followers_count" => $user->followers_count,
            "following_count" => $user->following_count,
            "work_experiences" => $user->workExperiences,
            "interests" => $user->interests,
            "job_title" => $user->job_title ?? "",
        ];

        if ($isAuthenticatedUser) {
            $data = array_merge([
                "certificates" => $user->certificates,
            ], $data);
        }

        return $data;
    } //end method getProfileData
} //end class UserRepository
