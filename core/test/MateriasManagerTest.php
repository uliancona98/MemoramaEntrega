<?php
use PHPUnit\Framework\TestCase;
require_once('core/php/MateriasManager.php');
require_once('core/php/DataBaseManager.php');

class MateriasManagerTest extends TestCase 
{
    /*Método que devuelve el mock de DataBaseManager
    cuando se utiliza el método insertQuery */
    private function getDataBaseManagerInsertMock($query){
        $dataBaseManagerMock = $this->getMockBuilder('DataBaseManager')
        ->disableOriginalConstructor()
        ->getMock();
        $dataBaseManagerMock->expects($this->once())
                    ->method('insertQuery')
                    ->with($query)
                    ->will($this->returnValue(true));
        return $dataBaseManagerMock;
    }

    private function getDataBaseManagerRealizeMock($expectedResult, $query){
        $dataBaseManagerMock = $this->getMockBuilder('DataBaseManager')
        ->disableOriginalConstructor()
        ->getMock();

        $dataBaseManagerMock->expects($this->once())
                    ->method('realizeQuery')
                    ->with($query)
                    ->will($this->returnValue($expectedResult));
        
        return $dataBaseManagerMock;
    }

    private function getDataBaseManagerFailedInsertMock($query){
        $dataBaseManagerMock = $this->getMockBuilder('DataBaseManager')
        ->disableOriginalConstructor()
        ->getMock();

        $dataBaseManagerMock->expects($this->once())
                    ->method('insertQuery')
                    ->with($query)
                    ->will($this->returnValue(false));
        return $dataBaseManagerMock;
    }

    private function getDataBaseManagerEmptyRealizeMock($query){
        $dataBaseManagerMock = $this->getMockBuilder('DataBaseManager')
        ->disableOriginalConstructor()
        ->getMock();

        $dataBaseManagerMock->expects($this->once())
                    ->method('realizeQuery')
                    ->with($query)
                    ->will($this->returnValue(array()));
        
        return $dataBaseManagerMock;
    }

    public function testGetMateria(){
        $expectedResult = array(
            array(
                'id' => '1',
                'nombre' =>'Fisica'
            )
        );
        $idmateria = '1';
        $query = "SELECT * FROM materias WHERE id = $idmateria";
        $dbManagerMock = $this->getDataBaseManagerRealizeMock($expectedResult,$query);
        $materiasManager = new MateriasManager($dbManagerMock);
        $result = $materiasManager->getMateria($idmateria);
        $this->assertEquals(json_encode($expectedResult),$result);
    }

    public function testSetMateria(){
        $name = "Fisica";
        $query = "INSERT INTO materias (nombre) VALUES('$name')";
        $dbManagerMock = $this->getDataBaseManagerInsertMock($query);
        $materiasManager = new MateriasManager($dbManagerMock);
        $result = $materiasManager->setMateria($name);
        $this->assertEquals("",$result);
    }

    public function testUpdateMateria(){
        $name = "Matematicas";
        $id = "1";
        $query = "UPDATE materias set nombre= '$name' WHERE id =".intval($id);
        $dbManagerMock = $this->getDataBaseManagerInsertMock($query);
        $materiasManager = new MateriasManager($dbManagerMock);
        $result = $materiasManager->updateMateria($id,$name);
        $this->assertEquals("",$result);
    }

    public function testDeleteMateria(){
        $idMateria = "1";
        $query = "DELETE FROM materias WHERE id = '$idMateria'";
        $dbManagerMock = $this->getDataBaseManagerInsertMock($query);
        $materiasManager = new MateriasManager($dbManagerMock);
        $result = $materiasManager->deleteMateria($idMateria);
        $this->assertEquals("",$result);
    }

    public function testGetAllMateria(){
        $expectedResult = array(
            array(
                array(
                'id' => '1',
                'name' =>'Fisica'
                )
            )
        );
        $rawRecievedData = array(
            array(
                'id' => '1',
                'nombre' =>'Fisica'
            )
        );
        $query = "SELECT * FROM materias";
        $dbManagerMock = $this->getDataBaseManagerRealizeMock($rawRecievedData,$query);
        $materiasManager = new MateriasManager($dbManagerMock);
        $result = $materiasManager->getAllMateria();
        $this->assertEquals(json_encode($expectedResult),$result);
    }

    public function testGetMateriaFailure(){
        $expectedResult = "Tabla de materias esta vacia";
        $idmateria = '1';
        $query = "SELECT * FROM materias WHERE id = $idmateria";
        $dbManagerMock = $this->getDataBaseManagerEmptyRealizeMock($query);
        $materiasManager = new MateriasManager($dbManagerMock);
        $result = $materiasManager->getMateria($idmateria);
        $this->assertEquals($expectedResult,$result);
    }

    public function testSetMateriaFailure(){
        $name = "Fisica";
        $query = "INSERT INTO materias (nombre) VALUES('$name')";
        $dbManagerMock = $this->getDataBaseManagerFailedInsertMock($query);
        $materiasManager = new MateriasManager($dbManagerMock);
        $result = $materiasManager->setMateria($name);
        $this->assertEquals(false,$result);
    }

    public function testUpdateMateriaFailure(){
        $name = "Matematicas";
        $id = "1";
        $query = "UPDATE materias set nombre= '$name' WHERE id =".intval($id);
        $dbManagerMock = $this->getDataBaseManagerFailedInsertMock($query);
        $materiasManager = new MateriasManager($dbManagerMock);
        $result = $materiasManager->updateMateria($id,$name);
        $this->assertEquals(false,$result);
    }

    public function testDeleteMateriaFailure(){
        $idMateria = "1";
        $query = "DELETE FROM materias WHERE id = '$idMateria'";
        $dbManagerMock = $this->getDataBaseManagerFailedInsertMock($query);
        $materiasManager = new MateriasManager($dbManagerMock);
        $result = $materiasManager->deleteMateria($idMateria);
        $this->assertEquals(false,$result);
    }

    public function testGetAllMateriaEmpty(){
        $expectedResult = "tabla materia vacia";
        $query = "SELECT * FROM materias";
        $dbManagerMock = $this->getDataBaseManagerEmptyRealizeMock($query);
        $materiasManager = new MateriasManager($dbManagerMock);
        $result = $materiasManager->getAllMateria();
        $this->assertEquals($expectedResult,$result);
    }
}