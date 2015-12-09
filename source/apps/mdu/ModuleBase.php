<?php

namespace Apiserver\Mdu\Modules;

class ModuleBase
{
    protected function initModel($model)
    {
        $modObj = new $model();
        $this->di = $modObj->getDI();
        return $modObj;
    }
}