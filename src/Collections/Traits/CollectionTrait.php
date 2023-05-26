<?php

declare(strict_types=1);

namespace builder\Database\Collections\Traits;

use builder\Database\Collections\CollectionMapper;

/**
 * @property-read array $proxies_compact
 * @property-read array $proxies
 * @property-read mixed $instance
 * @property-read mixed $pagination
 * @property-read bool $is_paginate
 */
trait CollectionTrait{

    /**
     * Check if is object without array
     *
     * @var bool
     */
    protected $unescapeIsObjectWithoutArray = false;

    /**
     * Check Proxies Type
     * Check type of Database Method request
     *
     * @return bool
     */
    static protected function checkProxiesType()
    {
        // get Trace
        self::getTrace();
        
        // if in first or insert proxies
        if(in_array(self::$instance, self::$proxies['first']) || in_array(self::$instance, self::$proxies['insert'])){
            return true;
        }
        
        return false;
    }
    
    /**
     * Get Instance of Database Fetch Method
     *
     * @return bool
     */
    static protected function getTrace() 
    {
        // get Trace
        $getTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        // instance functions
        $functions = array_map('strtolower', array_column($getTrace, 'function'));
        
        // get array interests
        $interest = array_intersect(self::$proxies_compact, $functions);

        // reset keys
        if(is_array($interest) && count($interest) > 0){
            $interest = array_values($interest);
        }
        
        // instance of DB fetch request
        self::$instance = $interest[0] ?? null;

        // instance of DB Paginate request
        self::$is_paginate = in_array(self::$instance, self::$proxies['paginate']);
    }

    /**
     * Convert arrays into instance of Collection Mappers
     *
     * @param  mixed  $items
     * 
     * @return array
     */
    protected function wrapArrayIntoCollectionMappers(mixed $items)
    {
        // check if valid array data
        if (is_array($items) && count($items) > 0) {
            return array_map(function ($item, $key){
                return new CollectionMapper($item, $key);
            }, $items, array_keys($items));
        }

        return $items;
    }

    /**
     * Results array of items from Collection or Arrayable.
     *
     * @param  mixed  $items
     * 
     * @return array
     */
    protected function getArrayItems($items)
    {
        // first or insert request
        if ($this->unescapeIsObjectWithoutArray || !is_array($items)) {
            return  $this->convertOnInit($items);
        }

        return $items;
    }

}