<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class BasicCrudController extends Controller {

    protected abstract function model();
    protected abstract function rulesStore();
    protected abstract function rulesUpdate();

    public function index() {
        return $this->model()::all();
    }

    public function store(Request $request) {
        $validateDate = $this->validate($request, $this->rulesStore());
        $obj = $this->model()::create($validateDate);
        $obj->refresh();

        return $obj;
    }

    protected function findOrFail($id) {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();

        return $this->model()::where($keyName, $id)->firstOrFail();
    }

    public function show($id) {
        $obj = $this->findOrFail($id);

        return $obj;
    }

    public function update(Request $request, $id) {
        $obj = $this->findOrFail($id);
        $validateDate = $this->validate($request, $this->rulesUpdate());
        $obj->update($validateDate);
        $obj->refresh();

        return $obj;
    }

    public function destroy($id) {
        $obj = $this->findOrFail($id);
        $obj->delete();

        return response()->noContent();
    }
}
