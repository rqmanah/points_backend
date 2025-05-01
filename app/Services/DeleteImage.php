<?php

namespace App\Services;
class DeleteImage
{
    private $model;
    protected $success = "Deleted successfully";
    protected $error = "Error while deleting";

    public function __construct($model)
    {
        $this->model = $model;
    }
    public function delete($primary_column, int $id)
    {
        $data = $this->model::findOrfail($id);
        if ($data) {
            $path = $data->$primary_column;
            if (file_exists($path) && $path != null && $path != "") {
                unlink($path);
            }
            $data->update([$primary_column => null]);
            return $this->success;
        }
        return $this->error;
    }
}
