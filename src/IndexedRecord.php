<?php

namespace Swis\Laravel\Fulltext;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IndexedRecord extends Model
{
    protected $table = 'laravel_fulltext';

    public function __construct(array $attributes = [])
    {
        $this->connection = config('laravel-fulltext.db_connection');

        parent::__construct($attributes);
    }

    public function indexable()
    {
        return $this->morphTo()->withoutGlobalScope(SoftDeletingScope::class)->withoutGlobalScope('onlyPaid');
    }

    public function updateIndex()
    {
        $this->setAttribute('indexed_title', $this->indexable->getIndexTitle());
        $this->setAttribute('indexed_content', $this->indexable->getIndexContent());
        $this->save();
    }
}
