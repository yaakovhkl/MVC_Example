<?php

/**
 * CsvDataBase
 *
 * CRUD options with CSV files.
 *
 * This class create folders in DB_HOST_PATH location as database and in folders csv files as tables.
 *
 * The class giving option to Create or drop table or database with public static functions.
 *
 * To implement the functions Read Update and Delete must create object instance of this class to access table.
 *
 */
class CsvDataBase {

	const FILE_EXTENSION = '.csv';

	private $tablePath;

	/**
	 * Set the path of the table.
	 *
	 * @param string $DB db name
	 * @param string $Table table name
	 *
	 * @throws Exception if db or table not exist
	 */
	function __construct( $DB, $Table ) {

		if ( ! self::dataBaseExist( $DB ) ) {
			throw new Exception( 'The Database not exist!' );
		} elseif ( ! self::tableExist( $DB, $Table ) ) {
			throw new Exception( 'The Table not exist!' );
		}
		$this->tablePath = DB_HOST_PATH . $DB . '/' . $Table . self::FILE_EXTENSION;
	}

	/**
	 * Return columns names from table.
	 *
	 * @return array columns
	 */
	public function getColumns() {

		$table   = fopen( $this->tablePath, 'r' );
		$columns = fgetcsv( $table );
		fclose( $table );

		return $columns;
	}

	/**
	 * Insert rows to table.
	 *
	 * @param array $new_rows in every item array row that has column_name => value
	 *
	 * note: all columns is required
	 *
	 * @return bool
	 */
	public function insert( array $new_rows ) {

		if ( $this->checkNewInsert( $new_rows ) ) {
			/** @var array $data */
			$data = $this->readTable();
			$data = $data ? $data : [ ];
			/* Marge old data whit new data*/
			foreach ( $new_rows as $new_row ) {
				array_push( $data, $new_row );
			}

			return $this->writeTable( $data );
		} else {
			return false;
		}
	}

	/**
	 * Check if all columns set.
	 *
	 * @param array $new_rows new rows to inserted
	 *
	 * @return bool
	 */
	private function checkNewInsert( array $new_rows ) {

		$columns = $this->getColumns();
		foreach ( $new_rows as $new_row ) {
			foreach ( $columns as $column ) {
				if ( ! isset( $new_row[ $column ] ) ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Update data by columns (option for conditions by value of the column).
	 *
	 * @param array $updates by column => value
	 *
	 * note: allow update only in existing columns if in $updates has other column the function return false
	 *
	 * @param array $where optional, condition, only one item by column_name => value
	 *
	 * @param bool $isUniqueColumn optional, if this column value not exist at all insert this row.
	 *
	 * @return bool success or not
	 */
	public function update( array $updates, array $where = [ ], $isUniqueColumn = false ) {

		$columns = $this->getColumns();

		if ( $data = $this->readTable() ) {
			/** @var array $data */
			if ( $where ) {

				/* Prevent more than one condition*/
				$where_column_name  = array_keys( $where )[0];
				$where_column_value = $where[ $where_column_name ];
				if ( $isUniqueColumn ) {
					if ( ! $this->checkUniqueColumn( $data, $where_column_name, $where_column_value ) ) {
						$this->insert( [ $updates ] );

						return true;
					}
				}
				for ( $i = 0; $i < count( $data ); $i ++ ) {

					if ( $data[ $i ][ $where_column_name ] == $where_column_value ) {
						foreach ( $updates as $column => $value ) {
							if ( ! in_array( $column, $columns ) ) {

								return false;
							}
							$data[ $i ][ $column ] = $value;
						}
					}
				}
			} else {
				for ( $i = 0; $i < count( $data ); $i ++ ) {
					foreach ( $updates as $column => $value ) {
						if ( ! in_array( $column, $columns ) ) {

							return false;
						}
						$data[ $i ][ $column ] = $value;
					}
				}
			}

			return $this->writeTable( $data );
		} elseif ( $isUniqueColumn ) {
			/* If is unique column and not data insert the updates.*/
			$this->insert( [ $updates ] );
		} else {
			return false;
		}
	}

	/**
	 * Check if has in unique column value as is given.
	 *
	 * @param array $data table content
	 * @param string $uniqueColumnName
	 * @param string $uniqueColumnValue
	 *
	 * @return bool
	 */
	private function checkUniqueColumn( $data, $uniqueColumnName, $uniqueColumnValue ) {

		for ( $i = 0; $i < count( $data ); $i ++ ) {
			if ( $data[ $i ][ $uniqueColumnName ] == $uniqueColumnValue ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Delete rows by condition.
	 *
	 * @param array $where condition, only one item by column_name => value
	 *
	 * @return bool success or not
	 */
	public function delete( array $where ) {

		if ( $data = $this->readTable() ) {
			/* Prevent more than one condition*/
			$where_column_name  = array_keys( $where )[0];
			$where_column_value = $where[ $where_column_name ];
			/** @var array $data */
			$data_size = count( $data );
			for ( $i = 0; $i < $data_size; $i ++ ) {
				if ( $data[ $i ][ $where_column_name ] == $where_column_value ) {
					unset( $data[ $i ] );
				}
			}

			return $this->writeTable( $data );
		} else {
			return false;
		}
	}

	/**
	 * Select rows from table (option for conditions by value of the column);
	 *
	 * @param array $where optional condition, only one item by column_name => value
	 *
	 * @return mixed array if has result ,bool false if not
	 */
	public function select( array $where = [ ] ) {

		if ( $data = $this->readTable() ) {
			/* Prevent more than one condition*/
			if ( $where ) {
				$where_column_name  = array_keys( $where )[0];
				$where_column_value = $where[ $where_column_name ];
				/** @var array $data */
				$data_size = count( $data );
				for ( $i = 0; $i < $data_size; $i ++ ) {
					if ( $data[ $i ][ $where_column_name ] == $where_column_value ) {
						unset( $data[ $i ] );
					}
				}
			}

			return $data;
		} else {
			return false;
		}
	}

	/**
	 * Return full data from table.
	 *
	 * @return mixed array data if success, false if not
	 */
	private function readTable() {

		$data    = false;
		$columns = $this->getColumns();
		$table   = fopen( $this->tablePath, 'r' );
		$index   = - 1;
		while( $row = fgetcsv( $table ) ) {
			if ( $index == - 1 ) {
				$index ++;
				continue;
			}
			for ( $i = 0; $i < count( $row ); $i ++ ) {
				$data[ $index ] [ $columns[ $i ] ] = $row[ $i ];
			}
			$index ++;
		}

		fclose( $table );

		return $data;
	}

	/**
	 * Write array of table data to csv file.
	 *
	 * @param array $data table data to write in table
	 *
	 * @return bool success or not
	 */
	private function writeTable( array $data ) {

		$columns = $this->getColumns();
		$table   = fopen( $this->tablePath, 'w+' );
		if ( ! fputcsv( $table, $columns ) ) {
			fclose( $table );

			return false;
		}
		foreach ( $data as $item ) {
			$row = [ ];
			for ( $i = 0; $i < count( $columns ); $i ++ ) {
				$row[ $columns[ $i ] ] = $item[ $columns[ $i ] ];
			}
			if ( ! fputcsv( $table, $row ) ) {
				fclose( $table );

				return false;
			}
		}
		fclose( $table );

		return true;
	}

	/**
	 * Check if database exist.
	 *
	 * @param string $DB database name
	 *
	 * @return bool
	 */
	public static function dataBaseExist( $DB ) {

		if ( file_exists( DB_HOST_PATH . $DB ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Create folder in DB_HOST_PATH this folder is database in which will the tables.
	 *
	 * @param string $DB database name
	 *
	 * @return bool success or not
	 */
	public static function createDataBase( $DB ) {

		if ( mkdir( DB_HOST_PATH . $DB ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Remove database folder from DB_HOST_PATH, before remover all tables to can remove folder.
	 *
	 * @param string $DB database name
	 *
	 * @return bool success or not
	 */
	public static function dropDataBase( $DB ) {

		self::dropAllTables( $DB );
		if ( rmdir( DB_HOST_PATH . $DB ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Drop all files from db folder, is used to drop database because can't remove folder if it has files.
	 *
	 * @param string $DB database name
	 */
	private static function dropAllTables( $DB ) {

		$tables = glob( DB_HOST_PATH . $DB . '/' . '*' );
		if ( $tables ) {
			foreach ( $tables as $table ) {
				unlink( $table );
			}
		}
	}

	/**
	 * Check if table exist.
	 *
	 * @param string $DB database name
	 * @param string $Table table name
	 *
	 * @return bool
	 */
	public static function tableExist( $DB, $Table ) {

		if ( self::dataBaseExist( $DB ) && file_exists( DB_HOST_PATH . $DB . '/' . $Table . self::FILE_EXTENSION ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Create csv file in database folder this file is a table.
	 *
	 * @param string $DB database name
	 * @param string $Table table name
	 * @param array $columns in every item column name
	 *
	 * @return bool success or not
	 */
	public static function createTable( $DB, $Table, array $columns ) {

		if ( $table = fopen( DB_HOST_PATH . $DB . '/' . $Table . self::FILE_EXTENSION, 'w+' ) ) {

			fputcsv( $table, $columns );

			fclose( $table );

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Remove table file.
	 *
	 * @param string $DB database name
	 * @param string $Table table name
	 *
	 * @return bool success or not
	 */

	public static function dropTable( $DB, $Table ) {

		if ( unlink( DB_HOST_PATH . $DB . '/' . $Table . self::FILE_EXTENSION ) ) {
			return true;
		} else {
			return false;
		}
	}
}