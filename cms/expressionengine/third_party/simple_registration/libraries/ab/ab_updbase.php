<?php
if(!class_exists('Ab_Common')) { require_once 'ab_common.php'; }
/**
 * AddonBakery - base class for UPD file (installer)
 * 
 */
 
class Ab_UpdBase extends Ab_Common {

    function __construct( $switch = TRUE )
    {
        parent::__construct();
    }
}
