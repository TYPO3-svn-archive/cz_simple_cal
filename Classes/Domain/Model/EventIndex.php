<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2010 Christian Zenker <christian.zenker@599media.de>, 599media GmbH
*  			
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * 
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_CzSimpleCal_Domain_Model_EventIndex extends Tx_Extbase_DomainObject_AbstractEntity {
	
	/**
	 * the timestamp from the beginning of that event
	 * 
	 * @ugly integer is used as we'd like an instance of the Utility_DayTime, but extbase would only return a DateTime Object in the extbase version shipped with TYPO3 4.4
	 * @var integer
	 */
	protected $start;
	
	/**
	 * the timestamp from the end of that event
	 * 
	 * @ugly integer is used as we'd like an instance of the Utility_DayTime, but extbase would only return a DateTime Object in the extbase version shipped with TYPO3 4.4
	 * @var integer
	 */
	protected $end;
	
	/**
	 * the start date as DateTime object
	 * 
	 * @var Tx_CzSimpleCal_Utility_DateTime
	 */
	protected $dateTimeObjectStart = null;
	
	/**
	 * the end date as DateTime object
	 * 
	 * @var Tx_CzSimpleCal_Utility_DateTime
	 */
	protected $dateTimeObjectEnd = null;
	
	/**
	 * the pid of the record
	 * 
	 * @var integer
	 */
	protected $pid;
	
	/**
	 * @var Tx_CzSimpleCal_Domain_Model_Event
	 */
	protected $event;
	
	/**
	 * set the timestamp from the beginning of that event
	 * 
	 * @param integer $start
	 * @return null
	 */
	public function setStart($start) {
		$this->start = $start;
		$this->dateTimeObjectStart = null;
	}
	
	/**
	 * get the timestamp from the beginning of that event
	 * @return integer
	 */
	public function getStart() {
		return $this->start;
	}
	
	/**
	 * get the start of this event as a dateTimeObject
	 * 
	 * @return Tx_CzSimpleCal_Utility_DateTime
	 */
	public function getDateTimeObjectStart() {
		if(is_null($this->dateTimeObjectStart)) {
			$this->dateTimeObjectStart = new Tx_CzSimpleCal_Utility_DateTime($this->start);
		}
		return $this->dateTimeObjectStart;
	}
	
	/**
	 * set the timestamp from the end of that event
	 * @param integer $end
	 * @return null
	 */
	public function setEnd($end) {
		$this->end = $end;
		$this->dateTimeObjectEnd = null;
	}
	
	/**
	 * get the timestamp from the end of that event
	 * @return integer
	 */
	public function getEnd() {
		return $this->end;
	}
	
	/**
	 * get the end of this event as a dateTimeObject
	 * 
	 * @return Tx_CzSimpleCal_Utility_DateTime
	 */
	public function getDateTimeObjectEnd() {
		if(is_null($this->dateTimeObjectEnd)) {
			$this->dateTimeObjectEnd = new Tx_CzSimpleCal_Utility_DateTime($this->end);
		}
		return $this->dateTimeObjectEnd;
	}
	
	/**
	 * set the pid of the record
	 * 
	 * @param $pid
	 * @return null
	 */
	public function setPid($pid) {
		$this->pid = $pid;
	}
	
	/**
	 * get the pid of the record
	 * 
	 * @return integer
	 */
	public function getPid() {
		return $this->pid;
	}
	
	public function setEvent($event) {
		$this->event = $event;
	}
	
	public function getEvent() {
		return $this->event;
	}
	
	
	/**
	 * create a new instance with data from a given array
	 * 
	 * @param $data
	 * @return Tx_CzSimpleCal_Domain_Model_EventIndex
	 */
	public static function fromArray($data) {
		$obj = new Tx_CzSimpleCal_Domain_Model_EventIndex();
		
		foreach($data as $name => $value) {
			$methodName = 'set'.t3lib_div::underscoredToUpperCamelCase($name);
			
			// check if there is a setter defined (use of is_callable to check if the scope is public)
			if(!is_callable(array($obj,	$methodName))) {
				throw new InvalidArgumentException(sprintf('Could not find the %s method to set %s in %s.', $methodName, $name, get_class($obj)));
			}
			
			call_user_func(array($obj, $methodName), $value);
		}
		
		return $obj;		
	}
	
	/**
	 * get a hash for this recurrance of the event
	 * 
	 * @return string
	 */
	public function getHash() {
		return md5(
			'eventindex-'.
			$this->getEvent()->getHash().'-'.
			$this->getStart().'-'.
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']
		);
	}
	
	/**
	 * tunnel all methods that were not found to the Event
	 * 
	 * @param $method
	 * @param $args
	 * @return mixed
	 */
	public function __call($method, $args) {
		if(!$this->event) {
			throw new BadMethodCallException(sprintf('The method %s was not found in %s.', $method, get_class($this)));
		}
		$callback = array($this->event, $method);
		if(!is_callable($callback)) {
			throw new BadMethodCallException(sprintf('The method %s was neither found in %s nor in %s.', $method, get_class($this), get_class($this->event)));
		}
		
		return call_user_func_array($callback, $args);
	}
}
?>