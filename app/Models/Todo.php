<?php

namespace App\Models;

use PDO;
use App\Lib\DB;

class Todo {

    private int $id;
    private string $text;
    private bool $done = false;

    public function __construct(int $id = null) {

        if(!empty($id)) {

            $this->find($id);
        }
    }

    public static function get(bool $withTrashed = false): array
    {
        if($withTrashed === true)
        {
            $res = DB::getInstance()->query('SELECT * FROM todos');
        }
        else
        {
            $res = DB::getInstance()->query('SELECT * FROM todos WHERE deleted_at IS NULL');
        }

        return $res->fetchAll(PDO::FETCH_CLASS, "App\Models\Todo");
    }

    public function find(int $id): Todo
    {
        $res = DB::getInstance()->prepare('SELECT * FROM todos WHERE id = :id');
        $res->bindParam('id', $id);
        $res->execute();

        $todo = $res->fetchObject("App\Models\Todo");

        if(!empty($todo))
        {
            $this->id = $todo->id;
            $this->text = $todo->text;
            $this->done = $todo->done;
        }

        return $this;
    }

    public function save(): int { //id teruggeven van die save

        //heeft object id update, geen id ne nieuwe
        if(!empty($this->id)) {

            return $this->update();
        }

        return $this->add();
    }

    private function add(): int
    {
        $res = DB::getInstance()->prepare('INSERT INTO todos (text) VALUES (:text)');
        $res->bindParam('text', $this->text);  //text van dit object
        $res->execute();

        $this->id = DB::getInstance()->lastInsertId(); //checken

        return $this->id;
    }

    function update(): int
    {
        if(empty($this->id)) {

            throw new \Excemption('No todo selected');
        }

        $now = date('Y-m-d H:i:s');

        $res = DB::getInstance()->prepare('UPDATE todos SET text = :text, done = :done, updated_at = :updated_at WHERE id = :id');
        $res->bindParam('id', $this->id);
        $res->bindParam('text', $this->text);
        $res->bindParam('done', $this->done, PDO::PARAM_INT);
        $res->bindParam('updated_at', $now);
        $res->execute();

        return $this->id;
    }

    public static function pending(): int
    {
        $res = DB::getInstance()->query('SELECT COUNT(*) FROM todos WHERE done = 0 and deleted_at IS NULL');

        return $res->fetchColumn();
    }

    public static function completed(): int
    {
        $res = DB::getInstance()->query('SELECT COUNT(*) FROM todos WHERE done = 1 and deleted_at IS NULL');

        return $res->fetchColumn();
    }

    function delete(): bool
    {
        // $res = $db->prepare('DELETE FROM todos WHERE id = :id');
        // $res->bindParam('id', $id);
        // $res->execute();

        $now = date('Y-m-d H:i:s');

        $res = DB::getInstance()->prepare('UPDATE todos SET deleted_at = :deleted_at WHERE id = :id');
        $res->bindParam('id', $this ->id);
        $res->bindParam('deleted_at', $now);
        $res->execute();

        return true;
    }

    public function isDone(): bool {

        return $this->done;
    } 

    public function isNotDone(): bool {

        return !$this->done;
    } 

    public function getId(): int{

        return $this->id;
    }

    public function getText(): string{

        return $this->text;
    }

    public function setText(string $text): void {

        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $this->text = $text;
    }

    public function setDone(): void {
        $this->done= true;
    }

    public function setUnDone(): void {
        $this->done = false;
    }
}

