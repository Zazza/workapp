<?php

/**
 * This file is part of the Workapp project Engine\Modules.
 *
 * (c) Dmitry Samotoy <dmitry.samotoy@gmail.com>
 *
 */

namespace Engine\Modules;

/**
 * Module interface
 * 
 * Требует обязательной реализации методов preInit() и postInit() в модуле
 * 
 * @author Dmitry Samotoy <dmitry.samotoy@gmail.com>
 */
interface Module {
	function preInit();
	function postInit();
}