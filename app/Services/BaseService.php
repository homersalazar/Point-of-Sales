<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    protected $repo;

    public function __construct($repo)
    {
        $this->repo = $repo;
    }

    public function getAll()
    {
        return $this->repo->all();
    }

    public function show($id)
    {
        return $this->repo->find($id);
    }

    public function search($search = null, $perPage = 10)
    {
        if (method_exists($this->repo, 'paginate')) {
            return $this->repo->paginate($search, $perPage);
        }
        return $this->repo->all();
    }

    protected function executeTransaction(callable $callback, string $errorMessage = '')
    {
        DB::beginTransaction();
        try {
            $result = $callback();
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($errorMessage, ['exception' => $e]);
            return null;
        }
    }

    public function store(array $data)
    {
        return $this->executeTransaction(fn() => $this->repo->create($data), 'create error');
    }

    public function update(array $data, $id)
    {
        return $this->executeTransaction(fn() => $this->repo->update($data, $id), 'update error');
    }

    public function delete($id)
    {
        return $this->executeTransaction(fn() => $this->repo->delete($id), 'delete error');
    }
}
