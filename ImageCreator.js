"use strict";

/*
* ImageCreator will create images and return the set image
*/
class ImageCreator
{
	constructor(_realInput, _userInput)
	{
		/*
		* instantiate the required properties
		*/
		this._userInput = _userInput;
		this._realInput = _realInput;
		this._extension = [];
		this._nameChanger = false;

		this.activate(_realInput, _userInput);
	}

	/*
	* set allowed extensions for images/files
	*/
	set allowedExtension(extensions)
	{
		let allowedExtensions = extensions.split(",");

		for (let i = 0; i < allowedExtensions.length; i++) 
		{
			this._extension.push(allowedExtensions[i]);
		}		

	}

	/*
	* Return the extensions which were set
	* in case the developer ever requires to know the allowed extentions from the object
	* Convert the array to a string, so the developer can output it instantly instead of having to loop through it
	*/
	get allowedExtension()
	{
		let arrayToString = this._extension.join();
		return arrayToString;
	}

	/*
	* if nameChanger is set to true, it will convert the names into numbers
	* this way injections will be stopped
	* if however set to false, it'll use the default image name as name
	*/
	set nameChanger(value)
	{
		if (value === true || value === false) 
		{
			this._nameChanger = value;
		}
	}

	activate()
	{
		/*
		* Check whether the "fake" input is set
		* If it has been set, use it as a replacement for the real input field
		* and fire the click() event on the real input
		* else use the real input as the input field to instantiate the file "uploader"
		*/
		if (_userInput != undefined) 
		{
			document.getElementById(_userInput).addEventListener("click", function() {

				document.getElementById(_realInput).click();

				document.getElementById(_realInput).addEventListener("change", function() {
					//let object = ImageCreator.getImages(this);
					console.log("rrr");
				});
				
			});
		} else
		{
			console.log(_realInput);
			document.getElementById(_realInput).addEventListener("change", function() {
				//let object = new ImageCreator(this);
				console.log("fired");
			});		
		}
	}

	/*
	* getImages will create the images and return them
	* set it to static so the user can call it from outside the class aswell
	* in case ever required for a gallery in which the data comes from an AJAX response
	* rather then an input field
	*/
	static getImages()
	{

	}
}
















function setImageCreator(realInput, userInput)
{
	if (userInput != undefined) 
	{
		document.getElementById(userInput).addEventListener("click", function() {

			if (realInput == undefined)
			{
				return "The given input was not valid.";
			} else 
			{
				document.getElementById(realInput).click();

				document.getElementById(realInput).addEventListener("change", function() {
					//let object = new ImageCreator(this);
					console.log("rrr");
				});
			}
		});
	} else
	{
		console.log(realInput);
		document.getElementById(realInput).addEventListener("change", function() {
			//let object = new ImageCreator(this);
			console.log("fired");
		});		
	}
}

//SOME FUCKING FUNCTION FOR MY JOKES
function findInOrderSuccessor(arr) {

	//OPTIONAL, KIND OF OVERKILL. IN CASE ARRAY IS EMPTY
	if (arr.length === 0) {
		return null;
	}

	let input = arr.pop();

	if (arr.length === 0) {
		return input;
	}

	let compareAgainstInput = arr.pop();
	
	if (input > compareAgainstInput) {
		array.unshift(input);

		var val = findInOrderSuccessor(arr);
	}

	if (input < compareAgainstInput) {
		let comp = findInOrderSuccessor(array.unshift(input));

		if (compareAgainstInput < comp) {
			return compareAgainstInput;
		} else if (compareAgainstInput > comp) {
			return comp;
		} else {
			return compareAgainstInput;
		}
	}

	if (val === input) {
		return null;
	}
	return val;
}