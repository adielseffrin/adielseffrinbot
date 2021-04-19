<?php 

namespace AdielSeffrinBot\Interfaces;

interface RepositoryInterface
{
    public function findAll();
    public function findById(int $id);
    public function create(object $entity);
    public function remove(int $id);
    public function update(int $id, object $entity);
}