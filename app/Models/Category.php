<?php

namespace App\Models;

use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\TaxModule\Entities\Taxable;

class Category extends Model
{
    use HasFactory;
    protected $with = ['translations','storage'];
    protected $casts = [
        'parent_id' => 'integer',
        'position' => 'integer',
        'priority' => 'integer',
        'status' => 'integer',
        'products_count' => 'integer',
        'childes_count' => 'integer',
    ];
    protected $appends = ['image_full_url'];

    public function getImageFullUrlAttribute(){
        $value = $this->image;
        if (count($this->storage) > 0) {
            foreach ($this->storage as $storage) {
                if ($storage['key'] == 'image') {
                    return Helpers::get_full_url('category',$value,$storage['value']);
                }
            }
        }

        return Helpers::get_full_url('category',$value,'public');
    }
    public function storage()
    {
        return $this->morphMany(Storage::class, 'data');
    }

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function childes()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Food::class);
    }

    public function getNameAttribute($value){
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale') ?: 'en';
        $baseLocale = substr(str_replace(['_', '-'], '-', $locale), 0, 2);

        // 1) Tentar a tradução do locale atual (se já estiver carregada via global scope)
        if ($this->relationLoaded('translations') && count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] === 'name' && !empty($translation['value'])) {
                    return $translation['value'];
                }
            }
        }

        // 2) Consultar explicitamente a tradução no locale atual (caso não tenha sido carregada)
        $currentLocaleName = $this->translations()->where('key', 'name')->where('locale', $locale)->value('value');
        if (!empty($currentLocaleName)) {
            return $currentLocaleName;
        }

        // 2.1) Consultar por variações do base locale (ex.: pt e pt-BR)
        $baseLocaleName = $this->translations()->where('key', 'name')->where('locale', 'like', $baseLocale . '%')->value('value');
        if (!empty($baseLocaleName)) {
            return $baseLocaleName;
        }

        // 3) Fallback para o fallback_locale da aplicação (se diferente do atual)
        if (!empty($fallbackLocale) && $fallbackLocale !== $locale) {
            $fallbackLocaleName = $this->translations()->where('key', 'name')->where('locale', $fallbackLocale)->value('value');
            if (!empty($fallbackLocaleName)) {
                return $fallbackLocaleName;
            }
        }

        // 4) Qualquer tradução disponível (qualquer locale)
        $anyTranslation = $this->translations()->where('key', 'name')->value('value');
        if (!empty($anyTranslation)) {
            return $anyTranslation;
        }

        // 5) Valor original do banco, se existir
        if (!empty($value)) {
            return $value;
        }

        // 6) Fallback final: atributo bruto (se disponível)
        return $this->attributes['name'] ?? null;
    }

    protected static function booted()
    {
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }

    protected static function boot()
    {
        parent::boot();
        static::created(function ($category) {
            $category->slug = $category->generateSlug($category->name);
            $category->save();
        });
        static::saved(function ($model) {
            if($model->isDirty('image')){
                $value = Helpers::getDisk();

                DB::table('storages')->updateOrInsert([
                    'data_type' => get_class($model),
                    'data_id' => $model->id,
                    'key' => 'image',
                ], [
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
    private function generateSlug($name)
    {
        $slug = Str::slug($name);
        if ($max_slug = static::where('slug', 'like',"{$slug}%")->latest('id')->value('slug')) {

            if($max_slug == $slug) return "{$slug}-2";

            $max_slug = explode('-',$max_slug);
            $count = array_pop($max_slug);
            if (isset($count) && is_numeric($count)) {
                $max_slug[]= ++$count;
                return implode('-', $max_slug);
            }
        }
        return $slug;
    }

    public function users()
    {
        return $this->morphToMany(User::class ,'visitor_log' );
    }

    public function taxVats()
    {
        return $this->morphMany(Taxable::class, 'taxable');
    }

}
