<?php
	
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
		*/
		private $row = [];

		/*
		* $table will be assigned at creation time, this way it'll be accessable by
		* the script at any given time.
		* This way you can also target the correct database table
		*/
		private $table;

		public function __construct($query, $table)
		{

			if (!is_string($query)) 
			{
				exit("Exit reason: Argument one passed at construct is not of type: String");
			}

			$this->table = $table;

			$this->fireQuery($query);
		}

		/*
		* setField initiates the column you wish to set
		*/
		public function setField($key, $value)
		{

			$this->row[$key] = $value;

		}

		/*
		* getField retrieves the requested column
		*/
		public function getField($key)
		{

			/*
			* if key exists, return the requested key
			* else return nothing (silence).
			*/
			if ($this->hasField($key)) 
			{
				return $this->row[$key];
			}
			else
			{
				return;
			}

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
			if (!empty($_POST)) 
			{

				foreach ($_POST as $key => $value) 
				{

					$this->row[$key] = $value;

				}

				$this->fireQuery();

			}

		}

		private function fireQuery($sql = NULL)
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

				/*
				* SQL commands
				*/
				$select = $haystack["select"];
				$update = $haystack["update"];
				$insert = $haystack["insert"];
				$delete = $haystack["delete"];

				$explodedSQL = explode(" ", $sql);
				$sqlCommand = $explodedSQL[0];

				//$completedQuery = $this->conn->prepare($sql);
				
				/*
				* There has been a SELECT statement
				* Retrieve data
				* else do whatever the query has set
				*/
				if(strpos($sqlCommand, $select) > -1)
				{
					echo "working SELECT";

					/*$completedQuery->execute();

					$result = $completedQuery->get_result();

					$num_of_rows = $result->num_rows;*/

					/*
					* number of rows bigger than 0
					* loop through the rows and add it 
					*/
					/*if ($num_of_rows > 0)
					{
						# code...
					}*/

				}
				else if (strpos($sqlCommand, $update) > -1 || strpos($sqlCommand, $insert) > -1 || strpos($sqlCommand, $delete) > -1)
				{

					//$completedQuery->execute();

				}
				else
				{

					exit("Query failed. No INSERT, SELECT, UPDATE, DELETE set in query. The given query selector is: {$sqlCommand}");

				}
			}
		}

		/*
		* function hasField checks whether the array_key exists which was requested.
		* it adds no benefits, besides less code.
		*/
		private function hasField($key)
		{

			return in_array($key, array_keys($this->row));

		}

	}


$recordTest = new Recordset("UPDATE", "table");
?>