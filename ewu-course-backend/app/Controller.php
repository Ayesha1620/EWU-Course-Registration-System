<?php

namespace App;

use PDO;

// Controller গুলোর common কাজ (JSON body পড়া, validation, generic CRUD)
// বিশেষ controller (Registration, Approval) এগুলো override করে নেয়।

abstract class Controller
{
    protected $db;
    protected $model;            // child এ set করা হয়
    protected $rules = [];        // required field list, validate() এ ব্যবহৃত

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    protected function input(): array
    {
        return get_json_input();
    }

    // required field check — validate করলেই সেটা ঠিক থাকে
    protected function validate(array $data, bool $creating = true): array
    {
        foreach ($this->rules as $field) {
            $mustCheck = $creating || array_key_exists($field, $data);
            if ($mustCheck && ($data[$field] === null || $data[$field] === '')) {
                json_error("{$field} is required", 422);
            }
        }
        return $data;
    }

    // ---------- generic REST handlers ----------

    public function index(): void
    {
        json_success($this->model->all());
    }

    public function show($id): void
    {
        $row = $this->model->find($id);
        if (!$row) {
            json_error('Not found', 404);
        }
        json_success($row);
    }

    public function store(): void
    {
        $row = $this->model->create($this->validate($this->input()));
        json_success($row, 'Created successfully', 201);
    }

    public function update($id): void
    {
        if (!$this->model->find($id)) {
            json_error('Not found', 404);
        }
        $this->model->update($id, $this->validate($this->input(), false));
        json_success($this->model->find($id), 'Updated successfully');
    }

    public function destroy($id): void
    {
        if (!$this->model->find($id)) {
            json_error('Not found', 404);
        }
        $this->model->delete($id);
        json_success([], 'Deleted successfully');
    }
}