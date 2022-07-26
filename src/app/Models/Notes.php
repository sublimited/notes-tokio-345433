<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JMS\Serializer\Annotation as Serializer;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notes extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'body',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];


    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {

    }

    /**
     * @Serializer\SerializedName("id")
     * @Serializer\Type("integer")
     * @Serializer\Groups({"api", "default"})
     * @Serializer\VirtualProperty()
     * @Serializer\Expose()
     * @return mixed
     * @var string
     */
    public function getId()
    {
        return $this::__get('id');
    }

    /**
     * @Serializer\SerializedName("name")
     * @Serializer\Type("string")
     * @Serializer\Groups({"api", "default"})
     * @Serializer\VirtualProperty()
     * @Serializer\Expose()
     * @return mixed
     * @var string
     */
    public function getName()
    {
        return $this::__get('name');
    }

    /**
     * @Serializer\SerializedName("body")
     * @Serializer\Type("string")
     * @Serializer\Groups({"api", "default"})
     * @Serializer\VirtualProperty()
     * @Serializer\Expose()
     * @return mixed
     * @var string
     */
    public function getBody()
    {
        return $this::__get('body');
    }

    /**
     * @Serializer\SerializedName("created_at")
     * @Serializer\Type("string")
     * @Serializer\Groups({"api", "default"})
     * @Serializer\VirtualProperty()
     * @Serializer\Expose()
     * @return mixed
     * @var string
     */
    public function getCreatedAt()
    {
        return $this::__get('created_at');
    }



      /**
     * @Serializer\SerializedName("tags")
     * @Serializer\Type("array")
     * @Serializer\Groups({"api", "default"})
     * @Serializer\VirtualProperty
     * @Serializer\Expose
     *
     * @return mixed
     *
     * @var string
     */
    public function getTags()
    {
        $tags = $this->tags()->get()->toArray();
        if(!empty($tags))
        {
            foreach($tags as $tagKey=>$tag)
            {
                unset($tags[$tagKey]['pivot']);
            }
        }
        return $tags;
    }

     /**
     * @Serializer\SerializedName("notes_by_tag")
     * @Serializer\Type("array")
     * @Serializer\Groups({"api", "default"})
     * @Serializer\VirtualProperty
     * @Serializer\Expose
     *
     * @return mixed
     *
     * @var string
     */
    public function getNotesByTag($id=null)
    {
        if(!$id) return [];
        $notes = Notes::with('tags')->whereHas('tags', function($query) use ($id){
            $query->where('id',$id);
       })->get()->toArray();
       
       if(!empty($notes))
       {
        foreach($notes as $noteKey => $note)
        {
            foreach($note['tags'] as $tagKey => $tag)
            {
                unset($notes[$noteKey]['tags'][$tagKey]['pivot']);
            }
        }
       }

       return $notes;
       
    }

    
    public function tags()
    {
        return $this->morphToMany(Tags::class,'taggable');
    }

}
