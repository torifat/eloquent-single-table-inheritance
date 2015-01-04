<?php

namespace Rifat\EloquentSingleTableInheritance;


trait SingleTableInheritanceTrait {

    /**
     * The ClassName where this Trait was invoked
     *
     * @var string
     */
    protected static $_stiBaseClass = __CLASS__;

    /**
     * The Database field for storing the ClassName for STI (Single Table Inheritance)
     *
     * @var string
     */
    protected static $stiClassNameField = 'class_name';

    /**
     * Indicates if the query on Parent Model should return the Children Model results
     *
     * @var bool
     */
    protected static $strictMode = false;

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        if (isset($this->table)) return $this->table;

        if (get_class($this) !== static::$_stiBaseClass) {
            // TODO: Throws an exception if user tries to add a table name to sub-classes
            $tableName =  str_replace('\\', '', snake_case(str_plural(class_basename(get_parent_class($this)))));
        } else {
            $tableName = str_replace('\\', '', snake_case(str_plural(class_basename($this))));
        }
        $this->setTable($tableName);
        return $tableName;
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        $builder = parent::newQuery();
        $className = get_class($this);
        if (static::$strictMode || $className !== static::$_stiBaseClass) {
            $builder->where(static::$stiClassNameField, '=', $className);
        }
        return $builder;
    }

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = array())
    {
        $this->setAttribute(static::$stiClassNameField, get_class($this));
        parent::save($options);
    }

    /**
     * Create a new model instance that is existing.
     *
     * @param  array  $attributes
     * @return static
     */
    public function newFromBuilder($attributes = array())
    {
        $class = $attributes->{static::$stiClassNameField};
        $instance = new $class;
        $instance->exists = true;
        $instance->setRawAttributes((array) $attributes, true);
        return $instance;

    }

}
