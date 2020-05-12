<?php
use PHPUnit\Framework\TestCase;
require_once('core/php/PuntajesManajer.php');
require_once('core/php/DataBaseManager.php');
class PuntajesManajerTest extends TestCase 
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
    devuelve null al query insert */
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

    //Test Case 1-1 metodo setPuntaje correcto
    public function testSetPuntajeCorrect(){
        /**Parámetros */
        $idUsuario = 1;
        $idMateria = 1;
        $fecha = '2020-04-03 03:54:59';
        $dificultad= 'media';
        $puntaje= 100;
        $foundPeers=4;

        $query = "INSERT INTO puntajes (id_usuario,id_materia,fecha,dificultad,puntaje,parejas_encontradas) VALUES('$idUsuario','$idMateria','$fecha','$dificultad',$puntaje,$foundPeers)";
        $dataBaseManagerMock = $this->getDataBaseManagerInsertMock($query);
        $puntajesManajer = new PuntajesManajer($dataBaseManagerMock);
        $result = $puntajesManajer->setPuntaje($idUsuario,$idMateria,$fecha,$dificultad,$puntaje,$foundPeers);
        /**Confirmamos que la ejecución del query sea exitoso */
        $this->assertEquals(true, $result);
    }

    //Test Case 1-2 metodo setPuntaje Incorrecto
    public function testSetPuntajeIncorrect(){
        /**Argumentos */
        $idUsuario = 1;
        $idMateria = 1;
        $fecha = '2020-04-03 03:54:59';
        $dificultad= 'media';
        $puntaje= null;
        $foundPeers=4;

        $query = "INSERT INTO puntajes (id_usuario,id_materia,fecha,dificultad,puntaje,parejas_encontradas) VALUES('$idUsuario','$idMateria','$fecha','$dificultad',$puntaje,$foundPeers)";
        $dataBaseManagerMock = $this->getDataBaseManagerInsertMockIncorrect();
        $puntajesManajer = new PuntajesManajer($dataBaseManagerMock);
        $result = $puntajesManajer->setPuntaje($idUsuario,$idMateria,$fecha,$dificultad,$puntaje,$foundPeers);
        /**Se valida que los argumentos no sean null, en este caso si hay un argumento nulo, por lo que 
         * el método regresa null sin ejecutar el query
         */
        $this->assertEquals(null, $result);
    }

    //Test Case 2-1 metodo deletePuntaje correcto
    public function testDeletePuntaje() {
        /*Entradas*/
        $idUsuario=1;
        $idMateria=2;
        $fecha='2020-04-03 03:54:59';
        $dificultad='media';
        $query = "DELETE FROM puntajes WHERE id_usuario = '$idUsuario' AND id_materia = '$idMateria' AND fecha='$fecha' AND '$dificultad'";
        
        $dataBaseManagerMock = $this->getDataBaseManagerInsertMock($query);
        $puntajesManajer = new PuntajesManajer($dataBaseManagerMock);
        $result = $puntajesManajer->deletePuntaje($idUsuario,$idMateria,$fecha,$dificultad);
        /**Se verifica que la ejecución del query fue existosa */
        $this->assertEquals(true, $result);
    }

    //Test Case 2-2 metodo deletePuntaje Incorrecto
    public function testDeletePuntajeIncorrecto() {
        /*Entradas*/
        $idUsuario=9;
        $idMateria=null;
        $fecha='2020-04-03 03:54:59';
        $dificultad='media';
        $dataBaseManagerMock = $this->getDataBaseManagerInsertMockIncorrect();
        $puntajesManajer = new PuntajesManajer($dataBaseManagerMock);
        $result = $puntajesManajer->deletePuntaje($idUsuario,$idMateria,$fecha,$dificultad);
        /**Se valida que los argumentos no sean null, en este caso si hay un argumento nulo, por lo que 
         * el método regresa null sin ejecutar el query
         */        
        $this->assertEquals(null, $result);
    }

    //Test Case 3-1 metodo getAllPuntajeForUsuario correcto
    public function testGetAllPuntajeForUsuario() {
        $expectedResult = array(
            array(
                'id_usuario' => '1',
                'id_materia' => '1',
                'fecha' => '2020-04-03 03:54:59',
                'dificultad' => 'media',
                'puntaje' => '1000',
               'parejas_encontradas' => '10'
            ),
            array(
                'id_usuario' => '1',
                'id_materia' => '1',
                'fecha' => '2020-04-03 03:50:59',
                'dificultad' => 'media',
                'puntaje' => '300',
                'parejas_encontradas' => '3'
            )
        );
        /*Entradas*/
        $idUsuario=1;
        $query = "SELECT * FROM puntajes WHERE id_usuario='$idUsuario'";

        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMock($expectedResult,$query);

        $puntajesManajer = new PuntajesManajer($dataBaseManagerMock);
        $result = $puntajesManajer->getAllPuntajeForUsuario($idUsuario);
        $expectedResult_encoded = json_encode($expectedResult);
        
        $this->assertEquals($expectedResult_encoded, $result);
    }

    //Test Case 3-2 metodo getAllPuntajeForUsuario Incorrecto
    public function testGetAllPuntajeForUsuarioIncorrecto() {
        /*Caso Negativo cuando devuelve 0 resultados */
        $expectedResult = array();
        /*Entradas*/
        $idUsuario=1;
        $query = "SELECT * FROM puntajes WHERE id_usuario='$idUsuario'";
        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMockIncorrect($expectedResult, $query);

        $puntajesManajer = new PuntajesManajer($dataBaseManagerMock);
        $result = $puntajesManajer->getAllPuntajeForUsuario($idUsuario);
        /**Si devuelve 0 se confirma que las filas del query fueron 0 */
        $this->assertEquals(0, $result);
    } 

    //Test Case 4-1 metodo getAllPuntajeForMateria correcto
    public function testgetAllPuntajeForMateriaCorrect() {
        $expectedResult = array(
            array(
                'id_usuario' => '1',
                'id_materia' => '1',
                'fecha' => '2020-04-03 03:54:59',
                'dificultad' => 'media',
                'puntaje' => '1000',
               'parejas_encontradas' => '10'
            ),
            array(
                'id_usuario' => '2',
                'id_materia' => '1',
                'fecha' => '2020-04-03 03:50:59',
                'dificultad' => 'media',
                'puntaje' => '300',
                'parejas_encontradas' => '3'
            )
        );
        /*Entradas*/
        $idMateria=2;
        $query = "SELECT * FROM puntajes WHERE id_materia='$idMateria'";

        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMock($expectedResult,$query);
        $puntajesManajer = new PuntajesManajer($dataBaseManagerMock);
        $result = $puntajesManajer->getAllPuntajeForMateria($idMateria);
        $expectedResult_encoded = json_encode($expectedResult);
        $this->assertEquals($expectedResult_encoded, $result);
    }

    //Test Case 4-1 metodo getAllPuntajeForMateria incorrecto
    public function testgetAllPuntajeForMateriaIncorrect() {
        /*Caso Negativo cuando no devuelve nada de resultados */
        $expectedResult = array();
        /*Entradas*/
        $idMateria=2;
        $query = "SELECT * FROM puntajes WHERE id_materia='$idMateria'";
        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMockIncorrect($expectedResult, $query);

        $puntajesManajer = new PuntajesManajer($dataBaseManagerMock);
        $result = $puntajesManajer->getAllPuntajeForMateria($idMateria);
        /**Si devuelve 0 se confirma que las filas del query fueron 0 */
        $this->assertEquals(0, $result);
    }

    //Test Case 5-1 metodo getAllPuntajeForUsuarioAndMateria correcto
    public function testGetAllPuntajeForUsuarioAndMateriaCorrect() {
        $expectedResult = array(
            array(
                'id_usuario' => '1',
                'id_materia' => '1',
                'fecha' => '2020-04-03 03:54:59',
                'dificultad' => 'baja',
                'puntaje' => '1000',
               'parejas_encontradas' => '10'
            ),
            array(
                'id_usuario' => '1',
                'id_materia' => '1',
                'fecha' => '2020-04-03 03:50:59',
                'dificultad' => 'media',
                'puntaje' => '300',
                'parejas_encontradas' => '3'
            )
        );
        /*Entradas*/
        $idUsuario=1;
        $idMateria=2;
        $query = "SELECT * FROM puntajes WHERE id_usuario='$idUsuario' AND id_materia='$idMateria'";

        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMock($expectedResult, $query);
        $puntajesManajer = new PuntajesManajer($dataBaseManagerMock);
        $result = $puntajesManajer->getAllPuntajeForUsuarioAndMateria($idUsuario, $idMateria);
        $expectedResult_encoded = json_encode($expectedResult);
        $this->assertEquals($expectedResult_encoded, $result);
    }
    
    //Test Case 5-2 metodo getAllPuntajeForUsuarioAndMateria Incorrecto
    public function testGetAllPuntajeForUsuarioAndMateriaIncorrect() {
        /*Caso Negativo cuando no devuelve nada de resultados */
        $expectedResult = array();
        /*Entradas*/
        $idUsuario=1;
        $idMateria=2;
        $query = "SELECT * FROM puntajes WHERE id_usuario='$idUsuario' AND id_materia='$idMateria'";

        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMockIncorrect($expectedResult, $query);

        $puntajesManajer = new PuntajesManajer($dataBaseManagerMock);
        $result = $puntajesManajer->getAllPuntajeForUsuarioAndMateria($idUsuario, $idMateria);
        /**Si devuelve 0 se confirma que las filas del query fueron 0 */
        $this->assertEquals(0, $result);
    }

    //Test Case 6-1 metodo getAllPuntajeForMateriaAndDificultadCorrect correcto
    public function testgetAllPuntajeForMateriaAndDificultadCorrect(){
        $expectedResult = array(
            array(
                'id_usuario' => '1',
                'id_materia' => '1',
                'fecha' => '2020-04-03 03:54:59',
                'dificultad' => 'media',
                'puntaje' => '1000',
               'parejas_encontradas' => '10'
            ),
            array(
                'id_usuario' => '2',
                'id_materia' => '1',
                'fecha' => '2020-04-03 03:50:59',
                'dificultad' => 'media',
                'puntaje' => '300',
                'parejas_encontradas' => '3'
            )
        );
        /*Entradas*/
        $idMateria = 1;
        $dificultad = 1;
        $query = "SELECT * FROM puntajes WHERE id_materia='$idMateria' AND dificultad='$dificultad'";

        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMock($expectedResult, $query);

        $puntajesManajer = new PuntajesManajer($dataBaseManagerMock);
        $result = $puntajesManajer->getAllPuntajeForMateriaAndDificultad($idMateria, $dificultad);
        $expectedResult_encoded = json_encode($expectedResult);
        $this->assertEquals($expectedResult_encoded, $result);
    }

    //Test Case 6-2 metodo getAllPuntajeForMateriaAndDificultadCorrect Incorrecto
    public function testGetAllPuntajeForMateriaAndDificultadIncorrect() {
        /*Caso Negativo cuando no devuelve nada de resultados */
        $expectedResult = array();
        /*Entradas*/
        $idMateria = 1;
        $dificultad = 1;
        $query = "SELECT * FROM puntajes WHERE id_materia='$idMateria' AND dificultad='$dificultad'";

        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMockIncorrect($expectedResult, $query);

        $puntajesManajer = new PuntajesManajer($dataBaseManagerMock);
        $result = $puntajesManajer->getAllPuntajeForMateriaAndDificultad($idMateria, $dificultad);
        /**Si devuelve 0 se confirma que las filas del query fueron 0 */
        $this->assertEquals(0, $result);
    }

    //Test Case 7-1 metodo getAllPuntajeForUsuarioAndMateriaAndDificultad correcto
    public function testgetAllPuntajeForUsuarioAndMateriaAndDificultadCorrect(){
        $expectedResult = array(
            array(
                'id_usuario' => '1',
                'id_materia' => '1',
                'fecha' => '2020-04-03 03:54:59',
                'dificultad' => 'media',
                'puntaje' => '1000',
               'parejas_encontradas' => '10'
            )
        );
        /*Entradas*/
        $idUsuario = 1;
        $idMateria = 2;
        $dificultad = 'medio';
        $query = "SELECT * FROM puntajes WHERE id_usuario='$idUsuario' AND id_materia='$idMateria' AND dificultad='$dificultad'";

        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMock($expectedResult, $query);

        $puntajesManajer = new PuntajesManajer($dataBaseManagerMock);
        $result = $puntajesManajer->getAllPuntajeForUsuarioAndMateriaAndDificultad($idUsuario,$idMateria,$dificultad);
        $expectedResult_encoded = json_encode($expectedResult);
        $this->assertEquals($expectedResult_encoded, $result);
    }

    //Test Case 7-2 metodo getAllPuntajeForUsuarioAndMateriaAndDificultad Incorrecto
    public function testGetAllPuntajeForUsuarioAndMateriaAndDificultadIncorrecto() {
        /*Caso Negativo cuando no devuelve nada de resultados */
        $expectedResult = array();
        /*Entradas*/
        $idUsuario = 1;
        $idMateria = 2;
        $dificultad = 'medio';
        $query = "SELECT * FROM puntajes WHERE id_usuario='$idUsuario' AND id_materia='$idMateria' AND dificultad='$dificultad'";

        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMockIncorrect($expectedResult, $query);
        $puntajesManajer = new PuntajesManajer($dataBaseManagerMock);
        $result = $puntajesManajer->getAllPuntajeForUsuarioAndMateriaAndDificultad($idUsuario,$idMateria,$dificultad);
        /**Si devuelve 0 se confirma que las filas del query fueron 0 */
        $this->assertEquals(0, $result);
    }

}