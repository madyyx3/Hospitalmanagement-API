<?php

namespace Hms\Controllers;

use Hms\Utils\Validator;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class DefaultController
{
    private Environment $twig;
    protected Validator $validator;

    public function __construct()
    {
        $loader = new FilesystemLoader('resources/views');
        $this->twig = new Environment($loader);
        $this->validator = new Validator();
    }

    protected function render(string $file, array $params = [])
    {
        $_SESSION['template'] = $file;

        echo $this->twig->render($file, $params);
    }

    protected function redirect(string $path)
    {
        header("Location: $path");
    }

    protected function validate($params, $rules)
    {
        $response = $this->validator->validate($params, $rules);
        if ($response) {
            $params['error'] = $response;
            echo $this->twig->render($_SESSION['template'], $params);
            die();
        }

        return true;
    }
}
