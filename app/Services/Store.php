<?php

namespace App\Services;

use App\Bll\ImageUploader;
use App\Bll\Utility;
use Illuminate\Support\Facades\Hash;

class Store
{

    private $model;
    private $request = [];

    protected $data;
    protected $public_path;
    protected $resource;
    protected $error, $success, $saved = "Saved successfully";
    protected $filters = [];
    protected $allowDelete = false;
    protected $custom_filter = [];
    private $arr_filters = [];
    private $created;
    private $createdData;
    protected $custom_order_by = [];

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function addFilter($name, $value)
    {
        $this->arr_filters[$name] = $value;
    }

    private function getFilterArray()
    {
        // dd($this->arr_filters, $this->request->all() );
        $this->arr_filters = array_merge($this->arr_filters, $this->request["filter"] ?? []);
    }

    private function subGet($columns, $data)
    {

        $this->getFilterArray(); 

        $limit = isset($this->request["limit"]) && (int)$this->request["limit"] > 0 ? (int)$this->request["limit"] : 20;
       
        if (isset($this->request["sort_by"]) && array_key_exists($this->request["sort_by"], $this->custom_order_by)) {
            $sort_by = $this->custom_order_by[$this->request["sort_by"]];
        } else {
            $sort_by = isset($this->request["sort_by"])
            && in_array($this->request["sort_by"], $columns)
                ? (isset($data['table']) && $data['table'] != null && $data['table'] != []
                    ? ($data['table'] . "." . $this->request["sort_by"])
                    : $this->model->getQuery()->from . '.id')
                : $this->model->getQuery()->from . '.id';
        }
        // check if sort_direction is valid if not set it to desc
        $sort_direction = isset($this->request["sort_direction"]) && in_array($this->request["sort_direction"], ['asc', 'desc']) ? $this->request["sort_direction"] : 'desc';
        // check if page is numeric if not set it to 1
        $page = isset($this->request["page"]) && is_int((int)$this->request["page"]) ? $this->request["page"] : 1;
        $term = $this->request["term"];


        $filter = (array_diff(array_keys($this->arr_filters), array_keys($this->custom_filter)));
        // dd(array_keys($this->arr_filters), array_keys($this->custom_filter));
        $filter = array_values($filter);

        if ($data) {
            $data = $this->model->join($data['table'], $this->model->getQuery()->from . '.id', '=', $data['table'] . '.' . $data['key'])->where("lang_id", Utility::lang_id()); //->orderBy($sort_by, $sort_direction);
        } else {
            $data = $this->model; //->orderBy($sort_by, $sort_direction);

        }
        //custom filters
        foreach ($filter as $key => $col) {
            $value = $this->arr_filters[$col];

            if (in_array($col, $this->filters) && !empty($value)) {
                $data = $data->where($col, $value);
            }
        }
        if ($term) {
            $data = $data->where(function ($data) use ($columns, $term) {
                $bol = 1;
                foreach ($columns as $col) {
                    if ($bol)
                        $data->Where($col, 'like', '%' . $term . '%');
                    else
                        $data->orWhere($col, 'like', '%' . $term . '%');
                    $bol = 0;
                }
            });

        }

        
        foreach ($this->custom_filter as $col => $func) {
            if (array_key_exists($col, $this->arr_filters)) {
                $data = call_user_func_array(
                    array($this, $func),
                    [$data]
                );
            }
        }

        // dd($sort_by, $sort_direction, $limit, $page);
        $data = $data->select($columns);
        $data = $data->orderBy($sort_by, $sort_direction);
        // dd($data->toRawSql());
        $this->data = $data->paginate($limit, ['*'], 'page', $page)->appends($this->request->query());
        //   dd($data);
    }

    protected function Get($columns, $data)
    {
        $this->subGet($columns, $data);
        $this->data = $this->resource::collection($this->data)->response()->getData();
        return $this->success;
    }

    protected function GetWithoutResource($columns, $data)
    {
        $this->subGet($columns, $data);
        return $this->data;
    }

    public function GetData()
    {
        return $this->data;
    }

    public function GetCreated()
    {
        return $this->created;
    }

    public function GetCreatedData()
    {
        return $this->createdData;
    }

    protected function saveImage($id, $image_name, $multi = null, $imgId = null)
    {
        if ($multi) {
            $imageUploader = new ImageUploader(public_path($this->public_path) . $id . '/' . $imgId . '/');
            $uploaded = $imageUploader->upload($image_name);
            $image = $this->public_path . $id . '/' . $imgId . '/' . $uploaded;
        } else {
            $imageUploader = new ImageUploader(public_path($this->public_path) . $id . '/');
            $uploaded = $imageUploader->upload($this->request->file($image_name));
            $image = $this->public_path . $id . '/' . $uploaded;
        }

        return $image;
    }

    private function getMapped($columns)
    {
        $mapped = [];
        foreach ($columns as $col) {
            if ($col === 'password') {
                if (isset($this->request[$col]) && $this->request[$col] != null && $this->request[$col] != '')
                    $mapped[$col] = Hash::make($this->request[$col]);

            } else {
                $mapped[$col] = $this->request[$col];
            }
        }

        return $mapped;
    }

    protected function store($primary_columns, $data_columns, $foreign_key, $image_name = null)
    {
        $created = $this->model->create($this->getMapped($primary_columns));

        if ($image_name != null) {
            $this->checkImage($created, $image_name);
        }

        if ($data_columns != []) {
            $data = $this->getMapped($data_columns);
            $data[$foreign_key] = $created->id;
            $this->createdData = $created->Data()->create($data);
        }

        $this->created = $created;
        $this->data = $this->resource::make($created);
        return $this->saved;
    }

    protected function update($primary_columns, $data_columns, $foreign_key, int $id, $image_name = null, $option_id = null)
    {
        $update = $this->model->findOrFail($id);


        $update->update($this->getMapped($primary_columns));
        // dd($this->getMapped($data_columns));
        if ($data_columns != []) {
            $update->Data()->update($this->getMapped($data_columns));
        }
        if ($image_name != null) {
            $this->checkImage($update, $image_name);
        }
        if ($option_id != null) {
            $options = $this->getMapped($option_id);
            $update->options()->sync($options['option_id']);
        }
        $update = $this->model->where('id', $id)->first();

        $this->data = $this->resource::make($update);
        return $this->saved;
    }

    private function checkImage($entity, $image_name)
    {
        if ($this->request->hasFile($image_name)) {
            $image = $this->saveImage($entity->id, $image_name);
            $entity[$image_name] = $image;
            $entity->save();
        } else {

            if (!isset($this->request->$image_name) || $this->request->$image_name === null || $this->request->$image_name == '') {
                if ($this->allowDelete) {
                    $entity[$image_name] = null;
                    $entity->save();
                    return;
                }

                return;
            }
            // check if image came from STore Gallery
            $store_gallery = public_path('uploads/' . Utility::school_id() . '/' . $this->request->$image_name);
            if (file_exists($store_gallery)) {
                $entity[$image_name] = 'uploads/' . Utility::school_id() . '/' . $this->request->$image_name;
                $entity->save();
                return;
            }

            $iu = new ImageUploader(public_path($this->public_path) . $entity->id . '/');
            $res = $iu->moveUploadedFile($this->request->$image_name, public_path($this->public_path) . $entity->id . '/', true);
            if ($res) {
                $entity[$image_name] = $this->public_path . $entity->id . '/' . $res;
                $entity->save();
            }
        }
    }

    protected function show(int $id)
    {
    }

    // protected function edit(int $id)
    // {
    // }

    protected function delete(int $id)
    {
        $data = $this->model->where('id', $id)->first();
        if ($data != null) {
            if (isset($data->image) && $data->image != null && $data->image != '') :
                $image_path = public_path($data->image);

                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            endif;

            $data->delete();

        }
    }
}
