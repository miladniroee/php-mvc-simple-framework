<?php

namespace Core;

use Exception;

class Router
{
    private string $Path;
    private array $Parameters = [];
    private ?string $Controller = NULL;
    private ?string $Method = NULL;

    public function __construct()
    {
        $RequestUri = str_replace(config('subdirectory'), '', $_SERVER['REQUEST_URI']);
        $RequestUri = str_replace('//', DIRECTORY_SEPARATOR, $RequestUri);
        $this->Path = parse_url($RequestUri, PHP_URL_PATH);
    }

    public function GetRoute()
    {
        $Routes = require __DIR__ . '/../Routes.php';
        foreach ($Routes as $Route):
            if ($this->ValidateRoute($Route['url'])):
                $this->Controller = $Route['controller'];
                $this->Method = $Route['method'];
                break;
            endif;
        endforeach;


        if ($this->Controller && $this->Method):

            $Instance = new $this->Controller;
            $Method = $this->Method;
            $Instance->$Method(...$this->Parameters);

        else:
            abort();
        endif;
    }

    private function ValidateRoute($RouteUrl): bool
    {
        // uri that programmer define
        $Uri = array_values(array_filter(explode('/', $RouteUrl)));

        //client url
        $Url = array_values(array_filter(explode('/', $this->Path)));

        if (count($Uri) !== count($Url)):
            return false;
        endif;

        foreach ($Uri as $Key => $Params):

            //if url has {*} accept whatever in it
            if (preg_match("/{(.*?)}/", $Params)):
                $Param = str_replace(['{', '}'], '', $Params);
                $this->Parameters[$Param] = $Url[$Key];
                continue;
            else:
                if ($Params !== $Url[$Key]):
                    return false;
                endif;
            endif;
        endforeach;

        // client url truly found
        return true;
    }

    /**
     * @throws Exception
     */
    public function GetRouteByName(string $RouteName, array $Data): string
    {
        $Routes = require __DIR__ . '/../Routes.php';
        $SearchRoute = array_search($RouteName, array_column($Routes, 'name'));
        if ($SearchRoute === false) {
            return $RouteName;
        } else {

            $ExtractUrlParams = array_values(array_filter(explode('/', $Routes[$SearchRoute]['url'])));


            foreach ($ExtractUrlParams as $Key => $Param):
                if (preg_match("/{(.*?)}/", $Param)):
                    $Variable = str_replace(['{', '}'], '', $Param);
                    if (in_array($Variable, array_keys($Data))):
                        $ExtractUrlParams[$Key] = $Data[$Variable];
                    else:
                        echo "<h2 style='direction: rtl;color:red;text-align:center'>value for {{$Variable}} in {{$RouteName}} Route is not defined.</h2>";
                        throw new Exception("value for {{$Variable}} in {{$RouteName}} Route is not defined");
                    endif;
                endif;
            endforeach;

            return implode('/', $ExtractUrlParams);
        }
    }
}