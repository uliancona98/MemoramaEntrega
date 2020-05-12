<?php
use PHPUnit\Framework\TestCase;
require_once('core/php/DataBaseManager.php');

class DataBaseManagersTest extends TestCase 
{
    /* La funcion debe empezar con la palabra test */
    /**Testcase 1-1 insertQuery correct */
    public function testInsertQueryCorrect(){
        $concepto = "concepto3";
        $descripcion = "Descripcion de concept3";
        $idmateria = 1;
        $query = "INSERT INTO parejas (concepto,descripcion,idmateria) VALUES('$concepto','$descripcion','$idmateria')";
        
        $mysqliMock = $this->createMock(mysqli::class);

        $mysqliMock->expects($this->once())
                    ->method('set_charset')
                    ->with('utf8')
                    ->will($this->returnValue(true));

        $mysqliMock->expects($this->once())
                    ->method('query')
                    ->will($this->returnValue(true));

        $dataBaseManager = new DataBaseManager($mysqliMock);
        $result = $dataBaseManager->insertQuery($query);
        $this->assertEquals(true, $result);
    }

    /**Testcase 1-2 insertQuery Incorrect */
    public function testInsertQueryIncorrect(){   
        /*Creación del mock de la clase mysqli */     
        $mysqliMock = $this->createMock(mysqli::class);

        $mysqliMock->expects($this->once())
                    ->method('set_charset')
                    ->with('utf8')
                    ->will($this->returnValue(true));

        $mysqliMock->expects($this->once())
                    ->method('query')
                    ->will($this->returnValue(false));

        $dataBaseManager = new DataBaseManager($mysqliMock);

        $concepto = "concepto3";
        $descripcion = "Descripcion de concept3";
        /*Hay un error con $query porque se omitió el atributo idmateria, por lo que el query
        va a fallar devolviendo falso */
        /*Parámetro $query */
        $query = "INSERT INTO parejas (concepto,descripcion,idmateria) VALUES('$concepto','$descripcion')";
        $result = $dataBaseManager->insertQuery($query);
        /*Se asegura que al realizar el insertQuery y 
        se tenga un parametro de un tipo diferente 
        al debido (se tiene string en vez de int)
        no se ejecute el query correctamente y devuelva falso */
        $this->assertEquals(false, $result);
    }

    /**Testcase 2-1 realizeQuery Correct */
    public function testRealizeQueryCorrect(){
        $expectedResult = [
            [
            'id' => '1',
            'nombre' => 'pepe',
            'tipo' => '0',
            'clave' => 'camello'
            ]
        ];

        $mysqliMock = $this->createMock(mysqli::class);

        $mysqliMock->expects($this->once())
                    ->method('set_charset')
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
        $name = 'pepe';
        $password = 'camello';
        /*Parámetro Query */
        $query = "SELECT * FROM usuario WHERE nombre='$name' AND clave='$password'";
        $dataBaseManager = new DataBaseManager($mysqliMock);
        $result = $dataBaseManager->realizeQuery($query);
        $this->assertEquals($expectedResult, $result);
    }
    /**Testcase 2-2 realizeQuery Inorrect */
    public function testRealizeQueryIncorrect(){
        $expectedResult = [
            [
            ]
        ];

        $mysqliMock = $this->createMock(mysqli::class);

        $mysqliMock->expects($this->once())
                    ->method('set_charset')
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

        $dataBaseManager = new DataBaseManager($mysqliMock);
        /*En el query se tiene el argumento del campo contraseña incorrecto,
        es decir la contraseña de ese usuario no es la correcta,
        por lo que no es válido y no devolverá ningún 
        resultado de la base de datos*/
        $name = 'pepe';
        /*El valor de $password es erróneo, no existe un usuario llamado así con esa contraseña */
        $password = 'c';
        /*Parámetro query */
        $query = "SELECT * FROM usuario WHERE nombre='$name' AND clave='$password'";
        $result = $dataBaseManager->realizeQuery($query);
        $this->assertEquals($expectedResult, $result);
    }
    /**Testcase 3-1 close Correct */
    public function testCloseCorrect(){
        $mysqliMock = $this->createMock(mysqli::class);

        $mysqliMock->expects($this->once())
                    ->method('set_charset')
                    ->with('utf8')
                    ->will($this->returnValue(true));

        $mysqliMock->expects($this->once())
                    ->method('close')
                    ->will($this->returnValue(true));

        $dataBaseManager = new DataBaseManager($mysqliMock);
        /*Se crea un objeto mysqli y se simula una conexión a la base de datos,
        luego se llama al método close() para cerrar la conexión
        Si devuelve true, significa que el cierre de la base de datos fue exitoso*/
        $this->assertEquals(true, $dataBaseManager->close());
    }
    
    /**Testcase 3-2 close Incorrect */
    public function testCloseIncorrect(){
        $mysqliMock = $this->createMock(mysqli::class);

        $mysqliMock->expects($this->once())
                    ->method('set_charset')
                    ->with('utf8')
                    ->will($this->returnValue(true));
    
        $mysqliMock->expects($this->once())
                    ->method('close')
                    ->will($this->returnValue(false));
    
        $dataBaseManager = new DataBaseManager($mysqliMock);
        /*Si devuelve false, significa que el cierre de la base de datos fue erróneo*/
        $this->assertEquals(false, $dataBaseManager->close());
    }
}