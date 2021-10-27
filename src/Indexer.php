<?php

namespace Swis\Laravel\Fulltext;

use Illuminate\Database\Eloquent\Model;

class Indexer
{
    public function unIndexOneByClass($class, $id)
    {
        $record = IndexedRecord::where('indexable_id', $id)->where('indexable_type', $class);
        if ($record->exists)
        {
            $record->delete();
        }
    }

    public function indexOneByClass($class, $id)
    {
        $model = call_user_func([$class, 'find'], $id);
        if (in_array(Indexable::class, class_uses($model), true))
        {
            $this->indexModel($model);
        }
    }

    public function indexModel(Model $model)
    {
        $model->indexRecord();
    }

    public function indexAllByClass($class)
    {
        $model = new $class;
        $self  = $this;
        if (in_array(Indexable::class, class_uses($model), true))
        {
            if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model)))
            {
                $model->withTrashed()->chunk(100, function($chunk) use ($self) {
                    foreach ($chunk as $modelRecord)
                    {
                        $self->indexModel($modelRecord);
                    }
                });
            }
            else
            {
                $model->chunk(100, function($chunk) use ($self) {
                    foreach ($chunk as $modelRecord)
                    {
                        $self->indexModel($modelRecord);
                    }
                });
            }
        }
    }
}
