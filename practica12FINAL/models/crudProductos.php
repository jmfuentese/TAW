<?php
	require_once("conexion.php");

	class DatosProd extends Conexion{
		public static function productRegistrationModel($table,$datos){
			$statement = Conexion::conectar()->prepare("INSERT INTO $table(nombre,descripcion,precio_compra,precio_venta,precio) VALUES (:nombre,:descripcion,:precioC,:precioV,:precioP)");
			$statement->bindParam(":nombre",$datos["nombre"],PDO::PARAM_STR);
			$statement->bindParam(":descripcion",$datos["descripcion"],PDO::PARAM_STR);
			$statement->bindParam(":precioC",$datos["precio_compra"],PDO::PARAM_INT);
			$statement->bindParam(":precioV",$datos["precio_venta"],PDO::PARAM_INT);
			$statement->bindParam(":precioP",$datos["precio"],PDO::PARAM_INT);
			if($statement->execute()){
				return "1";
			}else{
				return "0";
			}
			//$statement->close();
		}

		public static function registerUserModel($table, $datos){
            $statement = Conexion::conectar()->prepare(
                "INSERT INTO $table(first_name,last_name,usuario,password,user_email,date_added) 
                                VALUES (:nombre,:apellido,:usuario,:password,:email,:date_add)");
            $statement->bindParam(":nombre",$datos["nombre"],PDO::PARAM_STR);
            $statement->bindParam(":apellido",$datos["apellido"],PDO::PARAM_STR);
            $statement->bindParam(":usuario",$datos["usuario"],PDO::PARAM_INT);
            $statement->bindParam(":password",md5($datos["password"]),PDO::PARAM_INT);
            $statement->bindParam(":email",$datos["email"],PDO::PARAM_INT);
            $statement->bindParam(":date_add",$datos["date"],PDO::PARAM_INT);
            if($statement->execute()){
                return true;
            }else{
                return false;
            }
        }

        public static function registerCategoryModel($table, $datos){
            $statement = Conexion::conectar()->prepare(
                "INSERT INTO $table(nombre_categoria,descripcion_categoria,date_added) 
                                VALUES (:nombre,:descripcion,:date_add)");
            $statement->bindParam(":nombre",$datos["nombre"],PDO::PARAM_STR);
            $statement->bindParam(":descripcion",$datos["descripcion"],PDO::PARAM_STR);
            $statement->bindParam(":date_add",$datos["date"],PDO::PARAM_INT);
            if($statement->execute()){
                return true;
            }else{
                return false;
            }
        }

        public static function registerProductModel($table, $datos){
            $statement = Conexion::conectar()->prepare(
                "INSERT INTO $table(codigo_producto,nombre,date_added, precio_producto, cantidad_stock, id_categoria) 
                                VALUES (:codigo, :nombre,:date_add, :precio, :stock, :categoria)");
            $statement->bindParam(":codigo",$datos["codigo"],PDO::PARAM_STR);
            $statement->bindParam(":nombre",$datos["nombre"],PDO::PARAM_STR);
            $statement->bindParam(":date_add",$datos["date"],PDO::PARAM_INT);
            $statement->bindParam(":precio",$datos["precio"],PDO::PARAM_STR);
            $statement->bindParam(":stock",$datos["stock"],PDO::PARAM_STR);
            $statement->bindParam(":categoria",$datos["categoria"],PDO::PARAM_STR);
            if($statement->execute()){
                return true;
            }else{
                return false;
            }
        }

        public static function deleteProductModel($table, $idP){
		    $statement = Conexion::conectar()->prepare("DELETE FROM $table WHERE id = :id");
            $statement->bindParam(":id",$idP,PDO::PARAM_STR);
            if($statement->execute()){
                return true;
            }else{
                return false;
            }
        }

        public static function deleteCategoryModel($table, $idC){
            $statement = Conexion::conectar()->prepare("DELETE FROM $table WHERE id_categoria = :id");
            $statement->bindParam(":id",$idC,PDO::PARAM_STR);
            if($statement->execute()){
                return true;
            }else{
                return false;
            }
        }

        public static function deleteUserModel($table, $idU){
            $statement = Conexion::conectar()->prepare("DELETE FROM $table WHERE id = :id");
            $statement->bindParam(":id",$idU,PDO::PARAM_STR);
            if($statement->execute()){
                return true;
            }else{
                return false;
            }
        }

        public static function addStockModel($table, $stock, $idP){
		    $nota = "El usuario ".$_SESSION["usuario"]. " ha agregado ".$stock." al producto con id: ".$idP;
		    $date = date("Y-m-d h:i:s");
		    $currentStock = self::getStockModel($idP);
		    $newStock = (int)$stock + (int)$currentStock["cantidad_stock"];
            self::historialAdd($idP, $nota, $date, $stock);
		    $statement = Conexion::conectar()->prepare("UPDATE $table SET cantidad_stock = :newStock WHERE id = :id");
            $statement->bindParam(":id",$idP,PDO::PARAM_STR);
            $statement->bindParam(":newStock",$newStock,PDO::PARAM_STR);
            if($statement->execute()){
                return true;
            }else{
                return false;
            }
        }

        public static function historialAdd($idP,$nota, $date,$stock){
            $stmt = Conexion::conectar()->prepare("INSERT INTO historial (id_producto, nota, usuario,fecha,cantidad) VALUES 
                                                              (:idP, :nota, :usuario, :fecha, :cantidad");
            $stmt->bindParam(":idP",$idP,PDO::PARAM_STR);
            $stmt->bindParam(":nota",$nota,PDO::PARAM_STR);
            $stmt->bindParam(":usuario",$_SESSION["usuario"],PDO::PARAM_STR);
            $stmt->bindParam(":fecha",$date,PDO::PARAM_STR);
            $stmt->bindParam(":cantidad",$stock,PDO::PARAM_STR);
            $stmt->execute();
        }

        public static function delStockModel($table, $stock, $idP){
            $currentStock = self::getStockModel($idP);
            $newStock = (int)$currentStock["cantidad_stock"] - (int)$stock;
            $statement = Conexion::conectar()->prepare("UPDATE $table SET cantidad_stock = :newStock WHERE id = :id");
            $statement->bindParam(":id",$idP,PDO::PARAM_STR);
            $statement->bindParam(":newStock",$newStock,PDO::PARAM_STR);
            if($statement->execute()){
                return true;
            }else{
                return false;
            }
        }

        public static function getStockModel($idP){
            $statement = Conexion::conectar()->prepare("SELECT * FROM productos WHERE id = :id");
            $statement->bindParam(":id",$idP,PDO::PARAM_STR);
            $statement->execute();
            #fetch(): Obtiene una fila de un conjunto de resultados asociado al objeto PDOStatement.
            return $statement->fetch();
        }

        public static function loginModel($table, $datos){
            $statement = Conexion::conectar()->prepare("SELECT * FROM $table WHERE usuario = :user AND password = :pass");
            $statement->bindParam(":user", $datos["usuario"],PDO::PARAM_STR);
            $statement->bindParam(":pass", $datos["password"],PDO::PARAM_STR);
            $statement->execute();
            #fetch(): Obtiene una fila de un conjunto de resultados asociado al objeto PDOStatement.
            return $statement->fetch();
            //$statement->close();
        }

        public static function userListModel($table){
            $statement = Conexion::conectar()->prepare("SELECT * FROM $table");
            $statement->execute();

            return $statement->fetchAll();
        }

        public static function categoryListModel($table){
            $statement = Conexion::conectar()->prepare("SELECT * FROM $table");
            $statement->execute();

            return $statement->fetchAll();
        }

        public static function productsListModel($table){
            $statement = Conexion::conectar()->prepare("SELECT * FROM $table");
            $statement->execute();

            return $statement->fetchAll();
        }

        public static function getProductModel($table, $idP){
            $statement = Conexion::conectar()->prepare("SELECT * FROM $table WHERE id = :id");
            $statement->bindParam(":id", $idP, PDO::PARAM_INT);
            $statement->execute();

            return $statement->fetch();
        }

        public static function getUserModel($table){
            $statement = Conexion::conectar()->prepare("SELECT * FROM $table");
            $statement->execute();

            return $statement->fetch();
        }

        public static function getCategoryModel($table, $id){
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $table WHERE id_categoria = :id");
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch();
        }

        public static function getCategoryByNameModel($table, $nombre){
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $table WHERE nombre_categoria = :nombre");
            $stmt->bindParam(":nombre", $nombre, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch();
        }

        public static function updateUserModel($table, $datos){
            $stmt = Conexion::conectar()->prepare("UPDATE $table SET nombre = :nombre, apellido = :apellido, usuario = :usuario, 
                                                  email = :email WHERE id = :id");

            $stmt->bindParam(":usuario", $datos["usuario"], PDO::PARAM_STR);
            $stmt->bindParam(":password", $datos["password"], PDO::PARAM_STR);
            $stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
            $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);

            if($stmt->execute()){

                return "success";

            }

            else{

                return "error";

            }
        }
	}
?>