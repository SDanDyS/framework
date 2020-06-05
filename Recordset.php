<?php
	require_once "Connection.php";


	/*
	*Recordset is the database class, which will do the CRUD for you
	* DELETE
	* UPDATE
	* INSERT
	* SELECT
	*/
	class Recordset 
	{
		/*
		* $row will keep the database table its column names
		* $rowArray will keep the fetched/inserted data
		* $index decides what the count will be for $rowArray
		* $table sets the table name
		* $conn sets the connection for the table to retrieve/insert from
		* $errorSuppression "decides" whether errors should be thrown, such as undefined etc.
		*/
		private $row = [];

		private $typeOfRow = [];

		private $rowArray = [];

		private $index = -1;

		private $errorSuppression;

		private $conn;

		private $types = [];

		/*
		* $table will be assigned at creation time, this way it'll be accessable by
		* the script at any given time.
		* This way you can also target the correct database table
		*/
		private $table;

		public function __construct($query, $table, $errorSuppression = true)
		{
			$this->conn = Connection::setConnection();

			$this->errorSuppression = $errorSuppression;

			$this->table = $table;

			$this->getTableColumns();

			if (!empty($query)) 
			{
				$this->executeQuery($query);
			}
		}

		/*
		* call save() to start the CRUD process
		*/
		public function save($mixture = NULL)
		{
			/*
			* Check in what way the request was send
			* Based on the REQUEST method, request the method to save
			* If for some reason the method does not exist, call suppressionCaller
			* NOTICE: Send an argument along to save, to fetch both GET and POST.
			* Send a hierarchy argument along, to decide which one should overwrite
			*/
			$action = "saveBy{$_SERVER['REQUEST_METHOD']}";

			if (is_null($mixture))
			{
				if (method_exists($this, $action))
				{
					$this->$action();
				} else
				{
					$this->suppressionCaller(__METHOD__, $action);
				}			
			} else
			{
				$this->getMixedMETHOD($mixture);
			}
		}

		private function getMixedMETHOD($mixture)
		{
			$fetchMethods = ["POST/GET" => "saveByPOSTMixture", "GET/POST" => "saveByGETMixture", "POST" => "saveByPOST", "GET" => "saveByGET"];

			$request = $mixture;

			if(array_key_exists($request, $fetchMethods))
			{
				$action = $fetchMethods[$request];

				$this->$action();
			} else
			{
				$this->getSuppressionCaller(__METHOD__, $mixture);
			}
		}

		private function saveByPOSTMixture()
		{
			/*
			* Combination between GET and POST
			* In which the $_POST method is the leading method
			*/
			if (count($_POST) > 0) 
			{
				foreach ($_POST as $key => $value) 
				{
					if ($this->hasField($key, $this->row))
					{
						if (isset($_GET[$key]))
						{
							$value = "{$_POST[$key]}{$_GET[$key]}";
						}
						$this->setField($key, $value);
					}
					$this->executeQuery();
				}
			}
		}

		private function saveByGETMixture()
		{
			/*
			* Combination between GET and POST
			* In which the $_GET method is the leading method
			*/
			if (count($_GET) > 0)
			{
				foreach ($_GET as $key => $value) 
				{
					if ($this->hasField($key, $this->row))
					{
						if (isset($_POST[$key]))
						{
							$value = "{$_GET[$key]}{$_POST[$key]}";
						}
						$this->setField($key, $value);
					}
				}
				$this->executeQuery();
			}		
		}

		/*
		* if $_POST is not empty, start looping through it to assign keys and values to write to database
		*/
		private function saveByPOST()
		{
			if (count($_POST) > 0) 
			{
				foreach ($_POST as $key => $value) 
				{
					if ($this->hasField($key, $this->row))
					{
						$this->setField($key, $value);
					}
				}
				$this->executeQuery();
			}	
		}

		/*
		* if $_GET is not empty, start looping through it to assign keys and values to write to database
		*/
		private function saveByGET()
		{
			if (count($_GET) > 0)
			{
				foreach ($_GET as $key => $value)
				{
					if ($this->hasField($key))
					{
						$this->setField($key, $value);
					}
				}
				$this->executeQuery();
			}
		}

		private function setImages()
		{
			if (count($_FILES) > 0) 
			{
				foreach($_FILES as $singleFile)
				{
					$name = $singleFile["name"];
					$tmpName = $singleFile["tmp_name"];
					$type = $singleFile["type"];
					$size = $singleFile["size"];
					$error = $singleFile["error"];

					if (!empty($name))
					{
					//WORK ON THIS TOMORROW. WORKING ON UPLOADING FILES
					}
				}		
			}
		}

		private function getTableColumns()
		{
			/*
			* Retrieve the column names and types when method executeQuery is fired
			*/
			$columnRetriever = $this->conn->prepare("SHOW COLUMNS FROM `{$this->table}`");

			$columnRetriever->execute();

			/*
			* Store the fetched result
			*/
			$result = $columnRetriever->get_result();
			
			/*
			* Store the retrieved information of the columns
			*/
			while ($row = $result->fetch_assoc()) 
			{
				$this->row[$row["Field"]]["Field"] = $row["Field"];
				$this->row[$row["Field"]]["Type"] = $row["Type"];
				$this->row[$row["Field"]]["Null"] = $row["Null"];
				$this->row[$row["Field"]]["Key"] = $row["Key"];
				$this->row[$row["Field"]]["Default"] = $row["Default"];
				$this->row[$row["Field"]]["Extra"] = $row["Extra"];

				$this->setField($row["Field"], "");
			}		
		}

		private function getPrimaryKey()
		{
			$uniqueID = NULL;
			foreach ($this->row as $entryArray) 
			{
				if ($entryArray["Key"] === "PRI")
				{
					$uniqueID = $entryArray["Field"];
				}
			}
			return $uniqueID;
		}

		private function executeQuery($sql = NULL)
		{

			if (!is_null($sql)) 
			{
				$haystack = 
				[
					"select" => "SELECT",
					"update" => "UPDATE",
					"insert" => "INSERT",
					"delete" => "DELETE"
				];

				$sqlExplosion = explode(" ", $sql);
				$sqlAction = $sqlExplosion[0];

				
			// 	* SQL commands

			 	$select = $haystack["select"];
			 	$insert = $haystack["insert"];
			 	$update = $haystack["update"];
			 	$delete = $haystack["delete"];


				$completedQuery = $this->conn->prepare($sql);

				/*
				* There has been a SELECT statement
				* Retrieve data
				* else do whatever the query has set
				*/
				if($sqlAction === $select)
				{

					$completedQuery->execute();

					$result = $completedQuery->get_result();

					$num_of_rows = $result->num_rows;

					/*
					* loop through the rows and add it
					* if the resultset is 0, there is no need to fetch data, it's simply set to fetch the keys 
					*/
					if ($num_of_rows > 0) 
					{

						$this->setIndex();

						while ($row = $result->fetch_assoc())
						{
							foreach ($row as $key => $columnName) 
							{
								$this->rowArray[$this->index][$key] = $row[$key];
							}

							/*
							* increment $this->index with 1.
							*/
							$this->next();
						}

						/*
						* Reset $this->index, so during fetch time $this->index starts at 0.
						*/
						$this->resetIndex();
					}
					
				} else
				{
					/*
					* execute the query if it does not contain a SELECT statement
					*/
					$completedQuery->execute();

					/*
					* if the query contains an update or insert, retrieve the unique key. 
					*/
					if ($sqlAction === $insert || $sqlAction === $update)
					{
						/*
						* call the primary key
						* if it is the primary key, set the field to the fetched ID
						*/
						$uniqueID = $this->getPrimaryKey();
						$this->setField($uniqueID, $completedQuery->insert_id);
					}
				}
			} else
			{
				$this->selectQuery();
			}
		}

		private function selectQuery()
		{
			$requiredRow = NULL;
			
			$uniqueID = $this->getPrimaryKey();

			/*
			* if the primary key is set, but there is no value given to it, set to 0
			* this will ensure the query won't fail.
			*/
			if ($this->getField($uniqueID) == "" || is_null($this->getField($uniqueID)) || $this->getField($uniqueID) === "undefined")
			{
				$this->setField($uniqueID, 0);
			}
			
			$sql = "SELECT * FROM `{$this->table}` WHERE `{$uniqueID}` = ?";

			$selectQuery = $this->conn->prepare($sql);

			/*
			* Set all the type arguments to string
			* This is done, because type jugling at the time of writing could not be done by the developer
			*/
			$selectQuery->bind_param("s", $uniqueSelector);

			$uniqueSelector = $this->getField($uniqueID);

			$selectQuery->execute();

			$result = $selectQuery->get_result();

			$num_of_rows = $result->num_rows;

			if ($num_of_rows > 0) 
			{
				$this->updateQuery();
			} else
			{
				$this->insertQuery();
			}
		}

		private function insertQuery()
		{
			/*
			* Instantiate variables
			*/
			$createQuery = NULL;
			$placeholders = NULL;
			$bindPARAM = NULL;
			$completeSet = NULL;
			$counter = 0;

			/*
			* Array to push values which will be bound later on
			* This way SQL injection is prevented
			*/
			$args = [];

			$createQuery = "INSERT INTO `{$this->table}` (";

			/*
			* Set index count to 0
			*/
			$this->setIndex();

			/*
			* Start looping through the required elements and create a query string
			*/
			foreach($this->rowArray[$this->index] as $key => $value)
			{
				/*
				* If the primary key equals the key in the loop
				* If yes, return the loop and go on with the next key
				*/
				if ($this->getPrimaryKey() == $key)
				{
					$counter++;
					continue;
				}
				/*
				* If the required field is empty, set to NULL
				*/
				if ($value === "")
				{
					$this->setField($key, NULL);
				}

				/*
				* Check whether the end of the loop has been reached
				* Yes, start closing the query string
				* No, keep the query string open
				*/
				if ($counter === count($this->rowArray[$this->index]) - 1) 
				{
					$placeholders .= "?)";
					$createQuery .= "{$key}";
				} else
				{
					$placeholders .= "?,";
					$createQuery .= "{$key},";
				}

				/*
				* Types for bind_param();
				*/
				$bindPARAM .= "s";

				/*
				* Values / References pushed
				*/
				$args[] = $value;

				$counter++;
			}
			
			$createQuery .= ") VALUES (";

			/*
			* Create the complete query string
			*/
			$completeSet = "{$createQuery}{$placeholders}";

			/*
			* Create the query object
			*/
			$stmt = $this->conn->prepare($completeSet);

			/*
			* Bind the argument array and unpack it
			*/
			$stmt->bind_param($bindPARAM, ...$args);

			/*
			* Execute the created SQL object
			*/
			$stmt->execute();

			/*
			* Return inserted key and assign it to its respective field
			*/
			$this->setField($this->getPrimaryKey(), $stmt->insert_id);

			/*
			* Reset $this->index, so during fetch time $this->index starts at 0.
			*/
			$this->resetIndex();
		}

		private function updateQuery()
		{
			/*
			* Instantiate variables
			*/
			$createQuery = NULL;
			$bindPARAM = NULL;
			$counter = 0;

			/*
			* Array to push values which will be bound later on
			* This way SQL injection is prevented
			*/
			$args = [];

			$createQuery = "UPDATE `{$this->table}` SET";

			/*
			* Set index count to 0
			*/
			$this->setIndex();

			/*
			* Start looping through the required elements and create a query string
			*/
			foreach ($this->rowArray[$this->index] as $key => $value)
			{

				/*
				* Types for bind_param();
				*/
				$bindPARAM .= "s";
				
				/*
				* If the primary key equals the key in the loop
				* If yes, return the loop and go on with the next key
				*/
				if ($this->getPrimaryKey() == $key)
				{
					$counter++;
					continue;
				}

				/*
				* Values / References pushed
				*/
				$args[] = $value;

				/*
				* Check whether the end of the loop has been reached
				* Yes, start closing the query string
				* No, keep the query string open
				*/
				if ($counter === count($this->rowArray[$this->index]) - 1)
				{
					$createQuery .= " {$key} = ? ";
					$createQuery .= "WHERE {$this->getPrimaryKey()} = ?";

					/*
					* Get the primary key and its value
					* Push it so the WHERE clause can be completed
					*/
					$args[] = $this->getField("{$this->getPrimaryKey()}");
				} else
				{
					$createQuery .= " {$key} = ?, ";
				}

				$counter++;
			}

			/*
			* Create the query object
			*/
			$stmt = $this->conn->prepare($createQuery);

			/*
			* Bind the argument array and unpack it
			*/
			$stmt->bind_param($bindPARAM, ...$args);

			/*
			* Execute the created SQL object
			*/
			$stmt->execute();

			/*
			* Reset $this->index, so during fetch time $this->index starts at 0.
			*/
			$this->resetIndex();
		}

		/*
		* setField initiates the column you wish to set
		*/
		public function setField($key, $value)
		{
			$this->setIndex();

			if ($this->hasField($key, $this->row))
			{
				$this->rowArray[$this->index][$key] = $value;
			}
		}

		/*
		* getField retrieves the requested column
		*/
		public function getField($key)
		{
			$this->setIndex();
			// return $this->row;
			/*
			* if key exists, return the requested key
			* else return nothing (silence).
			* returning empty will prevent PHP from throwing an error.
			*/
			if ($this->hasField($key, $this->row)) 
			{
				return $this->rowArray[$this->index][$key];
			}
			else
			{
				$this->getSuppressionCaller(__METHOD__, $key);
			}
		}

		public function getRow($key = NULL)
		{
			if (is_null($key)) 
			{
				return $this->rowArray;
			}
			else if (array_key_exists($key, $this->rowArray))
			{
				return $this->rowArray[$key];
			}
			else
			{
				$this->getSuppressionCaller(__METHOD__, $key);
			}
		}

		//MOVE METHOD TO DEDICATED ERROR HANDLING CLASS.
		private function getSuppressionCaller($error, $clause)
		{
			if ($this->errorSuppression) 
			{
				return;
			}
			else
			{
				exit("{$error} <br/> Developer input: {$clause} <br/> Developer input failed. Check manual for further instructions.");
			}
		}

		/*
		* function hasField checks whether the array_key exists which was requested.
		* it is an expansion of in_array(). 
		* the function will check whether the multidimensional array contains the value.
		*/
		private function hasField($key, $haystack, $strict = false)
		{
			foreach ($haystack as $item) {

				if (($strict ? $item === $key : $item == $key) || (is_array($item) && $this->hasField($key, $item, $strict))) 
				{
					return true;
				}
    		}

    		return false;
		}

		public function next()
		{
			$this->index = $this->index + 1;

			return $this->index;
		}

		public function previous()
		{
			$this->index = $this->index - 1;

			return $this->index;
		}

		private function setIndex()
		{
			if ($this->index === -1) 
			{
				$this->index = $this->index + 1;

				return $this->index;
			}
		}

		private function resetIndex()
		{
			if ($this->index > -1) 
			{
				$this->index = $this->index = -1;

				return $this->index;
			}
		}

	}

//INSERT INTO `test` (t1) VALUES('2')
$id = $_GET["test_id"] ?? 0;
	$recordTest = new Recordset("SELECT * FROM `test` WHERE test_id = '{$id}'", "test");
	//echo $recordTest->getField("testVAR");
	//echo $recordTest->getField("testINT");

if (count($_POST) > 0)
{
	$recordTest->save();
	//echo $recordTest->getField("testVAR");
	//echo $recordTest->getField("testINT");
	header("Location: Recordset.php?test_id={$recordTest->getField('test_id')}");
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>

	<style>
	body {
		color: white;
	}
	</style>
</head>
<body style="background-color: black;">
	<form method="POST">
		<input type="text" name="testVAR" value=<?php echo "{$recordTest->getField('testVAR')}"; ?> >
		<input type="number" name="testINT" value=<?php echo "{$recordTest->getField('testINT')}"; ?> >
		<button type="submit">submit</button>
	</form>
</body>
</html>