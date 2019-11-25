<?php

namespace MichielKempen\NovaOrderField;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\EloquentSortable\Sortable;

trait OrderablePivot
{
    /**
     * Build an "index" query for the given related resource.
     *
     * @param  NovaRequest  $request
     * @param  Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $pivot
     * @return Builder
     */
    public static function orderedPivotIndexQuery(NovaRequest $request, $query, $pivot)
    {
        $attribute = static::modelOrderByFieldAttribute($pivot);

        if(!$attribute) {
            return $query;
        }

        $query->orderBy($pivot->qualifyColumn($attribute));

        return $query;
    }

    /**
     * Get the requested resource relationship
     *
     * @param  NovaRequest  $request
     * @return null|\Illuminate\Database\Eloquent\Model
     */
    protected static function orderedManyPivotModel(NovaRequest $request)
    {
        if(!$request->viaRelationship()) {
            return;
        }

        $resource = $request->viaResource();

        $relationship = $resource::newModel()->{$request->viaRelationship}();

        if(!$relationship || !$relationship->getPivotClass()) {
            return;
        }

        $pivot = $relationship->getPivotClass();

        if(!($model = new $pivot) instanceof Sortable) {
            return;
        }

        return $model;
    }
}