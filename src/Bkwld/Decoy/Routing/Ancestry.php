<?php namespace Bkwld\Decoy\Routing;

/**
 * This class tries to figure out if the injected controller has parents
 * and who they are.
 */
class Ancestry {
	
	// DI
	$this->controller;
	
	/**
	 * Inject dependencies
	 * @param Bkwld\Decoy\Controllers\Base $controller
	 */
	public function __construct($controller) {
		$this->controller = $controller;
		
		/**
		 * DEPENDENCIES I NEED:
		 * - controller class name
		 * - the decoy dir
		 * - PARENT_MODEL
		 * - MODEL
		 * - PARENT_MODEL
		 * 
		 * THINGS THAT WILL GET MOCKED
		 * - Request
		 * - Input
		 */
	}
	
	/**
	 * Test if the current route is serviced by has many and/or belongs to.  These
	 * are only true if this controller is acting in a child role
	 * 
	 */
	public function is_child_route() {
		if (empty($this->CONTROLLER)) throw new Exception('$this->CONTROLLER not set');
		return $this->action_is_child()
			|| $this->parent_in_input()
			|| $this->acting_as_related();
	}
	
	// Test if the current route is one of the full page has many listings or a new
	// page as a child
	public function action_is_child() {
		return Request::route()->is($this->CONTROLLER.'@child')
			|| Request::route()->is($this->CONTROLLER.'@new_child')
			|| Request::route()->is($this->CONTROLLER.'@edit_child');
	}
	
	// Test if the current route is one of the many to many XHR requests
	public function parent_in_input() {
		// This is check is only allowed if the request is for this controller.  If other
		// controller instances are instantiated (like via Controller::resolve()), they 
		// were not designed to be informed by the input.  Using action[uses] rather than like
		// ->controller because I found that controller isn't always set when I need it.  Maybe
		// because this is all being invoked from the constructor.
		if (strpos(Request::route()->action['uses'], $this->CONTROLLER.'@') === false) return false;		
		return isset(Input::get('parent_controller');
	}
	
	// Test if the controller must be used in rendering a related list within another.  In other
	// words, the controller is different than the request and you're on an edit page.  Had to
	// use action[uses] because Request::route()->controller is sometimes empty.  
	// Request::route()->action['uses'] is like "admin.issues@edit".  We're also testing that
	// the controller isn't in the URI.  This would never be the case when something was in the
	// sidebar.  But without it, deducing the breadcrumbs gets confused because controllers get
	// instantiated not on their route but aren't the children of the current route.
	public function acting_as_related() {
		$handles = Bundle::option('decoy', 'handles');
		$controller_name = substr($this->CONTROLLER, strlen($handles.'.'));
		return strpos(Request::route()->action['uses'], $this->CONTROLLER.'@') === false
			&& strpos(URI::current(), '/'.$controller_name.'/') === false
			&& strpos(Request::route()->action['uses'], '@edit') !== false;
	}
	
	// Guess at what the parent controller is by examing the route or input varibles
	public function deduce_parent_controller() {
		
		// If a child index view, get the controller from the route
		if ($this->action_is_child()) {
			return Request::segment(1).'.'.Request::segment(2);
		
		// If one of the many to many xhr requests, get the parent from Input
		} elseif ($this->parent_in_input()) {
			$input = BKWLD\Laravel\Input::json_and_input();
			return $input['parent_controller'];
		
		// If this controller is a related view of another, the parent is the main request	
		} else if ($this->acting_as_related()) {
			return Request::route()->controller;
		}
	}
	
	// Guess as what the relationship function on the parent model will be
	// that points back to the model for this controller by using THIS
	// controller's name.
	// returns - The string name of the realtonship
	public function deduce_parent_relationship() {
		$handles = Bundle::option('decoy', 'handles');
		$relationship = substr($this->CONTROLLER, strlen($handles.'.'));
		if (!method_exists($this->PARENT_MODEL, $relationship)) {
			throw new Exception('Parent relationship missing, looking for: '.$relationship);
		}
		return $relationship;
	}
	
	// Guess at what the child relationship name is.  This is typically the same
	// as the parent model.  For instance, Post has many Image.  Image will have
	// a function named "post" for it's relationship
	public function deduce_child_relationship() {
		$relationship = strtolower($this->PARENT_MODEL);
		if (!method_exists($this->MODEL, $relationship)) {
			
			// Try controller name instead, in other words the plural version.  It might be
			// named this if it's a many-to-many relationship
			$handles = Bundle::option('decoy', 'handles');
			$relationship = strtolower(substr($this->PARENT_CONTROLLER, strlen($handles.'.')));
			if (!method_exists($this->MODEL, $relationship)) {
				throw new Exception('Child relationship missing on '.$this->MODEL);
			}
		}
		return $relationship;
	}
	
}