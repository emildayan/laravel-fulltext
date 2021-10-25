<?php

namespace Swis\Laravel\Fulltext;

/**
 * @property \Swis\Laravel\Fulltext\IndexedRecord|null $indexedRecord
 */
trait Indexable
{
    /**
     * Boot the trait.
     */
    public static function bootIndexable()
    {
        static::observe(new ModelObserver());
    }

    public function getIndexContent()
    {
        return $this->getIndexDataFromColumns($this->indexContentColumns);
    }

    public function getIndexTitle()
    {
        return $this->getIndexDataFromColumns($this->indexTitleColumns);
    }

    public function indexedRecord()
    {
        return $this->morphOne(IndexedRecord::class, 'indexable');
    }

    public function indexRecord()
    {
        if (null === $this->indexedRecord) {
            $this->indexedRecord = new IndexedRecord();
            $this->indexedRecord->indexable()->associate($this);
        }
        $this->indexedRecord->updateIndex();
    }

    public function unIndexRecord()
    {
        if (null !== $this->indexedRecord) {
            $this->indexedRecord->delete();
        }
    }

    protected function getIndexDataFromColumns($columns)
    {
        $indexData = [];
        foreach ($columns as $column) {
            $indexValue = data_get($this, $column);

            if (is_array($indexValue)) {
                $indexValue = implode(' ', array_filter($indexValue));
            }

            $indexData[] = $indexValue;
        }

       
        $splittedWords = collect(preg_split('/[\s,]+/', implode(' ', array_filter($indexData))));
        $terms = [];
        foreach ($splittedWords as $index => $word)
        {
            if($index == 0) { $terms[] = $word; continue; }
            if(strlen($word) < 4) {
                $terms[$index - 1].= ''.$word;
                $terms[] = '';
                continue;
            }
            $terms[] = $word;
        }

        return implode(' ', array_filter($terms));
    }
}
