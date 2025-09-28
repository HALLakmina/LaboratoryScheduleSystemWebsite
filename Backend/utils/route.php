<?php
    namespace Backend\Utils;

    class Route{
        public $remainingPath='';
        public function accessEndpoint($basePath, $fileDir){
            // Get the current request URL
            $requestUri = $_SERVER['REQUEST_URI'];
            // Parse the URL to get the path component
            $urlPath = parse_url($requestUri, PHP_URL_PATH);

            // Remove any base directory if your project isn't at the root
            $baseDir = dirname($_SERVER['SCRIPT_NAME']);

            if ($baseDir !== '/') {
                $urlPath = str_replace($baseDir, '', $urlPath);
            }
            // Check if the request starts with our API path
            if (strpos($urlPath, $basePath) === 0) {
                // Extract the remaining path after our base path
                $remainingPath = substr($urlPath, strlen($basePath));
                $this->remainingPath = $remainingPath;
                // Include the router file if it exists
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
            $isValidPath = false;
            $middlewares = [];
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                return 0;
            }
            if($path ==='/'){
                $isValidPath =true;
                echo '/';
            }
            elseif ($path === $this->remainingPath){
                $isValidPath =true;
                echo $this->remainingPath;
            }

            if($isValidPath){
                $body = $_POST;
                $request = [
                    'body' => !empty($body) ? $body : []
                ];
                if($path){
                    // Determine if middleware was provided
                    if ($callback === null) {
                        $callback = $middlewareOrCallback;
                    } else {
                        $middlewares = (array)$middlewareOrCallback;
                    }
                    if(is_array($middlewares) && count($middlewares)>0){
                        foreach($middlewares as $middleware){
                            if(var_dump(is_callable($middleware))){
                                $middleware();
                            }
                        }
                        if(var_dump(is_callable($callback))){
                            $callback($request);
                        }
                    }else{
                        if(var_dump(is_callable($callback))){
                            $callback($request);
                        }
                    }
                }
                
            }
        }

        public function get($path,  $middlewareOrCallback, $callback = null){
            $isValidPath = false;
            $middlewares = [];
            if ($_SERVER['REQUEST_METHOD'] != 'GET') {
                return 0;
            }
            if($path ==='/'){
                $isValidPath =true;
            }
            elseif ($path === $this->remainingPath){
                $isValidPath =true;
                echo $this->remainingPath;
            }

            if($isValidPath){
                $body = $_GET;
                $request = [
                    'body' => !empty($body) ? $body : []
                ];
                if($path){
                    // Determine if middleware was provided
                    if ($callback === null) {
                        $callback = $middlewareOrCallback;
                    } else {
                        $middlewares = (array)$middlewareOrCallback;
                    }
                    if(is_array($middlewares) && count($middlewares)>0){
                        foreach($middlewares as $middleware){
                            if(is_callable($middleware)){
                                $middleware();
                            }
                        }
                        if(is_callable($callback)){
                            $callback($request);
                        }
                    }else{
                        if(is_callable($callback)){
                            $callback($request);
                        }
                    }
                }
                
            }
        }
    }

?>