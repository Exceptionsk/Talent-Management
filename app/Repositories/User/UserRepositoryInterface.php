<?php
namespace App\Repositories\User;

interface UserRepositoryInterface{
  public function getAll($offset, $limit, $type, $name, $course, $batch, $gender, $promote);
  public function giveResults($offset, $limit, $course_id, $batch_id);
  public function find($id);
  public function findByUid($uid);
  public function total();
  public function store($data);
  public function update($request, $id);
  public function delete($id);
}
