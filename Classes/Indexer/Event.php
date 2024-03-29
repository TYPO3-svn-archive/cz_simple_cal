<?php 

/**
 * a class that handles indexing of events
 * 
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class Tx_CzSimpleCal_Indexer_Event {
	
	static private
		$eventTable = 'tx_czsimplecal_domain_model_event',
		$eventIndexTable = 'tx_czsimplecal_domain_model_eventindex'
	;
	
	/**
	 * @var Tx_CzSimpleCal_Domain_Repository_EventRepository
	 */
	protected $eventRepository = null;
	
	/**
	 * @var Tx_CzSimpleCal_Domain_Repository_EventIndexRepository
	 */
	protected $eventIndexRepository = null;
	
	/**
	 * constructor
	 */
	public function __construct() {
		t3lib_div::makeInstance('Tx_Extbase_Dispatcher');
		
		$this->eventRepository = t3lib_div::makeInstance('Tx_CzSimpleCal_Domain_Repository_EventRepository');
		$this->eventIndexRepository = t3lib_div::makeInstance('Tx_CzSimpleCal_Domain_Repository_EventIndexRepository');
	}
	
	/**
	 * destructor
	 * 
	 * this will persist all changes
	 */
	public function __destruct() {
		Tx_Extbase_Dispatcher::getPersistenceManager()->persistAll();
	}
	
	/**
	 * create an eventIndex
	 * 
	 * @param integer|Tx_CzSimpleCal_Domain_Model_Event $event
	 */
	public function create($event) {
		if(is_integer($event)) {
			$event = $this->fetchEventObject($event);
		}
		
		$this->doCreate($event);
	}
	
	/**
	 * update an eventIndex
	 * 
	 * @param integer|Tx_CzSimpleCal_Domain_Model_Event $event
	 */
	public function update($event) {
		if(is_integer($event)) {
			$event = $this->fetchEventObject($event);
		}
		
		$this->doDelete($event);
		$this->doCreate($event);
		
	}
	
	/**
	 * delete the eventIndex 
	 * 
	 * @param integer|Tx_CzSimpleCal_Domain_Model_Event $event
	 */
	public function delete($event) {
		if(is_integer($event)) {
			$event = $this->fetchEventObject($event);
		}
		
		$this->doDelete($event);
	}
	
	/**
	 * delete an event
	 * 
	 * @param Tx_CzSimpleCal_Domain_Model_Event $event
	 */
	protected function doDelete($event) {
		$GLOBALS['TYPO3_DB']->exec_DELETEquery(
			self::$eventIndexTable,
			'event = '.$event->getUid()
		);
	}
	
	/**
	 * create the indexes
	 * 
	 * @param Tx_CzSimpleCal_Domain_Model_Event $event
	 */
	protected function doCreate($event) {
		if(!$event->isEnabled()) {
			return;
		}
		// get all recurrances...
		foreach($event->getRecurrances() as $recurrance) {
			// ...and store them to the repository
			$instance = Tx_CzSimpleCal_Domain_Model_EventIndex::fromArray(
				$recurrance
			);
			
			$this->eventIndexRepository->add(
				$instance
			);
		}
	}
	
	/**
	 * get an event object by its uid
	 * 
	 * @param integer $id
	 * @throws InvalidArgumentException
	 * @return Tx_CzSimpleCal_Domain_Model_Event
	 */
	protected function fetchEventObject($id) {
		$event = $this->eventRepository->findOneByUidEverywhere($id);
		if(empty($event)) {
			throw new InvalidArgumentException(sprintf('An event with uid %d could not be found.', $id));
		}
		return $event;
	}
}