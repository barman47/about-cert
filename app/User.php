<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Traits\UsesUuid;
use App\Traits\MultipleDocumentable;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;

use App\Interfaces\CanReceiverAlert;
use App\Interfaces\CanSendAlert;

class User extends Authenticatable implements CanReceiverAlert, CanSendAlert
{
    use Notifiable, UsesUuid, HasApiTokens, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function toSearchableArray()
    {
        // $array = [
        //     "username" => $this->username,
        //     "name" => $this->name,
        //     "email" => $this->email,
        //     "id" => $this->id,
        //     "thumbnail" => $this->thumbnail,
        //     "display_photo" => $this->display_photo
        // ];

        $array = $this->toArray();

        return $array;
    }//end method toSearchableArray

    public function getScoutKey()
    {
        return $this->email;
    }

    public function getFirstNameAttribute(){
        $names = explode(" ", preg_replace('/\s+/i', " ", $this->name));

        $firstName = count($names) > 1 ? $names[1] :  "";
        return $firstName;
    }//end method getFirstNameAttribute

    public function getLastNameAttribute(){
        $names = explode(" ", preg_replace('/\s+/i', " ", $this->name));

        $lastName = $names[0];
        return $lastName;
    }//end method getLastNameAttribute

    public function getOtherNameAttribute(){
        $names = explode(" ", preg_replace('/\s+/i', " ", $this->name));

        return count($names) > 2 ? $names[2] :  "";
    }//end method getLastNameAttribute

    public function entity(){
        return $this->morphTo();
    }//end method entity

    public function certificates(){
        return $this->hasMany(Certificate::class);
    }//end method certificates

    public function academicDetails(){
        return $this->hasMany(AcademicDetail::class);
    }//end method academicDetails

    public function hobbies(){
        return $this->hasMany(Hobby::class);
    }//end method hobbies

    public function following(){
        return $this->belongsToMany(static::class, "parasites", "parasite", "host")
                    ->as("following_pivot")
                    ->using(Parasite::class)
                    ->withTimestamps();
    }//end method following

    public function followers(){
        return $this->belongsToMany(static::class, "parasites", "host", "parasite")
                    ->as("followers_pivot")
                    ->using(Parasite::class)
                    ->withTimestamps();
    }//end method followers

    public function opportunities(){
        return $this->hasMany(Opportunity::class);
    }//end method opportunities

    public function events(){
        return $this->hasMany(Event::class);
    }//end method events

    public function languages(){
        return $this->belongsToMany(Language::class, "user_languages", "user_id", "language_id")
                    ->using(UserLanguage::class)
                    ->withTimestamps()
                    ->withPivot('proficiency');
    }//end method languages

    public function skills(){
        return $this->hasMany(Skill::class);
    }//end method skills

    public function posts(){
        return $this->hasMany(Post::class);
    }//end method posts

    public function documents(){
        return $this->hasMany(Document::class);
    }//end method posts

    public function rootDocuments() {
        return $this->hasMany(Document::class)->where("documentable_type", DocumentType::ROOT);
    }//end method rootDocuments

    public function workExperiences(){
        return $this->hasMany(WorkExperience::class);
    }//end method workExperiences

    public function socialAccounts(){
        return $this->hasMany(UserSocialAccount::class);
    }//end method socialAccounts

    public function interests(){
        return $this->belongsToMany(Interest::class, "user_interests", "user_id", "interest_id")
                    ->using(UserInterest::class)
                    ->withTimestamps();
    }//end method interests

    public function projects(){
        return $this->hasMany(Project::class);
    }//end method projects

    public function comments(){
        return $this->hasMany(Comment::class);
    }//end method comments

    public function watching(){
        return $this->hasMany(Watch::class);
    }//end method watching

    public function likes(){
        return $this->hasMany(Like::class);
    }//end method likes

    public function signatures(){
        return $this->hasMany(Signature::class);
    }//end method signatures

    public function cVs(){
        return $this->belongsToMany(CVTemplate::class, "user_c_v_s", "user_id", "c_v_template_id")
                    ->using(UserCV::class)
                    ->withPivot("src")
                    ->withTimestamps();
    }//end method cVs

    public function priviledges(){
        return $this->belongsToMany(Priviledge::class, "user_priviledges", "user_id", "priviledge_id")
                    ->using(UserPriviledge::class)
                    ->withTimestamps()
                    ->withPivot([
                        "meta",
                        "target_id"
                    ]);
    }//end method priviledges

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }//end method transactions

    public function alerts(){
        return $this->morphMany(Alert::class, "receiver");
    }//end method alerts

    public function signatureSendMarkers(){
        return $this->hasMany(SignatureSendMarker::class);
    }//end method signatureSendMarkers

    public function signatureReceiveMarkers(){
        return $this->hasMany(SignatureReceiveMarker::class, "receiver_id");
    }//end method signatureReceiveMarkers

    public function adminRequests(){
        return $this->hasMany(AdminRequest::class);
    }//end method adminRequests

    public function liveEvents(){
        return $this->hasMany(liveEvent::class);
    }//end method liveEvents
}//end class user
