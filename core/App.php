<?php

class App
{
    protected $controller = 'AuthController';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();
        $notFound = false;
        $isQrVerifyAlias = false;

        if (isset($url[0])) {
            if ($url[0] === 'login') {
                $this->controller = 'AuthController';
                $this->method = 'index';

                unset($url[0]);
            } elseif ($url[0] === 'logout') {
                $this->controller = 'AuthController';
                $this->method = 'logout';

                unset($url[0]);
            } elseif ($url[0] === 'dashboard') {
                $this->controller = 'DashboardController';
                $this->method = 'index';

                unset($url[0]);
            } elseif ($url[0] === 'verify') {
                $this->controller = 'VerificationController';
                $this->method = 'index';
                $isQrVerifyAlias = true;

                unset($url[0]);
            } elseif (preg_match('/\A[A-Za-z0-9_-]+\z/', $url[0])) {
                $formattedController = str_replace(' ', '', ucwords(str_replace('-', ' ', $url[0])));

                if (file_exists('controllers/' . $formattedController . 'Controller.php')) {
                    $this->controller = $formattedController . 'Controller';

                    unset($url[0]);
                } elseif (file_exists('controllers/' . $formattedController . '.php')) {
                    $this->controller = $formattedController;

                    unset($url[0]);
                } elseif (file_exists('controllers/' . $url[0] . '.php')) {
                    $this->controller = $url[0];

                    unset($url[0]);
                } else {
                    $notFound = true;
                }
            } else {
                $notFound = true;
            }
        }

        if (!$notFound && file_exists('controllers/' . $this->controller . '.php')) {
            require_once 'controllers/' . $this->controller . '.php';

            $this->controller = new $this->controller;

            if (!$isQrVerifyAlias && isset($url[1])) {
                if ($this->isPublicControllerAction($this->controller, $url[1])) {
                    $this->method = $url[1];

                    unset($url[1]);
                } else {
                    $notFound = true;
                }
            }

            if (!$notFound && $this->isPublicControllerAction($this->controller, $this->method)) {
                $this->params = $url ? array_values($url) : [];

                call_user_func_array([$this->controller, $this->method], $this->params);
            } else {
                $this->renderNotFound();
            }
        } else {
            $this->renderNotFound();
        }
    }

    private function isPublicControllerAction($controller, $methodName)
    {
        $allowed = false;

        if (is_string($methodName) && !str_starts_with($methodName, '__') && method_exists($controller, $methodName)) {
            $reflection = new ReflectionMethod($controller, $methodName);

            $allowed = $reflection->isPublic() && $reflection->getDeclaringClass()->getName() === get_class($controller);
        }

        return $allowed;
    }

    private function renderNotFound()
    {
        http_response_code(404);

        require 'views/errors/404.php';
    }

    public function parseUrl()
    {
        $result = [];

        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $result = explode('/', $url);
        }

        return $result;
    }
}
