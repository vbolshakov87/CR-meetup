<?php 


class my_class {

private function makeResponse($request)
{
    $controllerFunctions = array();
    $controllerName = false;
    $controller = false;
    $langRQ = false;

    // Controller
    if ( $request['controller'] ) {
        // Load by lang
        if ( $controllerName = array_search( $request['controller'], $this->_lang_controllers ) ) {
            if ( isset($this->_routing[$controllerName]) ) {
                $controllerFunctions = $this->_routing[$controllerName];
                $controller = $controllerName.'Controller';
            }
        }
        if ( !$controller ) {
            $controller = new Controller\ErrorController();
            return $controller->error404Action($request);
        }
    } else {
        // ???
        $controllerFunctions = reset($this->_routing);
        $cKey = array_keys($this->_routing);
        $controllerName = reset($cKey);
        $controller = $controllerName.'Controller';
    }

    // Action
    $action = false;
    if ( $request['action'] ){
        if ( $actionName = array_search( $request['action'], $this->_lang_actions[$controllerName] ) ) {
            if ( isset($controllerFunctions[$actionName]) ) {
                $aKey =$actionName;
                $action =$actionName.'Action';
            }
        }
        if ( !$action ) {
            $controller = new Controller\ErrorController();
            return $controller->error404Action($request);
        }
    } else {
        $aKey = array_keys($controllerFunctions);
        $aKey = reset($aKey);
        $action = $aKey.'Action';
    }
   
    // Correct method, GET POST
    $actionMethod =  $controllerFunctions[$aKey]['method'];
    $actionMethods = explode('|', $actionMethod);
    if ( !in_array($this->_method, $actionMethods)) 
        throw new AppException('Method not permited',  0);

    // Get Controller Class
    try {
        $controller = str_replace(' ', '', "Controller \ $controller" );
        $controller = new $controller();
    } catch (AppException $e){
        throw new AppException('Controller Class Not Found',  0);
    }
    
    // Get Action and Do
    try {
        $response = $controller->$action($request['params']);
    } catch (AppException $e){
        throw new AppException('Action Not Found',  0);
    }
    
    return $response;
}


}