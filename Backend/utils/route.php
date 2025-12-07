<?php
    namespace Backend\Utils;

    require_once __DIR__ . '/httpOnlyCookie.php';

    use Backend\Utils\HttpOnlyCookie;
    use Exception;

    class Route{
        private static $instance = null;
        private $routes=[];
        public $request=[
            "remainingPath"=>'',
            "body"=>[],
            "params"=>[],
            "query"=>[],
            "cookie"=>null
        ];
        public $respond=[
            "cookie" => null,
            "status" => []
        ];
        public function __construct(){
            $this->getRequest();
            $this->setHttpCookie();
        }
        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        private function getRequest(){
            $this->pathParameters();
            $this->queryStringParameters();
            $this->requestBodyParameters();
        }
        private function  pathParameters(){
            $pathInfo = $_SERVER['PATH_INFO'] ?? '';
            if (!empty($pathInfo)) {
                $segments = explode('/', trim($pathInfo, '/'));

                $pathParameters=[];
                foreach($segments as $segment => $value){
                    $pathParameters[$segment]= $value;
                }
                $this->request['params'] =$pathParameters;
            } 
        } 
        private function queryStringParameters(){
            $queryStringParameters=[];
            if($_GET){
                foreach($_GET as $key => $value){
                    $queryStringParameters[$key] =$value; 
                }
                $this->request['query'] =$queryStringParameters; 
            }
            if($_POST){
                foreach($_POST as $key => $value){
                    $queryStringParameters[$key] =$value; 
                }
                $this->request['query'] =$queryStringParameters;
            }
        }
        private function requestBodyParameters(){
            $allHeaders = getallheaders();
            $requestBodyParameters=[];
            if (isset($allHeaders['Content-Type'])) {
                $contentType = $allHeaders['Content-Type'];
                if($contentType === 'application/x-www-form-urlencoded' || $contentType === 'multipart/form-data'){
                    if($_POST){
                        foreach($_POST as $key => $value){
                            $requestBodyParameters[$key] =$value; 
                        }
                        $this->request['body'] =$requestBodyParameters;
                    }
                }
                if($contentType === 'application/json' || $contentType === null){
                    $requestBody = file_get_contents('php://input');
                    $body = json_decode($requestBody, true);
                    if ($body !== null) {
                        $this->request['body'] = $body;
                    }
                }
            }
        }
        private function setHttpCookie (){
            $httpCookie = new HttpOnlyCookie();
            $this->respond["cookie"] = function($name, $value, $options = []) use ($httpCookie) {
                $httpCookie->setCookie($name, $value, $options);
            };
            $this->request["cookie"] = function($name) use ($httpCookie) {
                return $httpCookie->get($name);
            };
        }
        public function accessEndpoint($basePath, $fileDir){
            $requestUri = $_SERVER['REQUEST_URI'];
            $urlPath = parse_url($requestUri, PHP_URL_PATH);
            $baseDir = dirname($_SERVER['SCRIPT_NAME']);

            if ($baseDir !== '/') {
                $urlPath = str_replace($baseDir, '', $urlPath);
            }
            if (strpos($urlPath, $basePath) === 0) {
                $remainingPath = substr($urlPath, strlen($basePath));
                $this->request['remainingPath'] = $remainingPath;
                if (file_exists($fileDir)) {
                    require_once  __DIR__ . "/../" . $fileDir;
                    // $folderPath =explode("/", $fileDir);
                    // $originalUrl = __DIR__;
                    // $parentDirectory = dirname($originalUrl); 
                    // $directoryPath = $parentDirectory .'/'.$folderPath[0].'/'; 
                    // if (is_dir($directoryPath)) {
                    //     if ($dh = opendir($directoryPath)) {
                    //         while (($file = readdir($dh)) !== false) {
                    //             if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    //                 $filePath = $directoryPath . $file;

                    //                 require_once $filePath;
                    //                 $className = str_replace(' ', '', ucwords(str_replace('_', ' ', pathinfo($file, PATHINFO_FILENAME))));
                    //                 $contents = file_get_contents($filePath);

                    //                 preg_match('/namespace\s+(.+);/', $contents, $namespaceMatch);
                    //                 $namespace = $namespaceMatch[1] ?? '';
                    //                 $fullClassName = $namespace ? $namespace . '\\' . $className : $className; 
                    //                 if (class_exists($fullClassName)) {
                    //                     new $fullClassName('Run+++');
                    //                 }
                    //             }
                    //         }
                    //         closedir($dh);
                    //     }
                    // } else {
                    //     echo "Directory not found: " . $directoryPath . "\n";
                    // }
                    return true;
                } else {
                    http_response_code(404);
                    echo "Router file not found: " . $fileDir;
                    return false;
                }
            }
            return false;
        }
        public function post($path,  $middlewareOrCallback, $callback = null){
            $middlewares = [];
            $actualCallback = $callback;

            if ($callback === null) {
                $actualCallback = $middlewareOrCallback;
            } else {
                $middlewares = (array)$middlewareOrCallback;
            }
            $this->routes['POST'][] = [
                'path' => $path,
                'middlewares' => $middlewares,
                'callback' => $actualCallback
            ];
            // $isValidPath = false;
            // $middlewares = [];
            // $remainingPath=$this->request['remainingPath'];
            // if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            //     return 0;
            // }
            // if($path ==='/' && empty($remainingPath)){
            //     echo "RUN 01 => $remainingPath \n ";
            //     $isValidPath =true;
            // }
            // elseif ($path === $this->request['remainingPath']){
            //     echo "RUN 02 \n";
            //     $isValidPath =true;
            // }
            // echo "$path \n";
            // echo $this->request['remainingPath']." \n";
            // if($isValidPath){
            //     if (json_last_error() !== JSON_ERROR_NONE) {
            //         throw new Exception('JSON Decode Error: ' . json_last_error_msg());
            //     }
            //     $request = $this->request;
            //     // Determine if middleware was provided
            //     if ($callback === null) {
            //         $callback = $middlewareOrCallback;
            //     } else {
            //         $middlewares = (array)$middlewareOrCallback;
            //     }
            //     if(is_array($middlewares) && count($middlewares)>0){
            //         foreach($middlewares as $middleware){
            //             if(is_callable($middleware)){
            //                 $middleware($request);
            //             }
            //         }
            //         if(is_callable($callback)){
            //             $callback($request);
            //         }
            //     }else{
            //         if(is_callable($callback)){
            //             $callback($request);
            //         }
            //     }
            // }
            // else{
            //     throw new Exception("Path not found");
            // }
        }

        public function get($path,  $middlewareOrCallback, $callback = null){
            $middlewares = [];
            $actualCallback = $callback;

            if ($callback === null) {
                $actualCallback = $middlewareOrCallback;
            } else {
                $middlewares = (array)$middlewareOrCallback;
            }

            $this->routes['GET'][] = [
                'path' => $path,
                'middlewares' => $middlewares,
                'callback' => $actualCallback
            ];
            // $isValidPath = false;
            // $middlewares = [];
            // $remainingPath='';
            // if(empty($this->remainingPath)){
            //     $remainingPath = $this->request['remainingPath'];
            // }
            // if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            //     return 0;
            // }
            // if($path ==='/'){
            //     $isValidPath =true;
            // }
            // elseif ($path === $remainingPath){
            //     $isValidPath =true;
            // }
            // if($isValidPath && ($path === $remainingPath || ($path === '/' && empty($remainingPath)))){
            //     $request =$this->request;
            //     // Determine if middleware was provided
            //     if ($callback === null) {
            //         $callback = $middlewareOrCallback;
            //     } else {
            //         $middlewares = (array)$middlewareOrCallback;
            //     }
            //     if(is_array($middlewares) && count($middlewares)>0){
            //         foreach($middlewares as $middleware){
            //             if(is_callable($middleware)){
            //                 $middleware();
            //             }
            //         }
            //         if(is_callable($callback)){
            //             $callback($request);
            //         }
            //     }else{
            //         if(is_callable($callback)){
            //             $callback($request);
            //         }
            //     }
                
            // }else{
            //     throw new Exception("Path not found");
            // }
        }

        public function dispatch(){
            $method = $_SERVER['REQUEST_METHOD'];
            $requestedPath = $this->request['remainingPath'];
            
            if (empty($requestedPath)) {
                $requestedPath = '/';
            }

            if (!isset($this->routes[$method])) {
                http_response_code(405);
                throw new Exception("Method not allowed");
            }

            $matched = false;

            foreach ($this->routes[$method] as $route) {
                $path = $route['path'];
                
                // More flexible path matching
                $normalizedRequestedPath = $requestedPath === '' ? '/' : $requestedPath;
                $normalizedRoutePath = $path === '' ? '/' : $path;
                
                if ($normalizedRoutePath === $normalizedRequestedPath) {
                    $matched = true;
                    
                    // Execute middlewares first
                    if (!empty($route['middlewares'])) {
                        foreach ($route['middlewares'] as $middleware) {
                            if (is_callable($middleware)) {
                                // Direct function call
                                $middleware($this->request, $this->respond);
                            } elseif (is_array($middleware) && count($middleware) === 2) {
                                // [object, method] format
                                list($object, $methodName) = $middleware;
                                if (method_exists($object, $methodName)) {
                                    $object->$methodName($this->request, $this->respond);
                                } else {
                                    throw new Exception("Middleware method not found: " . $methodName);
                                }
                            } else {
                                throw new Exception("Invalid middleware format");
                            }
                        }
                    }

                    // Execute the callback
                    if (is_callable($route['callback'])) {
                        $route['callback']($this->request, $this->respond); 
                    } else {
                        throw new Exception("Callback is not callable");
                    }
                    
                    break;
                }
            }

            if (!$matched) {
                http_response_code(404);
                throw new Exception("Path not found: " . $requestedPath);
            }
        }
    }

?>