<?php
namespace App\Repositories\Admin;

interface AdminRepositoryInterface{
  public function getAll();
  public function find($id);
  public function findByEmail($email);
  public function total();
  public function store($data);
  public function update($request, $id);
  public function delete($id);
}
