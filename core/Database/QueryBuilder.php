<?php
/**
 * core\Database\Database\Table.php
 *
 * The Table namespace is designed to handle the basic database table functions
 *
 * @author Joshua Weeks (jokka85@gmail.com)
 * @version 0.1
 * @since 0.1
 */
namespace Database\Table\QueryBuilder {

    use Database\Table\Table;

    class QueryBuilder extends Table {

        /**
         * Holds the query to be used
         * @var array
         */
        private $_query = [
            "select" => null,
            "update" => null,
            "insert" => null,
            "delete" => null,
            "join" => null,
            "on" => null,
            "where" => null,
            "between" => null,
            "orderBy" => null,
            "limit" => null
        ];

        /**
         * Arguments that will bind into the query
         * @var array
         */
        private $_args = [];

        function __construct(){
            parent::__construct();
        }

        /**
         * Start of SELECT statement. In any case where you are selecting information, this needs to be called first.
         *
         * The $select parameter can either be null (default to get all information) or an array containing the
         * fields to fetch.
         *
         * EX: ['id', 'name', 'address']
         *
         * @param null|array $select
         * @return $this
         */
        public function select($select = null){
            // CREATE THE SELECT QUERY
            $q = "SELECT ";

            for($i = 0; $i < count($select); $i++){
                $q .= $select[$i];

                if($i == (count($select) - 1)){
                    $q .= " ";
                } else {
                    $q .= ", ";
                }
            }

            if($select == null){
                $q .= "* ";
            }

            $this->_query["select"] = $q . "FROM " . $this->table . " ";

            return $this;
        }

        /**
         * This will be used when an UPDATE is needed to be done.
         *
         * @param array $set
         * @return $this
         */
        public function update($set){
            $q = '';
            if(!empty($set) || !is_null($set)) {

                $q .= "UPDATE " . $this->table . " SET ";

                $last = end($set);

                $count = 0;

                foreach ($set as $key => $value) {

                    $q .= $key . "= :update".$count;

                    $this->_args["update".$count] = $value;

                    if($last == $value){
                        $q .= " ";
                    } else {
                        $q .= ", ";
                    }

                    $count++;
                }

                $this->_query["update"] = $q;

            }

            return $this;
        }

        public function delete($identifier = null){
            if(!is_null($identifier) || !empty($identifier)) {
                $q = "DELETE FROM " . $this->table . " WHERE ";

                $last = end($identifier);
                foreach($identifier as $col => $val){
                    $q .= $col . "=" . $val;
                    $q .= ($last != $val) ? ", " : "";
                }

                $this->_query["delete"] = $q;
            }

            return $this;
        }

        /**
         * If a JOIN is needed then we can use this statement to achieve this.
         *
         * The $tables parameter is simply an array containing the tables to join. You can set these in one of two ways.
         * <pre>
         * FIRST:
         *      $tables = ["users"]
         *
         * SECOND:
         *      $tables = ["users" => "people"]
         * </pre>
         * If you do this the second way then the key is the actual table and the value is the alias.
         *
         * The $type parameter is the type of join to use. It defaults to just "JOIN" but can be set to any type such
         * as "INNER", "OUTER", "LEFT", etc..
         *
         * @param string $type
         * @param array $tables
         * @return $this
         */
        public function join($tables, $type = "JOIN"){
            $q = $type . " (";

            $lastTable = end($tables);

            foreach($tables as $table => $alias){
                if(is_int($table)){
                    $q .= $alias ." ";
                } else {
                    $q .= $table . " AS " . $alias . " ";
                }
                if($lastTable == $alias){
                    $q .= ") ";
                } else {
                    $q .= ", ";
                }

            }

            $this->_query["join"] = $q;

            return $this;
        }

        /**
         * When using the JOIN command, you can use this method to add the "ON" variables such as:
         * <pre>
             * // $query = "SELECT * FROM table JOIN table2 ";
             * $this->on(["table.id" => "table2.table_id"]);
         * </pre>
         *
         * @param array $tables
         * @return $this
         */
        public function on($tables){
            $q = "ON (";
            if(!is_null($tables)){
                for($i = 0; $i < count($tables); $i++) {
                    foreach ($tables[$i] as $t1 => $t2) {
                        $q .= $t1 . " = " . $t2 . " ";
                    }

                    $q .= ($i < (count($tables) - 1)) ? "AND " : ") ";
                }
            }
            $this->_query["on"] = $q;

            return $this;
        }

        /**
         * Adding WHERE variables to the statement. They will be prepared and then executed as they are added. The
         * general way to do this is to add in the following way:
         * <pre>
         *      $this->where([
         *              "id" => [
         *                  "=" => 1
         *                  ]
         *              ];
         * </pre>
         *
         * The layout is simple. <b>[ FIELD => [ COMPARISON_OPERATOR => VALUE ]]
         *
         * @param null|array $where
         * @return $this
         */
        public function where($where = null){
            $q = ($where == null)? "" : "WHERE ";

            if($where != null){
                for($i = 0; $i < count($where); $i++) {
                    foreach ($where[$i] as $key => $arr) {
                        $q .= " " . $key . " ";

                        foreach ($arr as $comp_op => $value) {
                            $q .= $comp_op . " :where" . $i . " ";
                            $this->_args["where" . $i] = $value;
                        }

                        if ($i < (count($where) - 1)) {
                            $q .= "AND ";
                        }
                    }
                }
            }

            $this->_query["where"] = $q;

            return $this;
        }

        /**
         * Creates a BETWEEN statement to be followed when necessary.
         *
         * @param string $field
         * Field to be searched
         *
         * @param string $from
         * Starting point to search from
         *
         * @param string $to
         * Ending point to search to
         *
         * @param bool $date
         * If we are searching dates, verify date is in proper format, if not then abandon.
         *
         * @return string $this
         */
        public function between($field, $from, $to, $date = false){
            // ADD BETWEEN STATEMENT
            $from = htmlspecialchars($from);
            $to = htmlspecialchars($to);
            if($date) {
                $fromDate = \DateTime::createFromFormat('Y-m-d', $from);
                $toDate = \DateTime::createFromFormat('Y-m-d', $to);

                $fromCheck = ($fromDate && $fromDate->format('Y-m-d') === $from) ? true : false;
                $toCheck = ($toDate && $toDate->format('Y-m-d') === $to) ? true : false;

                if(!$fromCheck || !$toCheck){
                    return $this;
                }
            }

            $this->_query["between"] = $field . " BETWEEN '". $from . "' AND '" . $to . "' ";
            return $this;
        }

        /**
         * Order preference if needed. Decides which field to order by and if DESC or ASC
         *
         * @param string $orderBy
         * Which field to order by
         *
         * @param bool $desc
         * <b>TRUE</b> if DESCENDING , <b>FALSE</b> for ASCENDING
         *
         * @return $this
         */
        public function orderBy($orderBy, $desc = true){
            $this->_query["orderBy"] = "ORDER BY " . $orderBy . " " . (($desc) ? "DESC " : "ASC ") . " ";
            return $this;
        }

        /**
         * Add a limit to the results using the $start and $end.
         *
         * @param int $start
         * @param int $end
         * @return $this
         */
        public function limit($start, $end){
            if(is_numeric($start) && is_numeric($end)) {
                $this->_query["limit"] = "LIMIT " . $start . "," . $end . " ";
            }
            return $this;
        }

        /**
         * Submits the QUERY that has been built.  The order of the query being made is simple.
         *
         * $this->select()
         *      ->join() // if join is needed
         *      ->on()  // if there is a join and ON is needed
         *      ->where()
         *      ->orderBy()
         *      ->endQuery()
         *
         * @param boolean $clear
         * Clear the query once ending it. Set to FALSE manually if you are in development and need to save the query
         * for review.
         *
         * @return null|\PDOStatement
         * Will return the \PDOStatement if processed correctly. NULL if errors.
         */
        public function endQuery($clear = true){
            $paginator = $this->getPaginator();

            // BUILD QUERY
            $q = $this->getQuery();

            if(!is_null($paginator)){
                $paginator->set($this->getConfig(), $q);
                $q = $paginator->get();
            }

            if($clear){ $this->clearQuery(); }

            // WE NOW HAVE A FULL QUERY
            return $this->db->prepAndBind($q, $this->_args);
        }

        /**
         * Returns the built query
         *
         * @return string
         */
        public function getQuery(){
            $q = null;
            foreach($this->_query as $key => $query){
                if($key == "between" && !is_null($query) && strstr($q, "WHERE") == false){
                    $q .= "WHERE ";
                }
                $q .= $query;
            }
            return $q;
        }

        /**
         * Clear the query for Re-Use
         */
        public function clearQuery(){
            $this->_query = null;
        }

    }

}