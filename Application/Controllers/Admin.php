<?php

namespace App\Controllers;

use App\Controller;
use App\Exceptions\Error404;
use App\Models\Article;
use App\MultiException;

class Admin extends Controller
{
    protected static $actionDefault = 'Base'; // Создаём свойство экшн по умолчанию для данного контроллера

    public function actionBase()
    {
        $all = Article::findAll();
        $this->view->all = $all;
        $this->view->page = $_GET['page'] ?: 1; // Для реализации пагинации
        $this->view->displayTwig('admin.html');
    }

    public function actionInsert()
    {
        $this->view->data = null; // Передавать нам нечего, но должно что-то быть передано, например пустой массив или null
        $this->view->displayTwig('insert.html');
    }

    public function actionEdit()
    {
        $this->view->data = Article::findById($_GET['id']);
        $this->view->displayTwig('edit.html');
    }

    public function actionSave()
    {
        try {
            $article = new Article();
            $article->fill($_POST);
            $article->save();
            header('Location: /admin/');
        } catch (MultiException $e) {
            $this->view->errors = $e;
            $this->view->displayTwig('insert.html');
        }
    }

    public function actionDelete()
    {
        $article = Article::findById($_GET['id']);
        if (null == $article) {
            throw new Error404;
        }
        $article->delete();
        header('Location: /admin/');
    }

}