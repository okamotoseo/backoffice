
<?php 
class SearchCriteriaModel
{

	

	/**
	 * Array with filedName, fielValue and conditionType
	 * @var array()
	 */
	public $filters;
	
	/**
	 * Result
	 * @var array();
	 */
	public $searchCriteria;
	
	
	
	
	
	
	
	
	public function __construct($filters = null)
	{
		 
		if(isset($filters)){
			 
			$this->filters = $filters;
			
			$this->getSearchCriteria();
				
		}
		 
	}

	
	/**
	 *	CONDITION	NOTES
	 *	eq			Equals.
	 *	finset		A value within a set of values
	 *	from		The beginning of a range. Must be used with to
	 *	gt			Greater than
	 *	gteq		Greater than or equal
	 *	in			In. The value can contain a comma-separated list of values.
	 *	like		Like. The value can contain the SQL wildcard characters when like is specified.
	 *	lt			Less than
	 *	lteq		Less than or equal
	 *	moreq		More or equal
	 *	neq			Not equal
	 *	nfinset		A value that is not within a set of values
	 *	nin			Not in. The value can contain a comma-separated list of values.
	 *	notnull		Not null
	 *	null		Null
	 *	to			The end of a range. Must be used with from
	 */
	public function getSearchCriteria(){
	
		if(!isset($this->filters)){
			return $this->searchCriteria =  array();
		}
	
		foreach($this->filters as $i => $criteria){
				
			$filters[] = array('field' => $criteria['field'] , 'value' => $criteria['value'], 'condition_type' => $criteria['condition_type']);
				
		}
		unset($this->searchCriteria);
		$this->searchCriteria['search_criteria']['filter_groups'][] = array('filters' => $filters);
	
		return $this->searchCriteria;
	
	}

}

