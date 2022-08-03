<?php
namespace Helper;

// import helper methods includes in config.php list
foreach (HELPERS as $helper_module) {
    require_once $helper_module;
}