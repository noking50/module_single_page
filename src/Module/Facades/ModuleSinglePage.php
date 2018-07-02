<?php

namespace Noking50\Modules\SinglePage\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see Noking50\User\User
 */
class ModuleSinglePage extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'module_single_page';
    }

}
