<?php
use PHPUnit\Framework\TestCase;
require_once('core\php\userManager.php');
require_once('core\php\DataBaseManager.php');

class userManagerTest extends TestCase {
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

    
    public function testSetUser() {
        
        $name = "alex";
        $type = "1";
        $password = "1234";
        $query = "INSERT INTO usuario (nombre, clave, tipo) VALUES('$name','$password','$type')";
        $dbManagerMock = $this->getDataBaseManagerInsertMock($query);
        $usuarioManager = new userManager($dbManagerMock);
        $result = $usuarioManager->setUser($name, $password, $type);
        $this->assertEquals("", $result);
    }

    public function testSetUserWhenIsIncorrect() {
        $name = "alex";
        $type = "tipo2";//tipo debe ser tipo int
        $password = "1234";
        $query = "INSERT INTO usuario (nombre, clave, tipo) VALUES('$name','$password','$type')";
        $dbManagerMock = $this->getDataBaseManagerInsertMockIncorrect2($query);
        $userManager = new userManager($dbManagerMock);
        $result = $userManager->setUser($name, $password, $type);

        $this->assertEquals(false, $result);
    }

    public function testGetUser() {
        $expectedResult = array(
            array(
                'id' => '1',
                'nombre' => 'Ale',
                'tipo' => '0',
                'clave' => '123'
            )
        );

        $name = 'Ale';
        $password = '123';
        $query = "SELECT * FROM usuario WHERE nombre='$name' AND clave='$password'";;
        $dbManagerMock = $this->getDataBaseManagerRealizeMock($expectedResult, $query);
        $userManager = new userManager($dbManagerMock);
        $result = $userManager->getUser($name, $password);
        $this->assertEquals(json_encode($expectedResult), $result);

    }

    public function testGetUserNotExist() {
        $expectedResult = array(
        );
        $name = 'Ale';
        $password = '123';
        $query = "SELECT * FROM usuario WHERE nombre='$name' AND clave='$password'";
        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMockIncorrect($expectedResult, $query);
        $userManager = new userManager($dataBaseManagerMock);
        $result = $userManager ->getUser($name, $password);

        $this->assertEquals("Tabla usuario vacia11", $result);
    }

    public function testUpdateUser() {

        $id = '1';
        $name = 'ale';
        $password = '123';
        $type = '0';
        $query = "UPDATE usuario set nombre = '$name' , clave = '$password' , tipo = '$type' WHERE id=".intval($id);
        $dataBaseManagerMock = $this->getDataBaseManagerInsertMock($query);
        $userManager = new userManager($dataBaseManagerMock);
        $result = $userManager->updateUser($id,$name, $password, $type);
        $this->assertEquals("", $result);

    }

    public function testUpdateUserWhenIsIncorrect() {
        $id = '1';
        $name = null;
        $password = '123';
        $type = '0';
        $query = "UPDATE usuario set nombre = '$name' , clave = '$password' , tipo = '$type' WHERE id=".intval($id);
        $dataBaseManagerMock = $this->getDataBaseManagerInsertMockIncorrect2($query);
        $userManager =  new userManager($dataBaseManagerMock);
        $result = $userManager->updateUser($id,$name, $password, $type);
        $this->assertEquals(false, $result);
    }

    public function testGetUserById() {
        $expectedResult = array(
            array(
                'id' => '1',
                'nombre' => 'Ale',
                'tipo' => '0',
                'clave' => '123'
            )
        );
        $id = '1';
        $query = "SELECT * FROM usuario WHERE id='$id' ";
        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMock($expectedResult, $query);
        $userManager = new userManager($dataBaseManagerMock);
        $result = $userManager->getUserById($id);
        $this->assertEquals(json_encode($expectedResult), $result);

    }

    public function testGetUserByIdWhenUserNotExist() {

        $expectedResult = array(
        );
        $id = '1';
        $query = "SELECT * FROM usuario WHERE id='$id' ";
        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMockIncorrect($expectedResult, $query);
        $userManager = new userManager($dataBaseManagerMock);
        $result = $userManager ->getUserById($id);

        $this->assertEquals("Tabla usuario vacia", $result);
    }

    public function testDeleteUser() {
        $id = '1';

        $query = "DELETE FROM usuario WHERE id = $id";
        $dataBaseManagerMock = $this->getDataBaseManagerInsertMock($query);
        $userManager = new userManager($dataBaseManagerMock);
        $result = $userManager->deleteUser($id);
        $this->assertEquals("", $result);

    }

    public function testDeleteUserWhenIdIsIncorrect() {
        $id = null;
        $query = "DELETE FROM usuario WHERE id = $id";

        $dataBaseManagerMock = $this->getDataBaseManagerInsertMockIncorrect2($query);
        $userManager = new userManager($dataBaseManagerMock);
        $result = $userManager->deleteUser($id);
        $this->assertEquals(false, $result);
    }

    public function testGetAllUsers() {
        $expectedResult = array(
            array(
                'id' => '1',
                'nombre' => 'Ale',
                'tipo' => '0',
                'clave' => '123'
            )
        );
        $finalResult = array(array(
            array(
                'id' => '1',
                'name' => 'Ale',
                'type' => '0',
                'password' => '123'
            )
        ));
        $query = "SELECT * FROM usuario";
        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMock($expectedResult, $query);
        $userManager = new userManager($dataBaseManagerMock);
        $result = $userManager->getAllUsers();
        $this->assertEquals(json_encode($finalResult), $result);   
    }

    public function testGetAllUsersWhenIsEmpty() {
        $query = "SELECT * FROM usuario";
        $expectedResult = array();
        $dataBaseManagerMock = $this->getDataBaseManagerRealizeMock($expectedResult, $query);
        $userManager = new userManager($dataBaseManagerMock);
        $result = $userManager->getAllUsers();
        $this->assertEquals("Tabla usuario vacia", $result);
    }
}
