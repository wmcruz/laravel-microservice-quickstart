<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BasicCrudController;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends BasicCrudController {

    private $rules;

    public function __construct() {
        $this->rules = [
            'title' => 'required|max:255',
            'description' => 'required',
            'year_launched' => 'required|date_format:Y',
            'opened' => 'boolean',
            'rating' => 'required|in:' . implode(',', Video::RATING_LIST),
            'duration' => 'required|integer',
            'categories_id' => 'required|array|exists:categories,id',
            'genres_id' => 'required|array|exists:genres,id'
        ];
    }

    public function store(Request $request) {
        $validateData = $this->validate($request, $this->rulesStore());
        $obj = $this->model()::create($validateData);
        $obj->categories()->sync($request->get('categories_id'));
        $obj->genres()->sync($request->get('genres_id'));
        $obj->refresh();

        return $obj;
    }

    public function update(Request $request, $id) {
        $obj = $this->findOrFail($id);
        $validateDate = $this->validate($request, $this->rulesUpdate());
        $obj->update($validateDate);
        $obj->categories()->sync($request->get('categories_id'));
        $obj->genres()->sync($request->get('genres_id'));

        return $obj;
    }

    protected function model() {
        return Video::class;
    }

    protected function rulesStore() {
        return $this->rules;
    }

    protected function rulesUpdate() {
        return $this->rules;
    }
}
