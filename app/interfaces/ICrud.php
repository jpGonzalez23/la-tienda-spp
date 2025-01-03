<?php

interface ICrud {
    public static function create($data);
    public static function read($id);
    public static function readAll();
    public static function update($data);
    public static function delete($id);
}