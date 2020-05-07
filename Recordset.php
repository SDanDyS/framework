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

		/*
		* $table will be assigned at creation time, this way it'll be accessable by
		* the script at any given time.
		* This way you can also target the correct database table
		*/
		private $table;

		public function __construct($query, $table, $errorSuppression = true, $databaseConnection = "local")
		{

			if (!is_string($query)) 
			{
				$this->getSuppressionCaller(__METHOD__, $query);
			}
			$this->conn = Connection::setConnection($databaseConnection);
			$this->table = $table;
			$this->errorSuppression = $errorSuppression;

			$this->executeQuery($query);
		}

		/*
		* call save(), to insert, select, delete, update
		*/
		public function save()
		{
			/*
			* if $_POST is not empty, start looping through it to assign keys and values to write to database
			* if it IS empty, abort action and let the code execute and finish without calling any methods
			* note: do NOT exit the script. This could lead to code breaking or having undesired behavior.
			*/
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

		private function executeQuery($sql = NULL)
		{
			/*
			* Retrieve the column names and types when method executeQuery is fired
			*/
			$columnRetriever = $this->conn->prepare("SHOW COLUMNS FROM {$this->table}");

			$columnRetriever->execute();

			/*
			* Store the fetched result
			*/
			$result = $columnRetriever->get_result();
			

			while ($row = $result->fetch_assoc()) 
			{
				echo $this->row[$row["Field"]]["Field"] = $row["Field"];
				$this->row[$row["Field"]]["Type"] = $row["Type"];
				$this->row[$row["Field"]]["Null"] = $row["Null"];
				$this->row[$row["Field"]]["Key"] = $row["Key"];
				$this->row[$row["Field"]]["Default"] = $row["Default"];
				$this->row[$row["Field"]]["Extra"] = $row["Extra"];
			}

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
					$completedQuery->execute();

					if ($sqlAction === $insert || $sqlAction === $update)
					{
						$completedQuery->insert_id;
					}
				}
			} else
			{

			}
		}

		private function selectQuery()
		{
			$uniqueID = NULL;
			$uniqueRow = NULL;

			foreach ($this->row as $entryArray) 
			{
				if ($entryArray["Key"] === "PRI")
				{
					$uniqueID = $entryArray["Field"];
				}
			}

			$uniqueRow = $this->row[$uniqueID];

			if ($this->getField($uniqueID) == "") 
			{
				$this->setField($uniqueID, 0);
			}
			
			$sql = "SELECT * FROM `{$this->table}` WHERE {$uniqueID} = ?";

			$selectQuery = $this->conn->prepare($sql);

			$selectQuery->bind_param("i", $uniqueSelector);

			$uniqueSelector = $this->getField($uniqueID);

			$selectQuery->execute();

			$result = $selectQuery->get_result();

			$num_of_rows = $result->num_rows;

			if ($num_of_rows > 0) 
			{
				$this->update();
			} else
			{
				$this->insert();
			}
		}

		private function insertQuery()
		{
			$createQuery = NULL;
			$createQuery = "INSERT INTO `{$this->table}` (";
			foreach ($_POST as $key => $value)
			{
				$createQuery .= $key;
			}
			$createQuery .= ") VALUES (";

			$this->setIndex();

			foreach ($this->rowArray[$this->index] as $key => $value) 
			{
				# code...
			}
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
		* it adds no benefits, besides less code.
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
$recordTest = new Recordset("SELECT * FROM `test` WHERE id = 0", "test");

// foreach($recordTest->getField("t1") as $k => $v)
// {
// 	echo "{$k}: {$v} <br/>";
// }
$t = $recordTest->getRow();
//echo $recordTest->getField("t3");
var_dump($t);
?>