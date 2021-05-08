<?php
        require 'credentials.php';
         session_start([
     'cookie_lifetime' => 86400,
         ]);
        $con = mysqli_connect($host, $user, $password, $db);
        
        
        
        function verify_connection($con){
            if(!$con)
                return "failed";
            return "success";
        }
        
        
        
        function selectData($con, $what, $from, $where, $limit){
            $limit = empty($limit) ? "" : " LIMIT $limit";
            $sql = "SELECT $what FROM $from WHERE $where$limit";
            $query = mysqli_query($con, $sql);
            
            $data = array();
            if(mysqli_num_rows($query) == 0){
                return "No Records Found";
            }
            
            while($row = mysqli_fetch_assoc($query)){
                array_push($data, $row);
            }
            return ($data);
        }
                
        function selectDataOrdered($con, $what, $from, $where, $limit){
            $limit = empty($limit) ? "" : " ORDER BY $limit DESC";
            $sql = "SELECT $what FROM $from WHERE $where$limit";
            $query = mysqli_query($con, $sql);
            $data = array();
            if(mysqli_num_rows($query) == 0){
                return "No Records Found";
            }
            
            while($row = mysqli_fetch_assoc($query)){
                array_push($data, $row);
            }
            return ($data);
        }
        
        function selectDataFK($con, $sql) {
            $query = mysqli_query($con, $sql);
            $data = array();
            if(mysqli_num_rows($query) == 0){
                return "No Records Found";
            }
            
            while($row = mysqli_fetch_assoc($query)){
                array_push($data, $row);
            }
            return ($data);
        }
        
        function insertData($con, $what, $columns, $values){
            $sql = "INSERT INTO $what ($columns) VALUES ($values)";
            if(mysqli_query($con, $sql))
                return true;
            else
                return mysqli_error($con);
        }
        function insertDatas($con, $what, $columns, $values){
            $sql = "INSERT INTO $what ($columns) VALUES $values";
            if(mysqli_query($con, $sql))
                return true;
            else
                return mysqli_error($con);
        }
        
        function updateData($con, $what, $updates, $where) {
            $sql = "UPDATE $what SET $updates WHERE $where";
            if(mysqli_query($con, $sql)) 
                return true;
            else
                return mysqli_error($con);
        }

?>