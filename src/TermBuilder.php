<?php

namespace Swis\Laravel\Fulltext;

class TermBuilder
{
    public static function terms($search)
    {
        $wildcards = config('laravel-fulltext.enable_wildcards');

        // Remove every boolean operator (+, -, > <, ( ), ~, *, ", @distance) from the search query
        // else we will break the MySQL query.
        $search = trim(preg_replace('/[+\-><\(\)~*\"@]+/', ' ', $search));

        
        $splittedWords = collect(preg_split('/[\s,]+/', $search));
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
        $terms = collect($terms)->filter();


        if ($wildcards === true) {
            $terms->transform(function ($term) {
                return $term.'*';
            });
        }

        return $terms;
    }
}
