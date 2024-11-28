using Microsoft.Playwright;
using Microsoft.Playwright.MSTest;
using System.Text.RegularExpressions;

namespace AppSiteTests;

[TestClass]
public class AppSiteTests : PageTest
{
    [TestInitialize]
    public async Task TestInitialize()
    {
        await Context.Tracing.StartAsync(new()
        {
            Title = $"{TestContext.FullyQualifiedTestClassName}.{TestContext.TestName}",
            Screenshots = true,
            Snapshots = true,
            Sources = true
        });
        await Context.Browser.NewContextAsync(new()
        {
             RecordVideoDir = "videos/",
             RecordVideoSize = new RecordVideoSize() { Width = 640, Height = 480 }
         });
    }

    [TestCleanup]
    public async Task TestCleanup()
    {
        await Context.Tracing.StopAsync(new()
        {
            Path = Path.Combine(
                Environment.CurrentDirectory,
                "playwright-traces",
                $"{TestContext.FullyQualifiedTestClassName}.{TestContext.TestName}.zip"
            )
        });
        await Context.CloseAsync();
    }

    [TestMethod]
    public async Task HasTitle()
    {
        await Page.GotoAsync("http://localhost:8000/login");

        // Expect a title "to contain" a substring.
        await Expect(Page).ToHaveTitleAsync(new Regex("Login"));
    }

    [TestMethod]
    public async Task RegisterTest()
    {
        // Arrange
        string userName = "dylan";  // Nombre completo de prueba
        string userEmail = "dylan@gmail.com";  // Correo electrónico único de prueba
        string userPassword = "12345";  // Contraseña de prueba

        // Navegar a la página de login
        await Page.GotoAsync("http://localhost:8000/login");  // Cambia la URL según corresponda

        // Act 1: Hacer clic en el enlace de "Registrarse" y registrar al usuario
        await Page.ClickAsync("a[href='/register']");
        await Page.WaitForURLAsync("**/register");

        // Completar los campos del formulario de registro
        await Page.FillAsync("input[name='name']", userName);
        await Page.FillAsync("input[name='email']", userEmail);
        await Page.FillAsync("input[name='password']", userPassword);

        // Hacer clic en el botón de "Registrar"
        await Page.ClickAsync("button[type='submit']");

        // Esperar a que la página se redirija después del registro (ej. al login o dashboard)
        await Page.WaitForURLAsync("**/login");

        // Act 2: Ahora iniciar sesión con el usuario registrado
        await Page.FillAsync("input[name='email']", userEmail);
        await Page.FillAsync("input[name='password']", userPassword);
        await Page.ClickAsync("button[type='submit']");

        // Esperar a que la página se redirija al dashboard después del inicio de sesión
        await Page.WaitForURLAsync("**/dashboard");

        // Assert: Verificar que el mensaje de bienvenida esté visible en el dashboard
        await Expect(Page.Locator("body")).ToContainTextAsync("Bienvenido a tu Dashboard");
    }


    [TestMethod]
    public async Task LoginTest()
    {
        // Arrange
        string userEmail = "dylan@gmail.com";  // Correo electrónico de prueba
        string userPassword = "12345";  // Contraseña de prueba

        // Navegar a la página de login
        await Page.GotoAsync("http://localhost:8000/login");  // Cambia la URL según corresponda

        // Act
        // Completar el campo de correo electrónico
        await Page.FillAsync("input[name='email']", userEmail);

        // Completar el campo de contraseña
        await Page.FillAsync("input[name='password']", userPassword);

        // Hacer clic en el botón de "Iniciar sesión"
        await Page.ClickAsync("button[type='submit']");

        // Esperar que la página redirija después del inicio de sesión
        await Page.WaitForURLAsync("**/dashboard");  // Espera que la URL contenga "/dashboard" o usa otro criterio relevante

        // Assert
        // Verificar si el mensaje de bienvenida está visible, indicando que el inicio de sesión fue exitoso
        await Expect(Page.Locator("body")).ToContainTextAsync("Bienvenido a tu Dashboard");
    }

    [TestMethod]
    public async Task LogoutTest()
    {
        // Arrange
        string userEmail = "dylan@gmail.com";  // Correo electrónico de prueba
        string userPassword = "12345";  // Contraseña de prueba

        // Navegar a la página de login
        await Page.GotoAsync("http://localhost:8000/login");  // Cambia la URL según corresponda

        // Act
        // Completar el campo de correo electrónico
        await Page.FillAsync("input[name='email']", userEmail);

        // Completar el campo de contraseña
        await Page.FillAsync("input[name='password']", userPassword);

        // Hacer clic en el botón de "Iniciar sesión"
        await Page.ClickAsync("button[type='submit']");

        // Esperar que la página redirija después del inicio de sesión
        await Page.WaitForURLAsync("**/dashboard");  // Espera que la URL contenga "/dashboard" o usa otro criterio relevante

        // Act - Cerrar sesión
        // Hacer clic en el enlace de "Cerrar sesión"
        await Page.ClickAsync("button.btn-logout");  // Usa el boton correcto para el enlace de cerrar sesión

        // Esperar que la página redirija a la pantalla de inicio de sesión
        await Page.WaitForURLAsync("**/login");  // Verifica que la URL contenga "/login" o ajusta según corresponda

        // Assert - Verificar que la página ha redirigido correctamente después de cerrar sesión
        await Expect(Page.Locator("body")).ToContainTextAsync("Login");
    }


    [TestMethod]
    public async Task LoginTest_Error_NoRedirection()
    {
        // Arrange
        string userEmail = "wrongemail@gmail.com";  // Correo electrónico incorrecto
        string userPassword = "wrongpassword";  // Contraseña incorrecta
        string loginPageUrl = "http://localhost:8000/login";  // URL de la página de login

        // Navegar a la página de login
        await Page.GotoAsync(loginPageUrl);  // Cambia la URL según corresponda

        // Act
        // Completar el campo de correo electrónico
        await Page.FillAsync("input[name='email']", userEmail);

        // Completar el campo de contraseña
        await Page.FillAsync("input[name='password']", userPassword);

        // Guardar la URL actual antes de hacer clic en "Iniciar sesión"
        string initialUrl = Page.Url;

        // Hacer clic en el botón de "Iniciar sesión"
        await Page.ClickAsync("button[type='submit']");

        // Esperar un momento para que la página responda (sin redirección exitosa)
        await Page.WaitForTimeoutAsync(2000);  // Espera de 2 segundos (ajusta si es necesario)

        // Assert
        // Verificar que la URL no haya cambiado, lo que indica un error en el inicio de sesión
        string currentUrl = Page.Url;
        
        // Comparar las URLs directamente como cadenas
        Assert.AreEqual(initialUrl, currentUrl);  // Asegúrate de que la URL siga siendo la misma
    }

    [TestMethod]
    public async Task CreateTaskTest()
    {
        // Arrange
        string userEmail = "dylan@gmail.com";  // Correo electrónico de prueba
        string userPassword = "12345";  // Contraseña de prueba
        string taskTitle = "Nueva Tarea";  // Título de la tarea
        string taskDescription = "Descripción de la tarea";  // Descripción de la tarea
        string taskDueDate = "2024-12-31";  // Fecha de vencimiento de la tarea (formato YYYY-MM-DD)

        // Navegar a la página de login
        await Page.GotoAsync("http://localhost:8000/login");  // Cambia la URL según corresponda

        // Act - Iniciar sesión
        // Completar el campo de correo electrónico
        await Page.FillAsync("input[name='email']", userEmail);

        // Completar el campo de contraseña
        await Page.FillAsync("input[name='password']", userPassword);

        // Hacer clic en el botón de "Iniciar sesión"
        await Page.ClickAsync("button[type='submit']");

        // Esperar que la página redirija después del inicio de sesión
        await Page.WaitForURLAsync("**/dashboard");  // Espera que la URL contenga "/dashboard"

        // Assert - Verificar que el inicio de sesión fue exitoso
        await Expect(Page.Locator("body")).ToContainTextAsync("Bienvenido a tu Dashboard");

        // Act - Hacer clic en el botón para crear una tarea
        var createTaskButton = Page.Locator("button.btn-create-task");
 
        await createTaskButton.ClickAsync();  // Realiza el clic en el botón de crear tarea

        // Esperar que la página de crear tarea se cargue
        await Page.WaitForURLAsync("**/task/create");  // Espera que la URL contenga "/task/create"

        // Assert - Verificar que la página de creación de tarea esté visible
        await Expect(Page.Locator("h1")).ToContainTextAsync("Crear Nueva Tarea");

        // Act - Rellenar el formulario de creación de tarea
        await Page.FillAsync("input[name='title']", taskTitle);  // Rellenar el título
        await Page.FillAsync("textarea[name='description']", taskDescription);  // Rellenar la descripción
        await Page.FillAsync("input[name='due_date']", taskDueDate);  // Rellenar la fecha de vencimiento

        // Hacer clic en el botón de "Crear Tarea"
        await Page.ClickAsync("button[type='submit']");

        // Esperar que la página redirija después de crear la tarea (esto dependerá de cómo maneja tu aplicación la redirección)
        await Page.WaitForURLAsync("**/dashboard");  // Espera que la URL contenga "/dashboard" (o la URL de redirección esperada)

        // Assert - Verificar que la tarea se haya creado correctamente (puedes verificar la presencia de la tarea en el listado o cualquier mensaje de éxito)
        await Expect(Page.Locator("body")).ToContainTextAsync(taskTitle);  // Verifica que el título de la tarea esté en la página del dashboard
    }

    [TestMethod]
    public async Task EditTaskTest()
    {
        // Arrange
        string userEmail = "dylan@gmail.com";  // Correo electrónico de prueba
        string userPassword = "12345";  // Contraseña de prueba
        string newTaskTitle = "Tarea editada";  // Nuevo título de la tarea
        string newTaskDescription = "Descripción editada";  // Nueva descripción de la tarea

        // Iniciar sesión en la aplicación
        await Page.GotoAsync("http://localhost:8000/login");
        await Page.FillAsync("input[name='email']", userEmail);
        await Page.FillAsync("input[name='password']", userPassword);
        await Page.ClickAsync("button[type='submit']");
        await Page.WaitForURLAsync("**/dashboard");

        // Act
        // Buscar el enlace o texto de "Editar" (suponemos que solo hay una tarea y el enlace es visible)
        var editLinkLocator = Page.Locator("a:has-text('Editar')");  // Localiza el enlace o texto que contiene "Editar"
        await editLinkLocator.ClickAsync();  // Hacer clic en el enlace para editar la tarea

        // Esperar a que el formulario de edición se cargue
        await Page.WaitForSelectorAsync("form");

        // Cambiar el título y la descripción de la tarea
        await Page.FillAsync("input[name='title']", newTaskTitle);
        await Page.FillAsync("textarea[name='description']", newTaskDescription);

        // Enviar el formulario de edición
        await Page.ClickAsync("button[type='submit']");

        // Esperar que la tarea se guarde y se redirija al dashboard
        await Page.WaitForURLAsync("**/dashboard");

        // Assert
        // Verificar que el nuevo título de la tarea esté visible en el dashboard (listado de tareas)
        var updatedTaskLocator = Page.Locator($"text={newTaskTitle}");  // Localiza el texto con el nuevo título de la tarea
        await Expect(updatedTaskLocator).ToBeVisibleAsync();  // Verificar que la tarea editada esté visible en el listado
    }


    [TestMethod]
    public async Task DeleteTaskTest()
    {
        // Arrange
        string userEmail = "dylan@gmail.com";  // Correo electrónico de prueba
        string userPassword = "12345";  // Contraseña de prueba
        string taskTitle = "Tarea editada";  // Título de la tarea a eliminar

        // Iniciar sesión en la aplicación
        await Page.GotoAsync("http://localhost:8000/login");
        await Page.FillAsync("input[name='email']", userEmail);
        await Page.FillAsync("input[name='password']", userPassword);
        await Page.ClickAsync("button[type='submit']");
        await Page.WaitForURLAsync("**/dashboard");

        // Act: Hacer clic en el texto "Eliminar"
        var deleteButtonLocator = Page.Locator($"text='Eliminar'");
        await deleteButtonLocator.ClickAsync();  // Hacer clic en el enlace de eliminar

        // Esperar que la página se redirija o actualice después de la eliminación
        await Page.WaitForURLAsync("**/dashboard");

        // Verificar que la tarea ya no está visible en la lista
        var taskAfterDeletionLocator = Page.Locator($"text={taskTitle}");
        await Expect(taskAfterDeletionLocator).ToBeHiddenAsync();  // Verificar que la tarea ya no está visible
    }

    [TestMethod]
    public async Task CreateNotification()
    {
        // Arrange
        string userEmail = "dylan@gmail.com";  // Correo electrónico de prueba
        string userPassword = "12345";  // Contraseña de prueba
        string notificationMessage = "Nueva Tarea";  // Mensaje de la notificación

        // Navegar a la página de login
        await Page.GotoAsync("http://localhost:8000/login");  // Cambia la URL según corresponda

        // Act - Iniciar sesión
        // Completar el campo de correo electrónico
        await Page.FillAsync("input[name='email']", userEmail);

        // Completar el campo de contraseña
        await Page.FillAsync("input[name='password']", userPassword);

        // Hacer clic en el botón de "Iniciar sesión"
        await Page.ClickAsync("button[type='submit']");

        // Esperar que la página redirija después del inicio de sesión
        await Page.WaitForURLAsync("**/dashboard");  // Verifica que la URL contenga "/dashboard"

        // Assert - Verificar que el inicio de sesión fue exitoso
        await Expect(Page.Locator("body")).ToContainTextAsync("Bienvenido a tu Dashboard");

        await Page.ClickAsync("#show-notifications-section");
        await Page.ClickAsync("#show-notifications-section");

        await Page.WaitForSelectorAsync("textarea[name='message']");

        // Act - Rellenar el formulario de creación de notificación
        await Page.FillAsync("textarea[name='message']", notificationMessage);  // Rellenar el campo del mensaje

        // Act - Hacer clic en el botón para enviar la notificación
        await Page.ClickAsync("button.btn-create-notification");  // Botón para crear la notificación

        // Esperar la redirección o el mensaje de éxito (dependiendo del comportamiento esperado)
        await Page.WaitForURLAsync("**/dashboard");  // Ajusta si la URL esperada es diferente

        // Assert - Verificar que la notificación se haya creado correctamente
        await Expect(Page.Locator("body")).ToContainTextAsync(notificationMessage);  // Verifica que el mensaje esté visible en el dashboard
    }

    [TestMethod]
    public async Task MarkNotificationAsRead()
    {
        // Arrange
        string userEmail = "dylan@gmail.com";  // Correo electrónico de prueba
        string userPassword = "12345";  // Contraseña de prueba

        // Navegar a la página de login
        await Page.GotoAsync("http://localhost:8000/login");  // Cambia la URL según corresponda

        // Act - Iniciar sesión
        // Completar el campo de correo electrónico
        await Page.FillAsync("input[name='email']", userEmail);

        // Completar el campo de contraseña
        await Page.FillAsync("input[name='password']", userPassword);

        // Hacer clic en el botón de "Iniciar sesión"
        await Page.ClickAsync("button[type='submit']");

        // Esperar que la página redirija después del inicio de sesión
        await Page.WaitForURLAsync("**/dashboard");  // Verifica que la URL contenga "/dashboard"

        // Assert - Verificar que el inicio de sesión fue exitoso
        await Expect(Page.Locator("body")).ToContainTextAsync("Bienvenido a tu Dashboard");

        await Page.ClickAsync("#show-notifications-section");
        await Page.ClickAsync("#show-notifications-section");

        // Act - Buscar y marcar la notificación como leída
        var markAsReadLink = Page.Locator("a[href^='/notification/mark-read/']");  // Selector para el enlace de marcar como leída
        await markAsReadLink.ClickAsync();  // Realiza el clic en el enlace

        // Esperar la redirección o confirmación después de marcar como leída (ajusta según el comportamiento esperado)
        await Page.WaitForURLAsync("**/dashboard");  // Cambia si la URL final esperada es diferente

        // Assert - Verificar que la notificación se marcó como leída
        // Dependiendo de tu aplicación, podrías verificar un mensaje de éxito o la ausencia del enlace de marcar como leída
        await Expect(Page.Locator("body")).Not.ToContainTextAsync("Marcar como leída");  // Verifica que el enlace ya no esté presente
    }

    [TestMethod]
    public async Task DeleteNotification()
    {
        // Arrange
        string userEmail = "dylan@gmail.com";  // Correo electrónico de prueba
        string userPassword = "12345";  // Contraseña de prueba

        // Navegar a la página de login
        await Page.GotoAsync("http://localhost:8000/login");  // Cambia la URL según corresponda

        // Act - Iniciar sesión
        // Completar el campo de correo electrónico
        await Page.FillAsync("input[name='email']", userEmail);

        // Completar el campo de contraseña
        await Page.FillAsync("input[name='password']", userPassword);

        // Hacer clic en el botón de "Iniciar sesión"
        await Page.ClickAsync("button[type='submit']");

        // Esperar que la página redirija después del inicio de sesión
        await Page.WaitForURLAsync("**/dashboard");  // Verifica que la URL contenga "/dashboard"

        // Assert - Verificar que el inicio de sesión fue exitoso
        await Expect(Page.Locator("body")).ToContainTextAsync("Bienvenido a tu Dashboard");

        await Page.ClickAsync("#show-notifications-section");
        await Page.ClickAsync("#show-notifications-section");

        // Act - Buscar y eliminar la notificación
        var deleteLink = Page.Locator("a[href^='/notification/delete/']");  // Selector para el enlace de eliminar notificación
        await deleteLink.ClickAsync();  // Realiza el clic en el enlace

        // Esperar la redirección o confirmación después de eliminar (ajusta según el comportamiento esperado)
        await Page.WaitForURLAsync("**/dashboard");  // Cambia si la URL final esperada es diferente

        // Assert - Verificar que la notificación fue eliminada
        // Dependiendo de tu aplicación, podrías verificar un mensaje de éxito o la ausencia de la notificación
        await Expect(Page.Locator("body")).Not.ToContainTextAsync("Eliminar");  // Verifica que el enlace de eliminar ya no está presente
    }
}