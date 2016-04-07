	/**
	 * memcache monitor javascript
	 *
	 * Javascript to toggle visibility of UI elements
	 *
	 * @author Dave Long
	 */	

	// Function to open / close hidden divs
	function toggle_hidden_div( a ) {
		var element = document.getElementById( a );
		if( element.style.display == "table" ){
			element.style.display = "none";
		}
		else{
			element.style.display = "table";
		}
	}

	// Function to open / close hidden table body
	function toggle_hidden_tbody( a ) {
		var element = document.getElementById( a );
		if( element.style.display == "table-row-group" ){
			element.style.display = "none";
		}
		else{
			element.style.display = "table-row-group";
		}
	}