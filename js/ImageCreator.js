"use strict";

/*
* ImageCreator will create images and return the set image
*/
class ImageCreator
{
	//_realInput, _userInput = undefined, 
	constructor(startValues)
	{
		/*
		* instantiate the required properties
		*/
		this._body = document.getElementsByTagName(`body`)[0];
		this._divError = document.createElement(`div`);
		this._divError.style.height = `100vh`;
		this._divError.style.width = `100vw`;


		this._startValues = startValues;

		// if (startValues.hasOwnProperty(`userInput`)) {
		// 	this._userInput = startValues[`userInput`];
		// } else {
		// 	this._userInput = null;
		// }

		// if (startValues.hasOwnProperty(`extension`)) {
		// 	this.allowedExtension = startValues[`extension`];
		// } else {
		// 	this._extension = [];
		// }

		// if (startValues.hasOwnProperty(`distortion`)) {
		// 	this.distortion = startValues[`distortion`];
		// } else {
		// 	this._distortion = false;
		// }

		let t = {
			"realInput": "yo"
		};
		this.hasOwnProperty({
			"realInput": false
		});

		// try {
		// 	if (!startValues.hasOwnProperty(`realInput`) || startValues[`realInput`] === ``) {
		// 		throw `The following property hasn't been set: realInput`;
		// 	}

		// 	let realInput = document.getElementById(startValues[`realInput`]) || document.getElementsByName(startValues[`realInput`])[0];

		// 	if (realInput == undefined) {
		// 		throw `Could not find an input field with ID or name: ${startValues[`realInput`]}`;
		// 	}
		// 	this._realInput = startValues[`realInput`];
		// } catch (error) {
		// 	console.log(this._divError);
		// 	this._divError.innerHTML = error;
		// 	this._body.appendChild(this._divError)
		// }

		this.activate();
	}

	hasOwnProperty(dynamicObject) {
		let $this = this;
		let property;
		Object.keys(dynamicObject).forEach(function(dynamicObjectKey, index) {
			property = `_${dynamicObjectKey}`;
			console.log(property);

			if ($this._startValues.hasOwnProperty(`${dynamicObjectKey}`)) {
				console.log(property);
				$this.property = $this._startValues[dynamicObjectKey];
			} else {
				$this.property = dynamicObject[dynamicObjectKey]
			}
		});

		console.log(this);
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
	set distortion(value)
	{
		/**
		 * === is strict operator
		 * Therefore we emulate a "bool" type.
		 */
		if (value === true || value === false) 
		{
			this._distortion = value;
		}
	}

	render(inputObject) {
		if (inputObject.files && inputObject.files[0]) {
			for (let i = 0; i < inputObject.files.length; i++) {
				let reader = new FileReader();
				
				reader.onload = function(e) {
					document.getElementById(`yes`).setAttribute(`src`, e.target.result);
				}
	
				reader.readAsDataURL(inputObject.files[i]);
			}
		}
	}

	input(realInput, userInput = undefined) {
		console.log(realInput);
		var userInputSelector = document.getElementById(userInput) || document.getElementsByName(userInput)[0] || userInput;
		var realInputSelector = document.getElementById(realInput) || document.getElementsByName(realInput)[0] || realInput;
		var $this = this;
		/*
		* Check whether the "fake" input is set
		* If it has been set, use it as a replacement for the real input field
		* and fire the click() event on the real input
		* else use the real input as the input field to instantiate the file "uploader"
		*/
		if (userInputSelector != undefined) 
		{
			userInputSelector.addEventListener(`click`, function() {
	
				realInputSelector.click();
	
				realInputSelector.addEventListener(`change`, function() {
					// $this EMULATES THE ACTUAL CLASS INSTANTIATION
					// REFERING TO "THIS" IMMEDIATELY, WILL TARGET realInputSelector
					$this.render(realInputSelector);
				});
				
			});
		} else
		{
			console.log(realInputSelector);
			realInputSelector.addEventListener(`change`, function() {
				$this.render(realInputSelector);
			});		
		}
	}

	activate()
	{
		var userInputSelector = document.getElementById(this._userInput) || document.getElementsByName(this._userInput)[0];
		var realInputSelector = document.getElementById(this._realInput) || document.getElementsByName(this._realInput)[0];
		/*
		* Check whether the "fake" input is set
		* If it has been set, use it as a replacement for the real input field
		* and fire the click() event on the real input
		* else use the real input as the input field to instantiate the file "uploader"
		*/
		if (this._userInput != undefined) 
		{
			this.input(realInputSelector, userInputSelector);
		} else
		{
			console.log(realInputSelector);
			this.input(realInputSelector);
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