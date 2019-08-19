<?php
namespace App\Repositories\Notification;

interface NotificationRepositoryInterface{
  public function find($id);
  public function getByUserID($id);
  public function store($data);
  public function destroy($id);
}