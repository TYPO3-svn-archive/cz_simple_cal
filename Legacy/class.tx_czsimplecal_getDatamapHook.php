<?php 


require_once(t3lib_extMgm::extPath('cz_simple_cal').'Classes/Hook/Datamap.php');

/**
 * it is not possible to create a class dynamically, it its name does not start with "tx_" or "user_".
 * "Tx_" has the incorrect case therefore 
 */
class tx_czsimplecal_getDatamapHook extends Tx_CzSimpleCal_Hook_Datamap {}