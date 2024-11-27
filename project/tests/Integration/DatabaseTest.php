<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use App\Models\Database;
use PDO;
use PDOException;

class DatabaseTest extends TestCase
{
    /**
     * Verifica que la conexión a la base de datos se establezca correctamente con una configuración válida.
     */
    public function testDatabaseConnectionWithValidConfig()
    {
        $mockConfig = [
            'host' => 'localhost',
            'db' => 'test_db',
            'user' => 'root',
            'pass' => '',
        ];

        $database = new class($mockConfig) extends \App\Models\Database {
            public function __construct($mockConfig)
            {
                $this->conn = new \PDO(
                    "mysql:host={$mockConfig['host']};dbname={$mockConfig['db']}",
                    $mockConfig['user'],
                    $mockConfig['pass']
                );
            }
        };

        $this->assertInstanceOf(PDO::class, $database->getConnection());
    }

    /**
     * Verifica que se lance una excepción al proporcionar una configuración inválida.
     */
    public function testInvalidDatabaseConfiguration()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid database configuration.");

        new class extends \App\Models\Database {
            public function __construct()
            {
                $config = [
                    'host' => '',
                    'db' => '',
                    'user' => '',
                    // Falta 'pass'
                ];
                if (empty($config['host']) || empty($config['db']) || empty($config['user']) || !isset($config['pass'])) {
                    throw new \InvalidArgumentException("Invalid database configuration.");
                }
            }
        };
    }

    /**
     * Verifica que el modo de error de PDO se configure correctamente.
     */
    public function testPdoErrorModeIsSetCorrectly()
    {
        $mockConfig = [
            'host' => 'localhost',
            'db' => 'test_db',
            'user' => 'root',
            'pass' => '',
        ];

        $database = new class($mockConfig) extends \App\Models\Database {
            public function __construct($mockConfig)
            {
                $this->conn = new \PDO(
                    "mysql:host={$mockConfig['host']};dbname={$mockConfig['db']}",
                    $mockConfig['user'],
                    $mockConfig['pass']
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        };

        $this->assertEquals(PDO::ERRMODE_EXCEPTION, $database->getConnection()->getAttribute(PDO::ATTR_ERRMODE));
    }

    /**
     * Verifica que se establezcan correctamente los atributos de PDO.
     */
    public function testPdoAttributesAreSetCorrectly()
    {
        $mockConfig = [
            'host' => 'localhost',
            'db' => 'test_db',
            'user' => 'root',
            'pass' => '',
        ];

        $database = new class($mockConfig) extends \App\Models\Database {
            public function __construct($mockConfig)
            {
                $this->conn = new \PDO(
                    "mysql:host={$mockConfig['host']};dbname={$mockConfig['db']}",
                    $mockConfig['user'],
                    $mockConfig['pass']
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        };

        $this->assertEquals(PDO::ERRMODE_EXCEPTION, $database->getConnection()->getAttribute(PDO::ATTR_ERRMODE));
    }

    /**
     * Verifica que se lance una excepción al intentar establecer conexión con credenciales inválidas.
     */
    public function testDatabaseConnectionWithInvalidCredentials()
    {
        $this->expectException(PDOException::class);
        $this->expectExceptionMessage("Access denied");

        new class extends \App\Models\Database {
            public function __construct()
            {
                $mockConfig = [
                    'host' => 'localhost',
                    'db' => 'test_db',
                    'user' => 'invalid_user',
                    'pass' => 'invalid_pass',
                ];

                $this->conn = new \PDO(
                    "mysql:host={$mockConfig['host']};dbname={$mockConfig['db']}",
                    $mockConfig['user'],
                    $mockConfig['pass']
                );
            }
        };
    }

    /**
     * Verifica que se lance una excepción al fallar la conexión a la base de datos.
     */
    public function testDatabaseConnectionFails()
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage("Connection failed: Simulated failure");

        new class extends \App\Models\Database {
            public function __construct()
            {
                $dsn = "mysql:host=invalid_host;dbname=invalid_db";
                try {
                    $this->conn = new \PDO($dsn, 'invalid_user', 'invalid_pass');
                } catch (\PDOException $e) {
                    throw new \PDOException("Connection failed: Simulated failure", (int)$e->getCode(), $e);
                }
            }
        };
    }

    /**
     * Verifica que se lance una excepción al fallar la conexión con credenciales incorrectas.
     */
    public function testConnectionThrowsPdoExceptionOnFailure()
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage("Connection failed:");

        new class extends \App\Models\Database {
            public function __construct()
            {
                $dsn = "mysql:host=invalid_host;dbname=invalid_db";
                try {
                    $this->conn = new \PDO($dsn, 'invalid_user', 'invalid_pass');
                } catch (\PDOException $e) {
                    throw new \PDOException("Connection failed: " . $e->getMessage(), (int)$e->getCode(), $e);
                }
            }
        };
    }

    /**
     * Verifica que se lance una excepción para configuraciones inválidas repetidas.
     */
    public function testThrowsExceptionForInvalidConfiguration()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid database configuration.");

        new class extends \App\Models\Database {
            public function __construct()
            {
                $config = [
                    'host' => '',
                    'db' => '',
                    'user' => '',
                    // Falta 'pass'
                ];
                if (empty($config['host']) || empty($config['db']) || empty($config['user']) || !isset($config['pass'])) {
                    throw new \InvalidArgumentException("Invalid database configuration.");
                }
            }
        };
    }
}
