<?php
use PHPUnit\Framework\TestCase;
require_once('core/php/ParejasManager.php');
require_once('core/php/DataBaseManager.php');

class ParejasManagerTest extends TestCase 
{
    /*Método que devuelve el mock de DataBaseManager
    cuando se utiliza el método insertQuery */
    private function getDataBaseManagerInsertMock($query){
        $mysqliMock = $this->createMock(mysqli::class);

        $mysqliMock->method('set_charset')
                    ->with('utf8')
                    ->will($this->returnValue(true));

        $mysqliMock->expects($this->once())
                    ->method('query')
                    ->will($this->returnValue(true));
        /*Crear el mock mysqli */
        /*Crear el mock dataBaseManagerMock */
        $dataBaseManagerMock = $this->getMockBuilder('DataBaseManager')
        ->setMethods(array('insertQuery'))
        ->enableOriginalConstructor()
        ->setConstructorArgs(array($mysqliMock))
        ->enableProxyingToOriginalMethods()
        ->getMock();

        $dataBaseManagerMock->expects($this->once())
                    ->method('insertQuery')
                    ->with($query)
                    ->will($this->returnValue(true));
        return $dataBaseManagerMock;
    }
    /*Método que devuelve el mock de DataBaseManager
    cuando se utiliza el método realizeQuery */
    private function getDataBaseManagerRealizeMock($expectedResult, $query){
        /*Crear el mock mysqli */
        $mysqliMock = $this->createMock(mysqli::class);

        $mysqliMock->method('set_charset')
                    ->with('utf8')
                    ->will($this->returnValue(true));

        $mysqliMock->expects($this->once())
                    ->method('query')
                    ->will($this->returnCallback(
                        function() use ($expectedResult) {
                            $results = $expectedResult;
                            $mysqli_result = $this->getMockBuilder('mysqli_result')
                                                ->disableOriginalConstructor()
                                                ->getMock();
                            $mysqli_result->expects($this->any())
                                            ->method('fetch_all')
                                            ->will($this->returnValue($results));
                            return $mysqli_result;
                        }
                    )
                    );
                /*Crear el mock mysqli */
        $dataBaseManagerMock = $this->getMockBuilder('DataBaseManager')
        ->setMethods(array('realizeQuery'))
        ->enableOriginalConstructor()
        ->setConstructorArgs(array($mysqliMock))
        ->enableProxyingToOriginalMethods()
        ->getMock();

        $dataBaseManagerMock->expects($this->once())
                    ->method('realizeQuery')
                    ->with($query)
                    ->will($this->returnValue(json_encode($expectedResult)));
        
        return $dataBaseManagerMock;
    
    
    }

    /*Método que devuelve el mock de DataBaseManager
    cuando no se usa ninguna funcion de DataBaseManager */
    private function getDataBaseManagerInsertMockIncorrect(){
        $mysqliMock = $this->createMock(mysqli::class);

        $mysqliMock->method('set_charset')
                    ->with('utf8')
                    ->will($this->returnValue(true));

        /*Crear el mock mysqli */
        /*Crear el mock dataBaseManagerMock */
        $dataBaseManagerMock = $this->getMockBuilder('DataBaseManager')
        ->enableOriginalConstructor()
        ->setConstructorArgs(array($mysqliMock))
        ->enableProxyingToOriginalMethods()
        ->getMock();

        return $dataBaseManagerMock;
    }
    /*Método que devuelve el mock de DataBaseManager
    cuando se utiliza el método insertQuery caso NEGATIVO
    devuelve false al query insert */
    private function getDataBaseManagerInsertMockIncorrect2($query){
        $mysqliMock = $this->createMock(mysqli::class);

        $mysqliMock->method('set_charset')
                    ->with('utf8')
                    ->will($this->returnValue(true));

        /*Crear el mock mysqli */
        /*Crear el mock dataBaseManagerMock */
        $dataBaseManagerMock = $this->getMockBuilder('DataBaseManager')
        ->setMethods(array('insertQuery'))
        ->enableOriginalConstructor()
        ->setConstructorArgs(array($mysqliMock))
        ->enableProxyingToOriginalMethods()
        ->getMock();

        $dataBaseManagerMock->expects($this->once())
                    ->method('insertQuery')
                    ->with($query)
                    ->will($this->returnValue(false));
        return $dataBaseManagerMock;
    }

    /*Método que devuelve el mock de DataBaseManager
    cuando se utiliza el método realizeQuery  caso NEGATIVO
    Se realiza el query pero devuelve 0 resultados*/
    private function getDataBaseManagerRealizeMockIncorrect($expectedResult, $query){
        /*Crear el mock mysqli */
        $mysqliMock = $this->createMock(mysqli::class);

        $mysqliMock->method('set_charset')
                    ->with('utf8')
                    ->will($this->returnValue(true));

        $mysqliMock->expects($this->once())
                    ->method('query')
                    ->will($this->returnCallback(
                        function() use ($expectedResult) {
                            $results = $expectedResult;
                            $mysqli_result = $this->getMockBuilder('mysqli_result')
                                                ->disableOriginalConstructor()
                                                ->getMock();
                            $mysqli_result->expects($this->any())
                                            ->method('fetch_all')
                                            ->will($this->returnValue($results));
                            return $mysqli_result;
                        }
                    )
                    );
                /*Crear el mock mysqli */
        $dataBaseManagerMock = $this->getMockBuilder('DataBaseManager')
                                    ->setMethods(array('realizeQuery'))
                                    ->enableOriginalConstructor()
                                    ->setConstructorArgs(array($mysqliMock))
                                    ->enableProxyingToOriginalMethods()
                                    ->getMock();

        $dataBaseManagerMock->expects($this->once())
                    ->method('realizeQuery')
                    ->with($query)
                    ->will($this->returnValue(0));
        
        return $dataBaseManagerMock;
    }

    //Testcase 1-1 getPareja correct
    public function testGetPareja() {
        $expectedResult = array(
            array(
                'id' => '1',
                'id_materia' => '1',
                'concepto' => 'concepto1',
                'descripcion' => 'descripcion1'
            )
        );
        /*Entradas*/
        $idMateria=1;
        $id=1;  

        $query = "SELECT concepto,descripcion, FROM parejas WHERE id='$id' AND idmateria= '$idMateria'";

        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMock($expectedResult, $query);    

        $parejasManajer = new ParejasManager($dataBaseManagerMock);
        $result = $parejasManajer->getPareja($idMateria, $id);
        $expectedResult_encoded = json_encode($expectedResult);
        $this->assertEquals($expectedResult_encoded, $result);
    }
    
    //Testcase 1-2 getPareja incorrect
    public function testGetParejaIncorrect() {
        /*Devuelve resultado nulo, sin resultados para el query */
        $expectedResult = array();
        /*Entradas*/
        $idMateria=1;
        $id=1; 

        $query = "SELECT concepto,descripcion, FROM parejas WHERE id='$id' AND idmateria= '$idMateria'";
        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMockIncorrect($expectedResult, $query);

        $parejasManajer = new ParejasManager($dataBaseManagerMock, $query);
        $result = $parejasManajer->getPareja($idMateria, $id);
        $expectedResult_encoded = json_encode($expectedResult);
        /**Si devuelve 0 se confirma que las filas del query fueron 0 */
        $this->assertEquals(0, $result);
    }

    //Testcase 2-1 setPareja correct
    public function testSetPareja() {
        /**Argumentos */
        $idMateria = 1;
        $concepto='concepto1';
        $descripcion='descripcion1';
        $query = "INSERT INTO parejas (concepto,descripcion,idmateria) VALUES('$concepto','$descripcion','$idMateria')";

        $dataBaseManagerMock = $this->getDataBaseManagerInsertMock($query);
        $puntajesManajer = new ParejasManager($dataBaseManagerMock);
        $result = $puntajesManajer->setPareja($idMateria,$concepto, $descripcion);
        /**Si devuelve TRUE el query fue correcto*/
        $this->assertEquals(true, $result);
    }
    
    //Testcase 2-2 setPareja incorrect
    public function testSetParejaIncorrect() {
        /**Argumentos $concepto es null */
        $idMateria = 1;
        $concepto=null;
        $descripcion='descripcion1';
        $query = "INSERT INTO parejas (concepto,descripcion,idmateria) VALUES('$concepto','$descripcion','$idMateria')";

        $dataBaseManagerMock = $this->getDataBaseManagerInsertMockIncorrect();
        $puntajesManajer = new ParejasManager($dataBaseManagerMock);
        $result = $puntajesManajer->setPareja($idMateria,$concepto, $descripcion);
        /**Si devuelve null se confirma que un argumento era nullo*/
        $this->assertEquals(null, $result);
    }

    //Testcase 3-1 updatePareja correct
    public function testUpdatePareja() {
        /**Argumentos */
        $id=1;
        $idMatter = 1;
        $concept='concepto1';
        $definition='descripcion1';
        $query = "UPDATE parejas set idmateria = '$idMatter' , concepto = '$concept' , descripcion = '$definition' WHERE id=" . intval($id);

        $dataBaseManagerMock = $this->getDataBaseManagerInsertMock($query);
        $puntajesManajer = new ParejasManager($dataBaseManagerMock);
        $result = $puntajesManajer->updatePareja($id, $idMatter,$concept, $definition);
        /**Se confirma que el query fue exitoso devolviendo true */
        $this->assertEquals(true, $result);
    }

    //Testcase 3-2 updatePareja incorrect
    public function testUpdateParejaIncorrect() {
        /*Entrada, el idMateria es string */
        $id=1;
        $idMatter ='idMateria';
        $concept='concepto2';
        $definition='descripcion1';
        $query = "UPDATE parejas set idmateria = '$idMatter' , concepto = '$concept' , descripcion = '$definition' WHERE id=" . intval($id);

        $dataBaseManagerMock = $this->getDataBaseManagerInsertMockIncorrect2($query);
        $puntajesManajer = new ParejasManager($dataBaseManagerMock);
        $result = $puntajesManajer->updatePareja($id,$idMatter,$concept, $definition);
        /**Si devuelve false se confirma que la ejeccuión del query fue errónea false*/
        $this->assertEquals(false, $result);
    }

    //Testcase 4-1 deletePareja correct
    public function testDeletePareja() {
        $id=1;
        $idMateria = 1;
        $query = "DELETE FROM parejas WHERE id='$id' AND idmateria='$idMateria'";

        $dataBaseManagerMock = $this->getDataBaseManagerInsertMock($query);
        $puntajesManajer = new ParejasManager($dataBaseManagerMock);
        $result = $puntajesManajer->deletePareja($id, $idMateria);
        $this->assertEquals(true, $result);
    }
    //Testcase 4-2 deletePareja incorrect
    public function testDeleteParejaIncorrect() {
        /*Entradas idMateria es string */
        $id=100;
        $idMateria = "idMateria";
        $query = "DELETE FROM parejas WHERE id='$id' AND idmateria='$idMateria'";

        $dataBaseManagerMock = $this->getDataBaseManagerInsertMockIncorrect2($query);
        $puntajesManajer = new ParejasManager($dataBaseManagerMock);
        $result = $puntajesManajer->deletePareja($id,$idMateria);
        /**Si devuelve false se confirma que la ejeccuión del query fue errónea false*/
        $this->assertEquals(false, $result);
    }

    //Testcase 5-1 getAllParejasTheMateria correct
    public function testGetAllParejasTheMateria() {
        $expectedResult = array(
            array(
                'concepto' => 'concepto1',
                'descripcion' => 'descripcion1'
            ),
            array(
                'concepto' => 'concepto2',
                'descripcion' => 'descripcion2'
            )
        );
        /*Entradas*/
        $idMateria=1;    
        $query = "SELECT concepto,descripcion FROM parejas WHERE idmateria = $idMateria";

        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMock($expectedResult, $query);
        $parejasManajer = new ParejasManager($dataBaseManagerMock);
        $result = $parejasManajer->getAllParejasTheMateria($idMateria);
        $expectedResult_encoded = json_encode($expectedResult);
        $this->assertEquals($expectedResult_encoded, $result);
    }

    //Testcase 5-2 getAllParejasTheMateria Incorrect 
    public function testGetAllParejasTheMateriaIncorrect() {
        /*Devuelve 0 porque no hay ningún resultado*/
        $expectedResult = array();
        /*Entradas*/
        $idMateria=2;
        $query = "SELECT concepto,descripcion FROM parejas WHERE idmateria = $idMateria";

        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMockIncorrect($expectedResult, $query);


        $parejasManajer = new ParejasManager($dataBaseManagerMock);
        $result = $parejasManajer->getAllParejasTheMateria($idMateria);
        /*Devuelve 0 porque no hay ningún resultado*/
        $this->assertEquals(0, $result);
    }

    //Testcase 6-1 getAllParejas correct
    public function testGetAllParejas() {
        $expectedResult = array(
            array(
                'id' => '1',
                'id_materia' => '1',
                'concepto' => 'concepto1',
                'descripcion' => 'descripcion1'
            ),
            array(
                'id' => '2',
                'id_materia' => '1',
                'concepto' => 'concepto2',
                'descripcion' => 'descripcion2'
            )
        );        
        /*Entradas*/

        $query = "SELECT * FROM parejas";

        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMock($expectedResult, $query);
        $parejasManager = new ParejasManager($dataBaseManagerMock);
        $result = $parejasManager->getAllParejas();
        $expectedResult2 = array(array(
            array(
                'id' => '1',
                'idMatter' => '1',
                'concept' => 'concepto1',
                'definition' => 'descripcion1'
            ),
            array(
                'id' => '2',
                'idMatter' => '1',
                'concept' => 'concepto2',
                'definition' => 'descripcion2'
            )
        ));
        $expectedResult_encoded = json_encode($expectedResult2);
        $this->assertEquals($expectedResult_encoded, $result);
    }

    //Testcase 6-2 getAllParejas Incorrect Ningun resultado
    public function testGetAllParejasIncorrect() {
        /*Entradas*/
        $query = "SELECT * FROM parejas";

        $expectedResult = array();
        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMock($expectedResult, $query);
        
        $parejasManajer = new ParejasManager($dataBaseManagerMock);
        $result = $parejasManajer->getAllParejas();
        /*Devuelve 0 porque no hay ningún resultado*/
        $this->assertEquals(0, $result);
    }
}