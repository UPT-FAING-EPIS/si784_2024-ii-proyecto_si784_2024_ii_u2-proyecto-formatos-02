<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Mockery as m;
use App\Models\CategoryModel;

class CategoryModelTest extends TestCase
{
    protected $mockConn;
    protected $categoryModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockConn = m::mock('PDO'); // Crear un mock para la conexión PDO
        $this->categoryModel = new CategoryModel(); // Instanciar el modelo CategoryModel
        $this->categoryModel->conn = $this->mockConn; // Asignar la conexión mockeada al modelo
    }

    /**
     * Verifica que se pueda crear una categoría exitosamente con datos válidos.
     */
    public function testCreateCategorySuccess()
    {
        $data = ['name' => 'New Category'];

        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('execute')
             ->once()
             ->with(['name' => 'New Category'])
             ->andReturn(true);

        $this->mockConn->shouldReceive('prepare')
             ->once()
             ->with("INSERT INTO categories (name) VALUES (:name)")
             ->andReturn($stmt);

        $result = $this->categoryModel->createCategory($data);

        $this->assertTrue($result);
    }

    /**
     * Verifica que falle la creación de una categoría si no se proporciona el campo 'name'.
     */
    public function testCreateCategoryFailsWithoutName()
    {
        $data = []; // Sin el campo 'name'
        $result = $this->categoryModel->createCategory($data);
        $this->assertFalse($result);
    }

    /**
     * Verifica que se puedan obtener todas las categorías exitosamente.
     */
    public function testGetCategoriesSuccess()
    {
        $expected = [
            ['id' => 1, 'name' => 'Category 1'],
            ['id' => 2, 'name' => 'Category 2'],
        ];

        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('execute')
             ->once()
             ->andReturn(true);
        $stmt->shouldReceive('fetchAll')
             ->once()
             ->with(\PDO::FETCH_ASSOC)
             ->andReturn($expected);

        $this->mockConn->shouldReceive('prepare')
             ->once()
             ->with("SELECT * FROM categories")
             ->andReturn($stmt);

        $result = $this->categoryModel->getCategories();

        $this->assertEquals($expected, $result);
    }

    /**
     * Verifica que se pueda obtener una categoría específica por su ID exitosamente.
     */
    public function testGetCategoryByIdSuccess()
    {
        $id = 1;
        $expected = ['id' => 1, 'name' => 'Category 1'];

        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('bindParam')
             ->once()
             ->with(':id', $id)
             ->andReturn(true);
        $stmt->shouldReceive('execute')
             ->once()
             ->andReturn(true);
        $stmt->shouldReceive('fetch')
             ->once()
             ->andReturn($expected);

        $this->mockConn->shouldReceive('prepare')
             ->once()
             ->with("SELECT * FROM categories WHERE id = :id")
             ->andReturn($stmt);

        $result = $this->categoryModel->getCategoryById($id);

        $this->assertEquals($expected, $result);
    }

    /**
     * Verifica que se pueda actualizar una categoría exitosamente con datos válidos.
     */
    public function testUpdateCategorySuccess()
    {
        $data = ['id' => 1, 'name' => 'Updated Category'];

        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('bindParam')
             ->with(':name', $data['name'])
             ->once()
             ->andReturn(true);
        $stmt->shouldReceive('bindParam')
             ->with(':id', $data['id'])
             ->once()
             ->andReturn(true);
        $stmt->shouldReceive('execute')
             ->once()
             ->andReturn(true);

        $this->mockConn->shouldReceive('prepare')
             ->once()
             ->with("UPDATE categories SET name = :name WHERE id = :id")
             ->andReturn($stmt);

        $result = $this->categoryModel->updateCategory($data);

        $this->assertTrue($result);
    }

    /**
     * Verifica que falle la actualización de una categoría si no se proporciona el campo 'id'.
     */
    public function testUpdateCategoryFailsWithoutId()
    {
        $data = ['name' => 'Missing ID'];
        $result = $this->categoryModel->updateCategory($data);
        $this->assertFalse($result);
    }

    /**
     * Verifica que se pueda eliminar una categoría exitosamente por su ID.
     */
    public function testDeleteCategorySuccess()
    {
        $id = 1;

        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('execute')
             ->once()
             ->with(['id' => $id])
             ->andReturn(true);

        $this->mockConn->shouldReceive('prepare')
             ->once()
             ->with("DELETE FROM categories WHERE id = :id")
             ->andReturn($stmt);

        $result = $this->categoryModel->deleteCategory($id);

        $this->assertTrue($result);
    }

    /**
     * Cierra los mocks después de cada prueba.
     */
    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}
