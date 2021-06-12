"use strict";

/*
* ImageCreator will create images and return the set image
*/
class ImageCreator
{
	constructor(startValues)
	{
		/*
		* instantiate the required properties
		*/
		this.createErrorElements();

		this._startValues = startValues;

		this.hasOwnProperty({
			"userInput": null,
			"extension": [],
			"distortion": false
		});

		try {
			if (!startValues.hasOwnProperty(`realInput`) || startValues[`realInput`] === ``) {
				throw `The following property hasn't been set: realInput`;
			}

			let realInput = document.getElementById(startValues[`realInput`]) || document.getElementsByName(startValues[`realInput`])[0];

			if (realInput == undefined) {
				throw `Could not find an input field with ID or name: ${startValues[`realInput`]}`;
			}




			if (!startValues.hasOwnProperty(`appendToElement`) || startValues[`appendToElement`] === ``) {
				throw `The following property hasn't been set: appendToElement`;
			}

			let appendToElement = document.getElementById(startValues[`appendToElement`]) || document.getElementsByName(startValues[`appendToElement`])[0];

			if (appendToElement == undefined) {
				throw `Could not find an element with ID or name: ${startValues[`appendToElement`]}`;
			}

			//based on name or ID the object element is retrieved and assigned
			this._appendToElement = appendToElement;
			this._realInput = realInput;

		} catch (error) {
			console.log(this._divError);
			this._divError.innerHTML = error;
			this._body.appendChild(this._divError);
			return;
		}

		this.activate();
	}

	createErrorElements() {
		this._body = document.getElementsByTagName(`body`)[0];
		this._divError = document.createElement(`div`);
		this._divError.style.height = `100vh`;
		this._divError.style.width = `100vw`;
	}

	hasOwnProperty(dynamicObject) {
		let $this = this;
		let property;
		Object.keys(dynamicObject).forEach(function(dynamicObjectKey, index) {
			property = `_${dynamicObjectKey}`;
			console.log(property);

			if ($this._startValues.hasOwnProperty(`${dynamicObjectKey}`)) {
				console.log(property);
				$this[property] = $this._startValues[dynamicObjectKey];
				console.log($this[property]);
			} else {
				$this[property] = dynamicObject[dynamicObjectKey];
			}
		});
		delete this._startValues;
		console.log(this);
	}

	/*
	* set allowed extensions for images/files
	*/
	set allowedExtension(extensions)
	{
		let allowedExtensionsArray;

		try {
			allowedExtensionsArray = extensions.split(",");
		} catch (error) {
			allowedExtensionsArray = extensions;
		}

		for (let i = 0; i < allowedExtensionsArray.length; i++) 
		{
			this._extension.push(allowedExtensionsArray[i]);
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
	* if distortion is set to true, it will convert the names into numbers
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

	input() {
		let $this = this;
		/*
		* Check whether the "fake" input is set
		* If it has been set, use it as a replacement for the real input field
		* and fire the click() event on the real input
		* else use the real input as the input field to instantiate the file "uploader"
		*/
		if (this._userInput != undefined) 
		{
			this._userInput.addEventListener(`click`, function() {
	
				this._realInput.click();
	
				this._realInput.addEventListener(`change`, function() {
					// $this EMULATES THE ACTUAL CLASS INSTANTIATION
					// REFERING TO "THIS" IMMEDIATELY, WILL TARGET realInputSelector
					$this.render(this._realInput);
				});
				
			});
		} else
		{
			console.log(this._realInput);
			this._realInput.addEventListener(`change`, function() {
				$this.render(this._realInput);
			});		
		}
	}

	activate()
	{

		this.input();
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